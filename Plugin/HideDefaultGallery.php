<?php
declare(strict_types=1);

namespace Panth\ProductGallery\Plugin;

use Magento\Catalog\Block\Product\View\Gallery as DefaultGallery;
use Magento\Catalog\Helper\Image as ImageHelper;
use Panth\Core\Helper\Theme;
use Panth\ProductGallery\Helper\Data as ConfigHelper;
use Panth\ProductGallery\ViewModel\Config as ConfigViewModel;

class HideDefaultGallery
{
    private ConfigHelper $configHelper;

    private Theme $themeHelper;

    private ConfigViewModel $configViewModel;

    private ImageHelper $imageHelper;

    private bool $rendering = false;

    private array $replacedProductIds = [];

    public function __construct(
        ConfigHelper $configHelper,
        Theme $themeHelper,
        ConfigViewModel $configViewModel,
        ImageHelper $imageHelper
    ) {
        $this->configHelper = $configHelper;
        $this->themeHelper = $themeHelper;
        $this->configViewModel = $configViewModel;
        $this->imageHelper = $imageHelper;
    }

    public function afterToHtml(DefaultGallery $subject, string $result): string
    {
        if (!$this->configHelper->isEnabled() || $this->rendering) {
            return $result;
        }

        $blockName = $subject->getNameInLayout();
        if ($blockName === 'product.info.media.video') {
            $product = $subject->getProduct();
            if ($product && isset($this->replacedProductIds[(int) $product->getId()])) {
                return '';
            }
            return $result;
        }
        if ($blockName !== 'product.media' && $blockName !== 'product.info.media.image') {
            return $result;
        }

        $product = $subject->getProduct();
        if (!$product || !$product->getId()) {
            return $result;
        }

        $this->rendering = true;
        try {
            $images = $this->buildImages($subject, $product);
            if (empty($images)) {
                return $this->fillEmptyPlaceholder($result);
            }

            $template = $this->themeHelper->isHyva()
                ? 'Panth_ProductGallery::hyva/gallery.phtml'
                : 'Panth_ProductGallery::gallery.phtml';

            $subject->setData('panth_gallery_images', $images);
            $subject->setData('panth_gallery_config', $this->configViewModel->getGalleryConfig());
            $subject->setData('panth_gallery_viewmodel', $this->configViewModel);

            $originalTemplate = $subject->getTemplate();
            $subject->setTemplate($template);
            try {
                $html = $subject->toHtml();
            } finally {
                $subject->setTemplate($originalTemplate);
            }

            if (!empty($html)) {
                $this->replacedProductIds[(int) $product->getId()] = true;
                return $html;
            }
        } catch (\Exception $e) {
        } finally {
            $this->rendering = false;
        }

        return $result;
    }

    private function fillEmptyPlaceholder(string $html): string
    {
        if (strpos($html, 'gallery-placeholder__image') === false) {
            return $html;
        }
        $url = (string) $this->imageHelper->getDefaultPlaceholderUrl('image');
        if ($url === '') {
            return $html;
        }
        $escaped = htmlspecialchars($url, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $filled = preg_replace_callback(
            '/<img\b[^>]*gallery-placeholder__image[^>]*>/i',
            static function (array $match) use ($escaped): string {
                return preg_replace('/(\s)src=(["\'])\2/i', '$1src="' . $escaped . '"', $match[0], 1) ?? $match[0];
            },
            $html
        );
        if ($filled === null) {
            return $html;
        }

        return preg_replace(
            '/(<link\b[^>]*\s)href=(["\'])\2/i',
            '$1href="' . $escaped . '"',
            $filled,
            1
        ) ?? $filled;
    }

    private function buildImages(DefaultGallery $subject, $product): array
    {
        $images = [];
        $mediaGallery = $product->getMediaGalleryImages();
        if (!$mediaGallery) {
            return $images;
        }

        $thumbW = $this->configHelper->getThumbWidth();
        $thumbH = $this->configHelper->getThumbHeight();
        $mainW = $this->configHelper->getMainImageWidth();
        $mainH = $this->configHelper->getMainImageHeight();

        $coreItems = $this->getCoreGalleryItems($subject, count($mediaGallery));
        $productName = (string) $product->getName();

        $index = 0;
        foreach ($mediaGallery as $image) {
            $coreItem = $coreItems[$index] ?? [];
            $index++;
            if ($image->getDisabled()) {
                continue;
            }
            $alt = (string) ($coreItem['caption'] ?? '');
            if ($alt === '') {
                $rawLabel = (string) $image->getLabel();
                $alt = $rawLabel !== '' ? $rawLabel : $productName;
            }
            $title = (string) ($coreItem['title'] ?? '');
            $images[] = [
                'thumb' => $this->imageHelper->init($product, 'product_page_image_small')
                    ->setImageFile($image->getFile())
                    ->resize($thumbW, $thumbH)
                    ->getUrl(),
                'medium' => $this->imageHelper->init($product, 'product_page_image_medium')
                    ->setImageFile($image->getFile())
                    ->resize($mainW, $mainH)
                    ->getUrl(),
                'large' => $this->imageHelper->init($product, 'product_page_image_large')
                    ->setImageFile($image->getFile())
                    ->getUrl(),
                'alt' => $alt,
                'title' => $title !== '' ? $title : $alt,
                'position' => (int) $image->getPosition(),
                'is_main' => $image->getFile() === $product->getImage(),
            ];
        }

        usort($images, fn($a, $b) => $a['position'] <=> $b['position']);

        return $images;
    }

    private function getCoreGalleryItems(DefaultGallery $subject, int $expected): array
    {
        try {
            $items = json_decode((string) $subject->getGalleryImagesJson(), true);
        } catch (\Throwable $e) {
            return [];
        }
        if (!is_array($items) || count($items) !== $expected) {
            return [];
        }
        return array_values($items);
    }
}

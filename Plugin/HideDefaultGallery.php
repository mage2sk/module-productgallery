<?php
declare(strict_types=1);

namespace Panth\ProductGallery\Plugin;

use Magento\Catalog\Block\Product\View\Gallery as DefaultGallery;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\App\ObjectManager;
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

    private ?object $imageTemplateResolver = null;

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
        $this->initImageTemplateResolver();
    }

    private function initImageTemplateResolver(): void
    {
        $resolverClass = 'Panth\AdvancedSEO\Model\ImageSeo\ImageTemplateResolver';
        if (class_exists($resolverClass)) {
            try {
                $this->imageTemplateResolver = ObjectManager::getInstance()->get($resolverClass);
            } catch (\Throwable $e) {
                $this->imageTemplateResolver = null;
            }
        }
    }

    public function afterToHtml(DefaultGallery $subject, string $result): string
    {
        if (!$this->configHelper->isEnabled() || $this->rendering) {
            return $result;
        }

        $blockName = $subject->getNameInLayout();
        if ($blockName !== 'product.media' && $blockName !== 'product.info.media.image') {
            return $result;
        }

        $product = $subject->getProduct();
        if (!$product || !$product->getId()) {
            return $result;
        }

        $this->rendering = true;
        try {
            $images = $this->buildImages($product);
            if (empty($images)) {
                return $result;
            }

            $template = $this->themeHelper->isHyva()
                ? 'Panth_ProductGallery::hyva/gallery.phtml'
                : 'Panth_ProductGallery::gallery.phtml';

            $subject->setData('panth_gallery_images', $images);
            $subject->setData('panth_gallery_config', $this->configViewModel->getGalleryConfig());
            $subject->setData('panth_gallery_viewmodel', $this->configViewModel);

            $originalTemplate = $subject->getTemplate();
            $subject->setTemplate($template);
            $html = $subject->toHtml();
            $subject->setTemplate($originalTemplate);

            if (!empty($html)) {
                return $html;
            }
        } catch (\Exception $e) {
        } finally {
            $this->rendering = false;
        }

        return $result;
    }

    private function buildImages($product): array
    {
        $images = [];
        $mediaGallery = $product->getMediaGalleryImages();
        if (!$mediaGallery) {
            return $images;
        }

        $thumbW = 72;
        $thumbH = 72;
        $mainW = 700;
        $mainH = 700;

        [$seoAlt, $seoTitle] = $this->resolveSeoAltTitle($product);
        $productName = (string) $product->getName();

        foreach ($mediaGallery as $image) {
            if ($image->getDisabled()) {
                continue;
            }
            $rawLabel = (string) $image->getLabel();
            $alt = $rawLabel !== '' ? $rawLabel : $productName;
            if ($seoAlt !== ''
                && ($rawLabel === ''
                    || $rawLabel === $productName
                    || strcasecmp($rawLabel, 'Image') === 0)
            ) {
                $alt = $seoAlt;
            }
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
                'title' => $seoTitle !== '' ? $seoTitle : $alt,
                'position' => (int) $image->getPosition(),
            ];
        }

        usort($images, fn($a, $b) => $a['position'] <=> $b['position']);

        if ($seoAlt !== '' && count($images) > 1) {
            $total = count($images);
            foreach ($images as $i => &$img) {
                if (($img['alt'] ?? '') === $seoAlt) {
                    $img['alt'] = $seoAlt . ' - Image ' . ($i + 1) . ' of ' . $total;
                }
            }
            unset($img);
        }

        return $images;
    }

    private function resolveSeoAltTitle($product): array
    {
        if ($this->imageTemplateResolver === null) {
            return ['', ''];
        }
        try {
            if (method_exists($this->imageTemplateResolver, 'isGalleryEnabled')
                && !$this->imageTemplateResolver->isGalleryEnabled()
            ) {
                return ['', ''];
            }
            if (!method_exists($this->imageTemplateResolver, 'resolve')) {
                return ['', ''];
            }
            $resolved = $this->imageTemplateResolver->resolve($product);
            return [
                (string) ($resolved['alt'] ?? ''),
                (string) ($resolved['title'] ?? ''),
            ];
        } catch (\Throwable $e) {
            return ['', ''];
        }
    }
}

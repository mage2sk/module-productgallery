<?php
declare(strict_types=1);

namespace Panth\ProductGallery\Block;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\View\Gallery as CoreGallery;
use Magento\Catalog\Helper\Image as ImageHelper;
use Panth\Core\Helper\Theme;
use Panth\ProductGallery\Helper\Data as ConfigHelper;
use Panth\ProductGallery\ViewModel\Config as ConfigViewModel;

class Gallery extends AbstractProduct
{
    private ConfigHelper $configHelper;

    private Theme $themeHelper;

    private ImageHelper $imageHelper;

    public function __construct(
        Context $context,
        ConfigHelper $configHelper,
        Theme $themeHelper,
        ImageHelper $imageHelper,
        array $data = []
    ) {
        $this->configHelper = $configHelper;
        $this->themeHelper = $themeHelper;
        $this->imageHelper = $imageHelper;
        parent::__construct($context, $data);
    }

    public function getTemplate()
    {
        if ($this->themeHelper->isHyva()) {
            return 'Panth_ProductGallery::hyva/gallery.phtml';
        }
        return 'Panth_ProductGallery::gallery.phtml';
    }

    public function isEnabled(): bool
    {
        return $this->configHelper->isEnabled();
    }

    public function getCurrentProduct()
    {
        return $this->getProduct();
    }

    public function getGalleryImages(): array
    {
        $product = $this->getCurrentProduct();
        if (!$product) {
            return [];
        }

        $images = [];
        $mediaGallery = $product->getMediaGalleryImages();

        if ($mediaGallery) {
            $mainWidth = $this->configHelper->getMainImageWidth();
            $mainHeight = $this->configHelper->getMainImageHeight();
            $thumbWidth = $this->configHelper->getThumbWidth();
            $thumbHeight = $this->configHelper->getThumbHeight();

            $coreItems = $this->getCoreGalleryItems($product, count($mediaGallery));
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
                        ->resize($thumbWidth, $thumbHeight)
                        ->getUrl(),
                    'medium' => $this->imageHelper->init($product, 'product_page_image_medium')
                        ->setImageFile($image->getFile())
                        ->resize($mainWidth, $mainHeight)
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

            usort($images, function ($a, $b) {
                return $a['position'] <=> $b['position'];
            });
        }

        return $images;
    }

    private function getCoreGalleryItems($product, int $expected): array
    {
        try {
            $coreBlock = $this->getLayout()->createBlock(
                CoreGallery::class,
                '',
                ['data' => ['product' => $product]]
            );
            $items = json_decode((string) $coreBlock->getGalleryImagesJson(), true);
        } catch (\Throwable $e) {
            return [];
        }
        if (!is_array($items) || count($items) !== $expected) {
            return [];
        }
        return array_values($items);
    }

    public function getGalleryConfig(): array
    {
        return $this->configHelper->getGalleryConfig();
    }

    public function getGalleryConfigJson(): string
    {
        return (string) json_encode(
            $this->configHelper->getGalleryConfig(),
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
        );
    }

    protected function _toHtml(): string
    {
        $product = $this->getCurrentProduct();
        if (!$product) {
            return '';
        }

        if (!$this->getData('panth_gallery_viewmodel') && !$this->getData('view_model')) {
            $this->setData('panth_gallery_viewmodel', new ConfigViewModel($this->configHelper));
        }

        return parent::_toHtml();
    }
}

<?php
declare(strict_types=1);

namespace Panth\ProductGallery\Block;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\App\ObjectManager;
use Panth\Core\Helper\Theme;
use Panth\ProductGallery\Helper\Data as ConfigHelper;

class Gallery extends AbstractProduct
{
    private ConfigHelper $configHelper;

    private Theme $themeHelper;

    private ImageHelper $imageHelper;

    private ?object $imageTemplateResolver = null;

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
                    'title' => $seoTitle !== '' ? $seoTitle : $alt,
                    'position' => (int) $image->getPosition(),
                    'is_main' => $image->getFile() === $product->getImage(),
                ];
            }

            usort($images, function ($a, $b) {
                return $a['position'] <=> $b['position'];
            });

            if ($seoAlt !== '' && count($images) > 1) {
                $total = count($images);
                foreach ($images as $i => &$img) {
                    if (($img['alt'] ?? '') === $seoAlt) {
                        $img['alt'] = $seoAlt . ' - Image ' . ($i + 1) . ' of ' . $total;
                    }
                }
                unset($img);
            }
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

    public function getGalleryConfig(): array
    {
        return $this->configHelper->getGalleryConfig();
    }

    public function getGalleryConfigJson(): string
    {
        return (string) json_encode($this->configHelper->getGalleryConfig());
    }

    protected function _toHtml(): string
    {
        $product = $this->getCurrentProduct();
        if (!$product) {
            return '';
        }

        return parent::_toHtml();
    }
}

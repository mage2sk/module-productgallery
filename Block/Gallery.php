<?php
/**
 * Copyright © Panth Infotech. All rights reserved.
 *
 * Product Gallery block with theme-aware template switching.
 */
declare(strict_types=1);

namespace Panth\ProductGallery\Block;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Helper\Image as ImageHelper;
use Panth\Core\Helper\Theme;
use Panth\ProductGallery\Helper\Data as ConfigHelper;

/**
 * Product gallery block
 */
class Gallery extends AbstractProduct
{
    /**
     * @var ConfigHelper
     */
    private ConfigHelper $configHelper;

    /**
     * @var Theme
     */
    private Theme $themeHelper;

    /**
     * @var ImageHelper
     */
    private ImageHelper $imageHelper;

    /**
     * Optional Panth_AdvancedSEO image template resolver. Soft
     * dependency injected via di.xml when that module is installed.
     * Nullable class hint lets PHP resolve lazily so ProductGallery
     * keeps working if Panth_AdvancedSEO is not installed.
     *
     * @var \Panth\AdvancedSEO\Model\ImageSeo\ImageTemplateResolver|null
     */
    private ?\Panth\AdvancedSEO\Model\ImageSeo\ImageTemplateResolver $imageTemplateResolver;

    /**
     * @param Context $context
     * @param ConfigHelper $configHelper
     * @param Theme $themeHelper
     * @param ImageHelper $imageHelper
     * @param array $data
     * @param object|null $imageTemplateResolver
     */
    public function __construct(
        Context $context,
        ConfigHelper $configHelper,
        Theme $themeHelper,
        ImageHelper $imageHelper,
        array $data = [],
        ?\Panth\AdvancedSEO\Model\ImageSeo\ImageTemplateResolver $imageTemplateResolver = null
    ) {
        $this->configHelper = $configHelper;
        $this->themeHelper = $themeHelper;
        $this->imageHelper = $imageHelper;
        $this->imageTemplateResolver = $imageTemplateResolver;
        parent::__construct($context, $data);
    }

    /**
     * Switch template based on active theme
     *
     * @return string
     */
    public function getTemplate()
    {
        if ($this->themeHelper->isHyva()) {
            return 'Panth_ProductGallery::hyva/gallery.phtml';
        }
        return 'Panth_ProductGallery::gallery.phtml';
    }

    /**
     * Check if module is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->configHelper->isEnabled();
    }

    /**
     * Get the current product
     *
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getCurrentProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    /**
     * Get product gallery images
     *
     * @return array
     */
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
                // Upgrade empty, product-name-only, and the Luma
                // sample-data placeholder "Image" to the SEO template.
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

            // Sort by position
            usort($images, function ($a, $b) {
                return $a['position'] <=> $b['position'];
            });

            // Suffix multi-image alts so each one is distinct.
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

    /**
     * Resolve SEO alt/title via the optionally injected template
     * resolver. Returns ['', ''] when the resolver is absent or
     * disabled so callers fall back to raw image labels.
     *
     * @param mixed $product
     * @return array{0:string,1:string}
     */
    private function resolveSeoAltTitle($product): array
    {
        if ($this->imageTemplateResolver === null) {
            return ['', ''];
        }
        try {
            // Gallery-specific gate honors panth_seo/image/gallery_seo_enabled.
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

    /**
     * Get gallery configuration array
     *
     * @return array
     */
    public function getGalleryConfig(): array
    {
        return $this->configHelper->getGalleryConfig();
    }

    /**
     * Get gallery config as JSON
     *
     * @return string
     */
    public function getGalleryConfigJson(): string
    {
        return (string) json_encode($this->configHelper->getGalleryConfig());
    }

    /**
     * Only render when enabled and product exists
     *
     * @return string
     */
    protected function _toHtml(): string
    {
        $product = $this->getCurrentProduct();
        if (!$product) {
            return '';
        }

        return parent::_toHtml();
    }
}

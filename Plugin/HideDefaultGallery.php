<?php
/**
 * Copyright © Panth Infotech. All rights reserved.
 *
 * Plugin to replace default Magento gallery with custom Panth gallery.
 */
declare(strict_types=1);

namespace Panth\ProductGallery\Plugin;

use Magento\Catalog\Block\Product\View\Gallery as DefaultGallery;
use Magento\Framework\Registry;
use Magento\Catalog\Helper\Image as ImageHelper;
use Panth\Core\Helper\Theme;
use Panth\ProductGallery\Helper\Data as ConfigHelper;
use Panth\ProductGallery\ViewModel\Config as ConfigViewModel;

/**
 * Plugin to hide the default Magento/Hyva gallery and render Panth gallery instead
 */
class HideDefaultGallery
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
     * @var ConfigViewModel
     */
    private ConfigViewModel $configViewModel;

    /**
     * @var Registry
     */
    private Registry $registry;

    /**
     * @var ImageHelper
     */
    private ImageHelper $imageHelper;

    /**
     * @var bool
     */
    private bool $rendering = false;

    /**
     * Optional SEO image template resolver from Panth_AdvancedSEO.
     * Uses mixed type to avoid ReflectionException when AdvancedSEO
     * is not installed — the class is resolved at runtime via
     * ObjectManager only when the module is present.
     *
     * @var mixed|null
     */
    private mixed $imageTemplateResolver = null;

    /**
     * @param ConfigHelper $configHelper
     * @param Theme $themeHelper
     * @param ConfigViewModel $configViewModel
     * @param Registry $registry
     * @param ImageHelper $imageHelper
     */
    public function __construct(
        ConfigHelper $configHelper,
        Theme $themeHelper,
        ConfigViewModel $configViewModel,
        Registry $registry,
        ImageHelper $imageHelper
    ) {
        $this->configHelper = $configHelper;
        $this->themeHelper = $themeHelper;
        $this->configViewModel = $configViewModel;
        $this->registry = $registry;
        $this->imageHelper = $imageHelper;

        // Resolve optional AdvancedSEO dependency at runtime
        $resolverClass = 'Panth\AdvancedSEO\Model\ImageSeo\ImageTemplateResolver';
        if (class_exists($resolverClass)) {
            try {
                $this->imageTemplateResolver = \Magento\Framework\App\ObjectManager::getInstance()
                    ->get($resolverClass);
            } catch (\Throwable $e) {
                $this->imageTemplateResolver = null;
            }
        }
    }

    /**
     * Replace default gallery HTML with Panth gallery
     *
     * @param DefaultGallery $subject
     * @param string $result
     * @return string
     */
    public function afterToHtml(DefaultGallery $subject, string $result): string
    {
        if (!$this->configHelper->isEnabled() || $this->rendering) {
            return $result;
        }

        // Only replace the main gallery block, not OG/meta blocks
        $blockName = $subject->getNameInLayout();
        if ($blockName !== 'product.media' && $blockName !== 'product.info.media.image') {
            return $result;
        }

        $product = $this->registry->registry('current_product');
        if (!$product) {
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

            // Use the block's own rendering engine to avoid require statements
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
            // Fall back to default
        } finally {
            $this->rendering = false;
        }

        return $result;
    }

    /**
     * Build gallery images array from product media gallery
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
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
            // Upgrade empty labels, product-name-only labels, and the
            // sample-data placeholder "Image" to the SEO template.
            // Real merchant-set labels (anything else) are preserved.
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

        // For multi-image galleries add a distinguishing position suffix
        // to the SEO alt text so each image has a unique alt attribute.
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

    /**
     * Resolve SEO alt/title via the optionally injected template
     * resolver. Returns ['', ''] when the resolver is absent or
     * disabled so the caller falls back to raw image labels.
     *
     * @return array{0:string,1:string}
     */
    private function resolveSeoAltTitle($product): array
    {
        if ($this->imageTemplateResolver === null) {
            return ['', ''];
        }
        try {
            // Gating is driven by isGalleryEnabled() which implicitly
            // returns false when the master image_seo_enabled flag is
            // off or when gallery_seo_enabled is off.
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

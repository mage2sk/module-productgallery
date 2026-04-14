<?php
/**
 * Copyright © Panth Infotech. All rights reserved.
 *
 * Product Gallery configuration helper.
 */
declare(strict_types=1);

namespace Panth\ProductGallery\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Product Gallery configuration helper
 */
class Data extends AbstractHelper
{
    /**
     * Configuration path constants
     */
    private const XML_PATH_PREFIX = 'panth_productgallery/';
    private const XML_PATH_ENABLED = 'panth_productgallery/general/enabled';

    /**
     * Get a config value from the module section
     *
     * @param string $path Relative path under panth_productgallery/
     * @param int|string|null $storeId
     * @return mixed
     */
    public function getConfigValue(string $path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PREFIX . $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if module is enabled
     *
     * @param int|string|null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    // ========================================
    // Layout Settings
    // ========================================

    /**
     * Get gallery layout type (horizontal, vertical, grid)
     *
     * @param int|string|null $storeId
     * @return string
     */
    public function getLayoutType($storeId = null): string
    {
        return (string) ($this->getConfigValue('layout/layout_type', $storeId) ?: 'horizontal');
    }

    /**
     * Get thumbnail position (bottom, left, right)
     *
     * @param int|string|null $storeId
     * @return string
     */
    public function getThumbPosition($storeId = null): string
    {
        return (string) ($this->getConfigValue('layout/thumb_position', $storeId) ?: 'bottom');
    }

    /**
     * Get main image width
     *
     * @param int|string|null $storeId
     * @return int
     */
    public function getMainImageWidth($storeId = null): int
    {
        return (int) ($this->getConfigValue('layout/main_image_width', $storeId) ?: 700);
    }

    /**
     * Get main image height
     *
     * @param int|string|null $storeId
     * @return int
     */
    public function getMainImageHeight($storeId = null): int
    {
        return (int) ($this->getConfigValue('layout/main_image_height', $storeId) ?: 700);
    }

    /**
     * Get thumbnail width
     *
     * @param int|string|null $storeId
     * @return int
     */
    public function getThumbWidth($storeId = null): int
    {
        return (int) ($this->getConfigValue('layout/thumb_width', $storeId) ?: 72);
    }

    /**
     * Get thumbnail height
     *
     * @param int|string|null $storeId
     * @return int
     */
    public function getThumbHeight($storeId = null): int
    {
        return (int) ($this->getConfigValue('layout/thumb_height', $storeId) ?: 72);
    }

    /**
     * Get number of visible thumbnails
     *
     * @param int|string|null $storeId
     * @return int
     */
    public function getVisibleThumbs($storeId = null): int
    {
        return (int) ($this->getConfigValue('layout/visible_thumbs', $storeId) ?: 5);
    }

    // ========================================
    // Zoom Settings
    // ========================================

    /**
     * Check if zoom is enabled
     *
     * @param int|string|null $storeId
     * @return bool
     */
    public function isZoomEnabled($storeId = null): bool
    {
        return (bool) $this->getConfigValue('zoom/enable_zoom', $storeId);
    }

    /**
     * Get zoom type (inner, lens)
     *
     * @param int|string|null $storeId
     * @return string
     */
    public function getZoomType($storeId = null): string
    {
        return (string) ($this->getConfigValue('zoom/zoom_type', $storeId) ?: 'inner');
    }

    /**
     * Get zoom level
     *
     * @param int|string|null $storeId
     * @return int
     */
    public function getZoomLevel($storeId = null): int
    {
        $level = (int) ($this->getConfigValue('zoom/zoom_level', $storeId) ?: 3);
        return max(2, min(5, $level));
    }

    // ========================================
    // Lightbox Settings
    // ========================================

    /**
     * Check if lightbox is enabled
     *
     * @param int|string|null $storeId
     * @return bool
     */
    public function isLightboxEnabled($storeId = null): bool
    {
        return (bool) $this->getConfigValue('lightbox/enable_lightbox', $storeId);
    }

    /**
     * Check if image counter is shown in lightbox
     *
     * @param int|string|null $storeId
     * @return bool
     */
    public function showLightboxCounter($storeId = null): bool
    {
        return (bool) $this->getConfigValue('lightbox/show_counter', $storeId);
    }

    /**
     * Check if keyboard navigation is enabled in lightbox
     *
     * @param int|string|null $storeId
     * @return bool
     */
    public function isKeyboardNavEnabled($storeId = null): bool
    {
        return (bool) $this->getConfigValue('lightbox/enable_keyboard_nav', $storeId);
    }

    // ========================================
    // Navigation Settings
    // ========================================

    /**
     * Check if navigation arrows are shown
     *
     * @param int|string|null $storeId
     * @return bool
     */
    public function showArrows($storeId = null): bool
    {
        return (bool) $this->getConfigValue('navigation/show_arrows', $storeId);
    }

    /**
     * Check if swipe is enabled
     *
     * @param int|string|null $storeId
     * @return bool
     */
    public function isSwipeEnabled($storeId = null): bool
    {
        return (bool) $this->getConfigValue('navigation/enable_swipe', $storeId);
    }

    /**
     * Check if infinite loop is enabled
     *
     * @param int|string|null $storeId
     * @return bool
     */
    public function isInfiniteLoop($storeId = null): bool
    {
        return (bool) $this->getConfigValue('navigation/infinite_loop', $storeId);
    }

    /**
     * Get all gallery configuration as array for JS/template usage
     *
     * @return array
     */
    public function getGalleryConfig(): array
    {
        return [
            'enabled' => $this->isEnabled(),
            'layout_type' => $this->getLayoutType(),
            'thumb_position' => $this->getThumbPosition(),
            'main_image_width' => $this->getMainImageWidth(),
            'main_image_height' => $this->getMainImageHeight(),
            'thumb_width' => $this->getThumbWidth(),
            'thumb_height' => $this->getThumbHeight(),
            'visible_thumbs' => $this->getVisibleThumbs(),
            'enable_zoom' => $this->isZoomEnabled(),
            'zoom_type' => $this->getZoomType(),
            'zoom_level' => $this->getZoomLevel(),
            'enable_lightbox' => $this->isLightboxEnabled(),
            'show_counter' => $this->showLightboxCounter(),
            'enable_keyboard_nav' => $this->isKeyboardNavEnabled(),
            'show_arrows' => $this->showArrows(),
            'enable_swipe' => $this->isSwipeEnabled(),
            'infinite_loop' => $this->isInfiniteLoop(),
        ];
    }
}

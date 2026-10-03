<?php
declare(strict_types=1);

namespace Panth\ProductGallery\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    private const XML_PATH_PREFIX = 'panth_productgallery/';
    private const XML_PATH_ENABLED = 'panth_productgallery/general/enabled';

    public function getConfigValue(string $path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PREFIX . $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isEnabled($storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getLayoutType($storeId = null): string
    {
        return (string) ($this->getConfigValue('layout/layout_type', $storeId) ?: 'horizontal');
    }

    public function getThumbPosition($storeId = null): string
    {
        return (string) ($this->getConfigValue('layout/thumb_position', $storeId) ?: 'bottom');
    }

    public function getMainImageWidth($storeId = null): int
    {
        return (int) ($this->getConfigValue('layout/main_image_width', $storeId) ?: 700);
    }

    public function getMainImageHeight($storeId = null): int
    {
        return (int) ($this->getConfigValue('layout/main_image_height', $storeId) ?: 700);
    }

    public function getThumbWidth($storeId = null): int
    {
        return (int) ($this->getConfigValue('layout/thumb_width', $storeId) ?: 72);
    }

    public function getThumbHeight($storeId = null): int
    {
        return (int) ($this->getConfigValue('layout/thumb_height', $storeId) ?: 72);
    }

    public function getVisibleThumbs($storeId = null): int
    {
        return (int) ($this->getConfigValue('layout/visible_thumbs', $storeId) ?: 5);
    }

    public function isZoomEnabled($storeId = null): bool
    {
        return (bool) $this->getConfigValue('zoom/enable_zoom', $storeId);
    }

    public function getZoomType($storeId = null): string
    {
        return (string) ($this->getConfigValue('zoom/zoom_type', $storeId) ?: 'inner');
    }

    public function getZoomLevel($storeId = null): int
    {
        $level = (int) ($this->getConfigValue('zoom/zoom_level', $storeId) ?: 3);
        return max(2, min(5, $level));
    }

    public function isLightboxEnabled($storeId = null): bool
    {
        return (bool) $this->getConfigValue('lightbox/enable_lightbox', $storeId);
    }

    public function showLightboxCounter($storeId = null): bool
    {
        return (bool) $this->getConfigValue('lightbox/show_counter', $storeId);
    }

    public function isKeyboardNavEnabled($storeId = null): bool
    {
        return (bool) $this->getConfigValue('lightbox/enable_keyboard_nav', $storeId);
    }

    public function showArrows($storeId = null): bool
    {
        return (bool) $this->getConfigValue('navigation/show_arrows', $storeId);
    }

    public function isSwipeEnabled($storeId = null): bool
    {
        return (bool) $this->getConfigValue('navigation/enable_swipe', $storeId);
    }

    public function isInfiniteLoop($storeId = null): bool
    {
        return (bool) $this->getConfigValue('navigation/infinite_loop', $storeId);
    }

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

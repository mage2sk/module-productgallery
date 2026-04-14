<?php
/**
 * Copyright © Panth Infotech. All rights reserved.
 */
declare(strict_types=1);

namespace Panth\ProductGallery\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Panth\ProductGallery\Helper\Data as ConfigHelper;

/**
 * ViewModel for product gallery configuration access in templates
 */
class Config implements ArgumentInterface
{
    /**
     * @var ConfigHelper
     */
    private ConfigHelper $configHelper;

    /**
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        ConfigHelper $configHelper
    ) {
        $this->configHelper = $configHelper;
    }

    /**
     * Check if product gallery is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->configHelper->isEnabled();
    }

    /**
     * Get gallery layout type
     *
     * @return string
     */
    public function getLayoutType(): string
    {
        return $this->configHelper->getLayoutType();
    }

    /**
     * Get thumbnail position
     *
     * @return string
     */
    public function getThumbPosition(): string
    {
        return $this->configHelper->getThumbPosition();
    }

    /**
     * Get main image width
     *
     * @return int
     */
    public function getMainImageWidth(): int
    {
        return $this->configHelper->getMainImageWidth();
    }

    /**
     * Get main image height
     *
     * @return int
     */
    public function getMainImageHeight(): int
    {
        return $this->configHelper->getMainImageHeight();
    }

    /**
     * Get thumbnail width
     *
     * @return int
     */
    public function getThumbWidth(): int
    {
        return $this->configHelper->getThumbWidth();
    }

    /**
     * Get thumbnail height
     *
     * @return int
     */
    public function getThumbHeight(): int
    {
        return $this->configHelper->getThumbHeight();
    }

    /**
     * Get visible thumbnails count
     *
     * @return int
     */
    public function getVisibleThumbs(): int
    {
        return $this->configHelper->getVisibleThumbs();
    }

    /**
     * Check if zoom is enabled
     *
     * @return bool
     */
    public function isZoomEnabled(): bool
    {
        return $this->configHelper->isZoomEnabled();
    }

    /**
     * Get zoom type
     *
     * @return string
     */
    public function getZoomType(): string
    {
        return $this->configHelper->getZoomType();
    }

    /**
     * Get zoom level
     *
     * @return int
     */
    public function getZoomLevel(): int
    {
        return $this->configHelper->getZoomLevel();
    }

    /**
     * Check if lightbox is enabled
     *
     * @return bool
     */
    public function isLightboxEnabled(): bool
    {
        return $this->configHelper->isLightboxEnabled();
    }

    /**
     * Check if lightbox counter is shown
     *
     * @return bool
     */
    public function showLightboxCounter(): bool
    {
        return $this->configHelper->showLightboxCounter();
    }

    /**
     * Check if keyboard navigation is enabled
     *
     * @return bool
     */
    public function isKeyboardNavEnabled(): bool
    {
        return $this->configHelper->isKeyboardNavEnabled();
    }

    /**
     * Check if navigation arrows are shown
     *
     * @return bool
     */
    public function showArrows(): bool
    {
        return $this->configHelper->showArrows();
    }

    /**
     * Check if swipe is enabled
     *
     * @return bool
     */
    public function isSwipeEnabled(): bool
    {
        return $this->configHelper->isSwipeEnabled();
    }

    /**
     * Check if infinite loop is enabled
     *
     * @return bool
     */
    public function isInfiniteLoop(): bool
    {
        return $this->configHelper->isInfiniteLoop();
    }

    /**
     * Get full gallery config as array (for JS initialization)
     *
     * @return array
     */
    public function getGalleryConfig(): array
    {
        return $this->configHelper->getGalleryConfig();
    }

    /**
     * Get gallery config as JSON string for template usage
     *
     * @return string
     */
    public function getGalleryConfigJson(): string
    {
        return (string) json_encode($this->configHelper->getGalleryConfig());
    }
}

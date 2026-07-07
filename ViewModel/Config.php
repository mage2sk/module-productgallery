<?php
declare(strict_types=1);

namespace Panth\ProductGallery\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Panth\ProductGallery\Helper\Data as ConfigHelper;

class Config implements ArgumentInterface
{
    private ConfigHelper $configHelper;

    public function __construct(
        ConfigHelper $configHelper
    ) {
        $this->configHelper = $configHelper;
    }

    public function isEnabled(): bool
    {
        return $this->configHelper->isEnabled();
    }

    public function getLayoutType(): string
    {
        return $this->configHelper->getLayoutType();
    }

    public function getThumbPosition(): string
    {
        return $this->configHelper->getThumbPosition();
    }

    public function getMainImageWidth(): int
    {
        return $this->configHelper->getMainImageWidth();
    }

    public function getMainImageHeight(): int
    {
        return $this->configHelper->getMainImageHeight();
    }

    public function getThumbWidth(): int
    {
        return $this->configHelper->getThumbWidth();
    }

    public function getThumbHeight(): int
    {
        return $this->configHelper->getThumbHeight();
    }

    public function getVisibleThumbs(): int
    {
        return $this->configHelper->getVisibleThumbs();
    }

    public function isZoomEnabled(): bool
    {
        return $this->configHelper->isZoomEnabled();
    }

    public function getZoomType(): string
    {
        return $this->configHelper->getZoomType();
    }

    public function getZoomLevel(): int
    {
        return $this->configHelper->getZoomLevel();
    }

    public function isLightboxEnabled(): bool
    {
        return $this->configHelper->isLightboxEnabled();
    }

    public function showLightboxCounter(): bool
    {
        return $this->configHelper->showLightboxCounter();
    }

    public function isKeyboardNavEnabled(): bool
    {
        return $this->configHelper->isKeyboardNavEnabled();
    }

    public function showArrows(): bool
    {
        return $this->configHelper->showArrows();
    }

    public function isSwipeEnabled(): bool
    {
        return $this->configHelper->isSwipeEnabled();
    }

    public function isInfiniteLoop(): bool
    {
        return $this->configHelper->isInfiniteLoop();
    }

    public function getGalleryConfig(): array
    {
        return $this->configHelper->getGalleryConfig();
    }

    public function getGalleryConfigJson(): string
    {
        return (string) json_encode($this->configHelper->getGalleryConfig());
    }
}

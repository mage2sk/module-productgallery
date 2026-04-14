<?php
/**
 * Copyright © Panth Infotech. All rights reserved.
 */
declare(strict_types=1);

namespace Panth\ProductGallery\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Zoom type options for gallery configuration
 */
class ZoomType implements OptionSourceInterface
{
    /**
     * Get zoom type options
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'inner', 'label' => __('Inner Zoom (Inside Image)')],
            ['value' => 'lens', 'label' => __('Lens Zoom (Magnifying Glass)')],
        ];
    }
}

<?php
/**
 * Copyright © Panth Infotech. All rights reserved.
 */
declare(strict_types=1);

namespace Panth\ProductGallery\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Layout type options for gallery configuration
 */
class LayoutType implements OptionSourceInterface
{
    /**
     * Get layout type options
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'horizontal', 'label' => __('Horizontal Thumbnails (Below Main Image)')],
            ['value' => 'vertical', 'label' => __('Vertical Thumbnails (Left of Main Image)')],
            ['value' => 'grid', 'label' => __('Grid (All Images Visible)')],
        ];
    }
}

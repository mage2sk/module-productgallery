<?php
/**
 * Copyright © Panth Infotech. All rights reserved.
 */
declare(strict_types=1);

namespace Panth\ProductGallery\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Thumbnail position options
 */
class ThumbPosition implements OptionSourceInterface
{
    /**
     * Get thumbnail position options
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'bottom', 'label' => __('Bottom')],
            ['value' => 'left', 'label' => __('Left')],
            ['value' => 'right', 'label' => __('Right')],
        ];
    }
}

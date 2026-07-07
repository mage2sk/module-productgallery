<?php
declare(strict_types=1);

namespace Panth\ProductGallery\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class LayoutType implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'horizontal', 'label' => __('Horizontal Thumbnails (Below Main Image)')],
            ['value' => 'vertical', 'label' => __('Vertical Thumbnails (Left of Main Image)')],
            ['value' => 'grid', 'label' => __('Grid (All Images Visible)')],
        ];
    }
}

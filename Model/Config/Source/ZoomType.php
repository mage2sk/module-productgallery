<?php
declare(strict_types=1);

namespace Panth\ProductGallery\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ZoomType implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'inner', 'label' => __('Inner Zoom (Inside Image)')],
            ['value' => 'lens', 'label' => __('Lens Zoom (Magnifying Glass)')],
        ];
    }
}

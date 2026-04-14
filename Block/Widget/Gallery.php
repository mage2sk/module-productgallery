<?php
/**
 * Copyright © Panth Infotech. All rights reserved.
 *
 * Product Gallery widget block.
 */
declare(strict_types=1);

namespace Panth\ProductGallery\Block\Widget;

use Panth\ProductGallery\Block\Gallery as GalleryBlock;
use Magento\Widget\Block\BlockInterface;

/**
 * Product gallery widget block
 */
class Gallery extends GalleryBlock implements BlockInterface
{
    /**
     * Default Luma template (overridden by parent getTemplate for Hyva)
     *
     * @var string
     */
    protected $_template = 'Panth_ProductGallery::gallery.phtml';
}

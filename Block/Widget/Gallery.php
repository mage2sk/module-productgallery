<?php
declare(strict_types=1);

namespace Panth\ProductGallery\Block\Widget;

use Panth\ProductGallery\Block\Gallery as GalleryBlock;
use Magento\Widget\Block\BlockInterface;

class Gallery extends GalleryBlock implements BlockInterface
{
    protected $_template = 'Panth_ProductGallery::gallery.phtml';
}

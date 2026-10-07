<?php
declare(strict_types=1);

namespace Panth\ProductGallery\Test\Unit\Fixture;

use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\DataObject;
use Panth\ProductGallery\Helper\Data as ConfigHelper;

/**
 * Shared doubles for the gallery block and the default-gallery plugin.
 */
trait GalleryFixtureTrait
{
    /**
     * Image helper stub whose URLs encode the image id, file and resize box,
     * for example "product_page_image_small|/a.jpg|72x72".
     *
     * @return ImageHelper
     */
    private function imageHelper(): ImageHelper
    {
        $state = new \stdClass();
        $state->id = '';
        $state->file = '';
        $state->size = '';

        $helper = $this->createStub(ImageHelper::class);
        $helper->method('getDefaultPlaceholderUrl')->willReturn('https://example.com/static/placeholder/image.jpg');
        $helper->method('init')->willReturnCallback(
            static function ($product, $imageId) use ($state, &$helper) {
                $state->id = (string) $imageId;
                $state->file = '';
                $state->size = '';
                return $helper;
            }
        );
        $helper->method('setImageFile')->willReturnCallback(
            static function ($file) use ($state, &$helper) {
                $state->file = (string) $file;
                return $helper;
            }
        );
        $helper->method('resize')->willReturnCallback(
            static function ($width, $height = null) use ($state, &$helper) {
                $state->size = $width . 'x' . $height;
                return $helper;
            }
        );
        $helper->method('getUrl')->willReturnCallback(
            static function () use ($state) {
                return rtrim($state->id . '|' . $state->file . '|' . $state->size, '|');
            }
        );

        return $helper;
    }

    /**
     * Config helper stub with fixed image dimensions.
     *
     * @param bool $enabled
     * @return ConfigHelper
     */
    private function sizedConfigHelper(bool $enabled = true): ConfigHelper
    {
        $config = $this->createStub(ConfigHelper::class);
        $config->method('isEnabled')->willReturn($enabled);
        $config->method('getMainImageWidth')->willReturn(700);
        $config->method('getMainImageHeight')->willReturn(600);
        $config->method('getThumbWidth')->willReturn(72);
        $config->method('getThumbHeight')->willReturn(64);
        $config->method('getGalleryConfig')->willReturn(['layout_type' => 'horizontal']);
        return $config;
    }

    private function image(string $file, int $position, string $label = '', bool $disabled = false): DataObject
    {
        return new DataObject([
            'file' => $file,
            'position' => $position,
            'label' => $label,
            'disabled' => $disabled ? 1 : 0,
        ]);
    }

    /**
     * @param array|null $images
     * @param string $mainImage
     * @param int $id
     * @return DataObject
     */
    private function product(?array $images, string $mainImage = '/a.jpg', int $id = 5): DataObject
    {
        return new DataObject([
            'id' => $id,
            'name' => 'Blue Shirt',
            'image' => $mainImage,
            'media_gallery_images' => $images,
        ]);
    }
}

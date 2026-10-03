<?php
declare(strict_types=1);

namespace Panth\ProductGallery\Test\Unit\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Panth\ProductGallery\Model\Config\Source\LayoutType;
use Panth\ProductGallery\Model\Config\Source\ThumbPosition;
use Panth\ProductGallery\Model\Config\Source\ZoomType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SourceModelsTest extends TestCase
{
    public static function sourceProvider(): array
    {
        return [
            'layout type' => [LayoutType::class, ['horizontal', 'vertical', 'grid']],
            'thumb position' => [ThumbPosition::class, ['bottom', 'left', 'right']],
            'zoom type' => [ZoomType::class, ['inner', 'lens']],
        ];
    }

    #[DataProvider('sourceProvider')]
    public function testOptionValuesAreTheOnesTheHelperUnderstands(string $class, array $values): void
    {
        /** @var OptionSourceInterface $source */
        $source = new $class();
        $options = $source->toOptionArray();

        $this->assertSame($values, array_column($options, 'value'));
        foreach ($options as $option) {
            $this->assertNotSame('', (string) $option['label']);
        }
    }

    public function testHelperDefaultsAreAmongTheOfferedOptions(): void
    {
        $this->assertContains('horizontal', array_column((new LayoutType())->toOptionArray(), 'value'));
        $this->assertContains('bottom', array_column((new ThumbPosition())->toOptionArray(), 'value'));
        $this->assertContains('inner', array_column((new ZoomType())->toOptionArray(), 'value'));
    }
}

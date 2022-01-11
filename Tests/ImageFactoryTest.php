<?php


use Msgframework\Lib\Image\ImageFactory;
use PHPUnit\Framework\TestCase;
use Msgframework\Lib\Image\Adapter\ImageAdapterInterface;

class ImageFactoryTest extends TestCase
{
    var array $path = array(
        'jpg' => __DIR__ . DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR . 'apples.jpg'
    );
    /**
     * Checking if the factory is returning the expected adapters
     */
    function testFactoryCreateAdapter()
    {
        $factory = new ImageFactory();

        $image = $factory->getImage($this->path['jpg']);

        $this->assertTrue($image instanceof ImageAdapterInterface);

        $image->destroy();
        $image = $factory->getImage($this->path['jpg'], 'Imagick');
        $this->assertTrue($image instanceof \Msgframework\Lib\Image\Adapter\ImagickAdapter);

        $image->destroy();
        $image = $factory->getImage($this->path['jpg'], 'GD');
        $this->assertTrue($image instanceof \Msgframework\Lib\Image\Adapter\GDAdapter);

        $image->destroy();
    }
}

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

        $this->assertTrue($factory->getImage($this->path['jpg']) instanceof ImageAdapterInterface);
        $this->assertTrue($factory->getImage($this->path['jpg'], 'Imagick') instanceof \Msgframework\Lib\Image\Adapter\ImagickAdapter);
        $this->assertTrue($factory->getImage($this->path['jpg'], 'GD') instanceof \Msgframework\Lib\Image\Adapter\GDAdapter);
    }
}

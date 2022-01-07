<?php


use PHPUnit\Framework\TestCase;

class ImageGDAdapterTest extends TestCase
{
    var array $path = array(
        'jpg' => __DIR__ . DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR . 'apples.jpg',
        'watermark' => __DIR__ . DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR . 'watermark.png',
        'save' => __DIR__ . DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR . 'gd' . DIRECTORY_SEPARATOR . 'save.jpg',
        'watermarking' => __DIR__ . DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR . 'gd' . DIRECTORY_SEPARATOR . 'watermarking.jpg'
    );

    function testGDAdapterCreate()
    {
        $image = new \Msgframework\Lib\Image\Adapter\GDAdapter($this->path['jpg']);

        $this->assertTrue($image instanceof \Msgframework\Lib\Image\Adapter\GDAdapter);
    }

    function testGDAdapterImageResize()
    {
        $image = new \Msgframework\Lib\Image\Adapter\GDAdapter($this->path['jpg']);

        $this->assertSame(2482, $image->getWidth());
        $this->assertSame(3475, $image->getHeight());

        $this->assertSame(300, $image->resize(300, 1)->getWidth());

        $this->assertSame(300, $image->resize(300, 2)->getHeight());
    }

    function testGDAdapterImageCrop()
    {
        $image = new \Msgframework\Lib\Image\Adapter\GDAdapter($this->path['jpg']);

        $this->assertSame(2482, $image->getWidth());
        $this->assertSame(3475, $image->getHeight());

        $image->crop(500, 500);

        $this->assertSame(500, $image->getWidth());
        $this->assertSame(500, $image->getHeight());
    }

    function testGDAdapterImageScale()
    {
        $image = new \Msgframework\Lib\Image\Adapter\GDAdapter($this->path['jpg']);

        $this->assertSame(2482, $image->getWidth());
        $this->assertSame(3475, $image->getHeight());

        $image->scale(200, 1);

        $this->assertSame(200, $image->getWidth());

        $image = new \Msgframework\Lib\Image\Adapter\GDAdapter($this->path['jpg']);

        $image->scale(200, 2);

        $this->assertSame(200, $image->getHeight());
    }

    function testGDAdapterImageSave()
    {
        $image = new \Msgframework\Lib\Image\Adapter\GDAdapter($this->path['jpg']);

        $this->assertSame(2482, $image->getWidth());
        $this->assertSame(3475, $image->getHeight());

        $image->save($this->path['save']);

        $this->assertFileExists($this->path['save']);
        
        unlink($this->path['save']);
    }

    function testGDAdapterImageWatermarking()
    {
        $image = new \Msgframework\Lib\Image\Adapter\GDAdapter($this->path['jpg']);
        $watermark = new \Msgframework\Lib\Image\Adapter\GDAdapter($this->path['watermark']);

        $this->assertSame(2482, $image->getWidth());
        $this->assertSame(3475, $image->getHeight());

        $image->watermark($watermark, 0, 0, 50, .9)->save($this->path['watermarking']);

        $this->assertFileExists($this->path['watermarking']);

        //unlink($this->path['watermarking']);
    }
}

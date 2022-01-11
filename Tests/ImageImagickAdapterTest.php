<?php


use PHPUnit\Framework\TestCase;

class ImageImagickAdapterTest extends TestCase
{
    var array $path = array(
        'jpg' => __DIR__ . DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR . 'apples.jpg',
        'watermark' => __DIR__ . DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR . 'watermark.png'
    );

    function testImagickAdapterCreate()
    {
        $image = new \Msgframework\Lib\Image\Adapter\ImagickAdapter($this->path['jpg']);

        $this->assertTrue($image instanceof \Msgframework\Lib\Image\Adapter\ImagickAdapter);
        $image->destroy();
    }

    function testImagickAdapterImageResize()
    {
        $image = new \Msgframework\Lib\Image\Adapter\ImagickAdapter($this->path['jpg']);

        $this->assertSame(2482, $image->getWidth());
        $this->assertSame(3475, $image->getHeight());

        $this->assertSame(300, $image->resize(300, 1)->getWidth());

        $this->assertSame(300, $image->resize(300, 2)->getHeight());
        $image->destroy();
    }

    function testImagickAdapterImageCrop()
    {
        $image = new \Msgframework\Lib\Image\Adapter\ImagickAdapter($this->path['jpg']);

        $this->assertSame(2482, $image->getWidth());
        $this->assertSame(3475, $image->getHeight());

        $image->crop(500, 500);

        $this->assertSame(500, $image->getWidth());
        $this->assertSame(500, $image->getHeight());
        $image->destroy();
    }

    function testImagickAdapterImageScale()
    {
        $image = new \Msgframework\Lib\Image\Adapter\ImagickAdapter($this->path['jpg']);

        $this->assertSame(2482, $image->getWidth());
        $this->assertSame(3475, $image->getHeight());

        $image->scale(200, 1);

        $this->assertSame(200, $image->getWidth());

        $image = new \Msgframework\Lib\Image\Adapter\ImagickAdapter($this->path['jpg']);

        $image->scale(200, 2);

        $this->assertSame(200, $image->getHeight());
        $image->destroy();
    }

    function testImagickAdapterImageSave()
    {
        $tmp_file = new \Msgframework\Lib\File\TmpFile();
        $image = new \Msgframework\Lib\Image\Adapter\ImagickAdapter($this->path['jpg']);

        $this->assertSame(2482, $image->getWidth());
        $this->assertSame(3475, $image->getHeight());

        $image->save($tmp_file->getPathname());

        $this->assertFileExists($tmp_file->getPathname());

        $image->destroy();
        $tmp_file->remove();
    }

    function testImagickAdapterImageWatermarking()
    {
        $tmp_file = new \Msgframework\Lib\File\TmpFile();
        $image = new \Msgframework\Lib\Image\Adapter\ImagickAdapter($this->path['jpg']);
        $watermark = new \Msgframework\Lib\Image\Adapter\ImagickAdapter($this->path['watermark']);

        $this->assertSame(2482, $image->getWidth());
        $this->assertSame(3475, $image->getHeight());

        $image->watermark($watermark, 0, 0, 50, .9)->save($tmp_file->getPathname());

        $this->assertFileExists($tmp_file->getPathname());
        $image->destroy();
        $tmp_file->remove();
    }
}

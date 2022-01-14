<?php


use Msgframework\Lib\Image\Adapter\ImageAdapter;
use PHPUnit\Framework\TestCase;

class ImageGDAdapterTest extends TestCase
{
    var array $path = array(
        'jpg' => __DIR__ . DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR . 'apples.jpg',
        'watermark' => __DIR__ . DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR . 'watermark.png'
    );

    function testGDAdapterCreate()
    {
        $image = new \Msgframework\Lib\Image\Adapter\GDAdapter($this->path['jpg']);

        $this->assertTrue($image instanceof \Msgframework\Lib\Image\Adapter\GDAdapter);
        $image->destroy();
    }

    function testGDAdapterImageResize()
    {
        $image = new \Msgframework\Lib\Image\Adapter\GDAdapter($this->path['jpg']);

        $this->assertSame(2482, $image->getWidth());
        $this->assertSame(3475, $image->getHeight());

        $this->assertSame(300, $image->resize(300, ImageAdapter::IMAGE_SITE_WIDTH)->getWidth());

        $this->assertSame(300, $image->resize(300, ImageAdapter::IMAGE_SITE_HEIGHT)->getHeight());
        $image->destroy();
    }

    function testGDAdapterImageCrop()
    {
        $image = new \Msgframework\Lib\Image\Adapter\GDAdapter($this->path['jpg']);

        $this->assertSame(2482, $image->getWidth());
        $this->assertSame(3475, $image->getHeight());

        $image->crop(500, 500);

        $this->assertSame(500, $image->getWidth());
        $this->assertSame(500, $image->getHeight());
        $image->destroy();
    }

    function testGDAdapterImageScale()
    {
        $image = new \Msgframework\Lib\Image\Adapter\GDAdapter($this->path['jpg']);

        $this->assertSame(2482, $image->getWidth());
        $this->assertSame(3475, $image->getHeight());

        $image->scale(200, ImageAdapter::IMAGE_SITE_WIDTH);

        $this->assertSame(200, $image->getWidth());

        $image = new \Msgframework\Lib\Image\Adapter\GDAdapter($this->path['jpg']);

        $image->scale(200, ImageAdapter::IMAGE_SITE_HEIGHT);

        $this->assertSame(200, $image->getHeight());
        $image->destroy();
    }

    function testGDAdapterImageSave()
    {
        $tmp_file = new \Msgframework\Lib\File\TmpFile();
        $image = new \Msgframework\Lib\Image\Adapter\GDAdapter($this->path['jpg']);

        $this->assertSame(2482, $image->getWidth());
        $this->assertSame(3475, $image->getHeight());

        $image->save($tmp_file->getPathname());

        $this->assertFileExists($tmp_file->getPathname());

        $image->destroy();
        $tmp_file->remove();
    }

    function testGDAdapterImageWatermarking()
    {
        $tmp_file = new \Msgframework\Lib\File\TmpFile();
        $image = new \Msgframework\Lib\Image\Adapter\GDAdapter($this->path['jpg']);
        $watermark = new \Msgframework\Lib\Image\Adapter\GDAdapter($this->path['watermark']);

        $this->assertSame(2482, $image->getWidth());
        $this->assertSame(3475, $image->getHeight());

        $image->watermark($watermark, 0, 0, 50, .9)->save($tmp_file->getPathname());

        $this->assertFileExists($tmp_file->getPathname());

        $image->destroy();
        $tmp_file->remove();
    }
}

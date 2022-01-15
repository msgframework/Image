<?php

namespace Msgframework\Lib\Image\Adapter;

use MSGFramework\Lib\Image\Exception\ImageException;
use MSGFramework\Lib\Image\Exception\ImageManipulateException;
use MSGFramework\Lib\Image\Exception\ImageNotSavedException;

class GDAdapter extends ImageAdapter implements ImageAdapterInterface
{
    private $image;
    private int $quality = 100;

    function __construct($path)
    {
        parent::__construct($path);
        $info = getimagesize($this->getPathName());

        if ($info === false) {
            throw new ImageException(sprintf('The file "%s" not the image', $this->getPathName()));
        }

        switch ($this->getMimeType()) {
            case 'image/gif':
                $gif = imagecreatefromgif($this->getPathName());
                if ($gif) {
                    $width = imagesx($gif);
                    $height = imagesy($gif);
                    $this->image = imagecreatetruecolor($width, $height);
                    $transparentColor = imagecolorallocatealpha($this->image, 0, 0, 0, 127);
                    imagecolortransparent($this->image, $transparentColor);
                    imagefill($this->image, 0, 0, $transparentColor);
                    imagecopy($this->image, $gif, 0, 0, 0, 0, $width, $height);
                    imagedestroy($gif);
                }
                break;
            case 'image/jpeg':
                $this->image = imagecreatefromjpeg($this->getPathName());
                break;
            case 'image/png':
                $this->image = imagecreatefrompng($this->getPathName());
                break;
            case 'image/webp':
                $this->image = imagecreatefromwebp($this->getPathName());
                break;
            case 'image/bmp':
            case 'image/x-ms-bmp':
            case 'image/x-windows-bmp':
                $this->image = imagecreatefrombmp($this->getPathName());
                break;
        }

        if (!$this->image) {
            throw new ImageException(sprintf('Failed to decode the image from file "%s" (%s)', $this->getPathName(), $this->getMimeType()));
        }
    }

    function getWidth(): int
    {
        return @imagesx($this->image);
    }

    function getHeight(): int
    {
        return @imagesy($this->image);
    }

    function resize($size = 100, int $side = 0): self
    {
        $size = explode("x", $size);

        if (count($size) == 2) {
            $size[0] = intval($size[0]);
            $size[1] = intval($size[1]);

            return $this->crop($size[0], $size[1]);
        } else {
            $size[0] = intval($size[0]);

            return $this->scale($size[0], $side);
        }
    }

    function crop(int $width, int $height): self
    {
        $width = min($width, $this->getWidth());
        $height = min($height, $this->getHeight());

        $size_ratio = max($width / $this->getWidth(), $height / $this->getHeight());

        $src_w = ceil($width / $size_ratio);
        $src_h = ceil($height / $size_ratio);

        $sx = intval(floor(($this->getWidth() - $src_w) / 2));
        $sy = intval(floor(($this->getHeight() - $src_h) / 2));

        $tmp_image = imagecreatetruecolor($width, $height);

        switch ($this->getMimeType()) {
            case 'image/gif':
                $transparent_index = imagecolortransparent($this->image);

                if ($transparent_index !== -1) {
                    $transparent_color = imagecolorsforindex($this->image, $transparent_index);

                    $transparent_destination_index = imagecolorallocate($tmp_image, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                    imagecolortransparent($tmp_image, $transparent_destination_index);

                    imagefill($tmp_image, 0, 0, $transparent_destination_index);
                }
                break;
            case 'image/png':
                imagealphablending($tmp_image, false);
                imagesavealpha($tmp_image, true);
                break;
        }

        if (!imagecopyresampled($tmp_image, $this->image, 0, 0, $sx, $sy, $width, $height, $src_w, $src_h)) {
            throw new ImageManipulateException($this->getPathName(), sprintf('crop to width: "%s" and height "%s"', $width, $height));
        }

        $this->image = $tmp_image;

        return $this;
    }

    function scale(int $size = 100, int $side = 0): self
    {
        switch ($side) {
            case self::SIDE_WIDTH :
                if ($this->getWidth() <= $size) {
                    return $this;
                } else {
                    $width = $size;
                    $height = intval(($width / $this->getWidth()) * $this->getHeight());
                }

                break;

            case self::SIDE_HEIGHT :
                if ($this->getHeight() <= $size) {
                    return $this;
                } else {
                    $height = $size;
                    $width = intval(($height / $this->getHeight()) * $this->getWidth());
                }

                break;

            case self::SIDE_AUTO :
            default :

                if ($this->getWidth() >= $this->getHeight()) {
                    $width = $size;
                    $height = intval(($width / $this->getWidth()) * $this->getHeight());

                } else {

                    $height = $size;
                    $width = intval(($height / $this->getHeight()) * $this->getWidth());

                }

                break;
        }

        if ($width < 1) $width = 1;
        if ($height < 1) $height = 1;

        $tmp_image = imagecreatetruecolor($width, $height);

        switch ($this->getMimeType()) {
            case 'image/gif':
                $transparent_index = imagecolortransparent($this->image);

                if ($transparent_index !== -1) {
                    $transparent_color = imagecolorsforindex($this->image, $transparent_index);

                    $transparent_destination_index = imagecolorallocate($tmp_image, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                    imagecolortransparent($tmp_image, $transparent_destination_index);

                    imagefill($tmp_image, 0, 0, $transparent_destination_index);
                }
                break;
            case 'image/png':
                imagealphablending($tmp_image, false);
                imagesavealpha($tmp_image, true);
                break;
        }

        if (!@imagecopyresampled($tmp_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight())) {
            throw new ImageManipulateException($this->getPathName(), sprintf('resize to width: "%s" and height "%s"', $width, $height));
        }

        $this->image = $tmp_image;

        return $this;

    }

    function opacity(float $opacity = 1): self
    {
        // Duplicate image and convert to TrueColor
        $imageDst = imagecreatetruecolor($this->getWidth(), $this->getHeight());
        imagealphablending($imageDst, false);
        imagefill($imageDst, 0, 0, imagecolortransparent($imageDst));
        imagecopy($imageDst, $this->image, 0, 0, 0, 0, $this->getWidth(), $this->getHeight());

        // Set new opacity to each pixel
        for ($x = 0; $x < $this->getWidth(); ++$x) {
            for ($y = 0; $y < $this->getHeight(); ++$y) {
                $pixelColor = imagecolorat($imageDst, $x, $y);
                $pixelOpacity = 127 - (($pixelColor >> 24) & 0xFF);
                if ($pixelOpacity > 0) {
                    $pixelOpacity = $pixelOpacity * $opacity;
                    $pixelColor = ($pixelColor & 0xFFFFFF) | ((int)round(127 - $pixelOpacity) << 24);
                    imagesetpixel($imageDst, $x, $y, $pixelColor);
                }
            }
        }

        $this->image = $imageDst;
        return $this;
    }

    function quality(int $quality = 90): self
    {
        $this->quality = $quality;
        return $this;
    }

    function save(string $path): void
    {
        $result = false;
        switch ($this->getMimeType()) {
            case 'image/gif':
                $result = imagegif($this->image, $path);
                break;
            case 'image/jpeg':
                $result = imagejpeg($this->image, $path, $this->quality);
                break;
            case 'image/png':
                imagealphablending($this->image, false);
                imagesavealpha($this->image, true);
                $result = imagepng($this->image, $path, 8);
                break;
            case 'image/webp':
                $result = imagewebp($this->image, $path, 8);
                break;
            case 'image/bmp':
            case 'image/x-ms-bmp':
            case 'image/x-windows-bmp':
                $result = imagewbmp($this->image, $path);
                break;
        }

        imagedestroy($this->image);

        if (!$result) {
            throw new ImageNotSavedException($this->getPathName(), $path);
        }
    }

    function show()
    {
        switch ($this->getMimeType()) {
            case 'image/gif':
                imagegif($this->image);
                break;
            case 'image/jpeg':
                imagejpeg($this->image, NULL, $this->quality);
                break;
            case 'image/png':
                imagealphablending($this->image, false);
                imagesavealpha($this->image, true);
                imagepng($this->image, NULL, ceil($this->quality / 10));
                break;
            case 'image/webp':
                imagewebp($this->image, NULL, $this->quality);
                break;
            case 'image/bmp':
            case 'image/x-ms-bmp':
            case 'image/x-windows-bmp':
                imagewbmp($this->image);
                break;
        }

        imagedestroy($this->image);
    }

    function watermark(ImageAdapterInterface $watermark, int $position, int $margin, int $ratio, float $opacity = 1): self
    {
        $watermark->resize($this->getWidth() * ($ratio / 100), 1);

        switch ($position)
        {
            case ImageAdapter::WATERMARK_TOP_CENTER :
                $watermark_x = ($this->getWidth() / 2) - ($watermark->getWidth() / 2);
                $watermark_y = $margin;
                break;

            case ImageAdapter::WATERMARK_TOP_RIGHT :
                $watermark_x = $this->getWidth() - $margin - $watermark->getWidth();
                $watermark_y = $margin;
                break;

            case ImageAdapter::WATERMARK_CENTER_LEFT :
                $watermark_x = $margin;
                $watermark_y = ($this->getHeight() / 2) - ($watermark->getHeight() / 2);
                break;

            case ImageAdapter::WATERMARK_CENTER_CENTER :
                $watermark_x = ($this->getWidth() / 2) - ($watermark->getWidth() / 2);
                $watermark_y = ($this->getHeight() / 2) - ($watermark->getHeight() / 2);
                break;

            case ImageAdapter::WATERMARK_CENTER_RIGHT :
                $watermark_x = $this->getWidth() - $margin - $watermark->getWidth();
                $watermark_y = ($this->getHeight() / 2) - ($watermark->getHeight() / 2);
                break;

            case ImageAdapter::WATERMARK_BOTTOM_LEFT :
                $watermark_x = $margin;
                $watermark_y = $this->getHeight() - $margin - $watermark->getHeight();
                break;

            case ImageAdapter::WATERMARK_BOTTOM_CENTER :
                $watermark_x = ($this->getWidth() / 2) - ($watermark->getWidth() / 2);
                $watermark_y = $this->getHeight() - $margin - $watermark->getHeight();
                break;

            case ImageAdapter::WATERMARK_BOTTOM_RIGHT :
                $watermark_x = $this->getWidth() - $margin - $watermark->getWidth();
                $watermark_y = $this->getHeight() - $margin - $watermark->getHeight();
                break;

            case ImageAdapter::WATERMARK_TOP_LEFT :
            default:
                $watermark_x = $margin;
                $watermark_y = $margin;
                break;
        }

        $watermark->opacity($opacity);

        if ($this->getMimeType() == "PNG") {
            imagealphablending($this->image, TRUE);
            $temp_img = imagecreatetruecolor($this->getWidth(), $this->getHeight());
            imagealphablending($temp_img, false);
            imagesavealpha($temp_img, true);
            imagecopy($temp_img, $this->image, 0, 0, 0, 0, $this->getWidth(), $this->getHeight());
            imagecopy($temp_img, $watermark->image, $watermark_x, $watermark_y, 0, 0, $watermark->getWidth(), $watermark->getHeight());
            imagecopy($this->image, $temp_img, 0, 0, 0, 0, $this->getWidth(), $this->getHeight());
            imagedestroy($temp_img);

        } elseif ($this->getMimeType() == "GIF") {
            $temp_img = imagecreatetruecolor($this->getWidth(), $this->getHeight());

            $transparent_index = imagecolortransparent($this->image);

            if ($transparent_index !== -1) {
                $transparent_color = imagecolorsforindex($this->image, $transparent_index);

                $transparent_destination_index = imagecolorallocate($temp_img, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                imagecolortransparent($temp_img, $transparent_destination_index);

                imagefill($temp_img, 0, 0, $transparent_destination_index);
            }

            imagecopy($temp_img, $this->image, 0, 0, 0, 0, $this->getWidth(), $this->getHeight());
            imagecopy($temp_img, $watermark->image, $watermark_x, $watermark_y, 0, 0, $watermark->getWidth(), $watermark->getHeight());
            imagecopy($this->image, $temp_img, 0, 0, 0, 0, $this->getWidth(), $this->getHeight());
            imagedestroy($temp_img);

        } else {
            imagecopy($this->image, $watermark->image, $watermark_x, $watermark_y, 0, 0, $watermark->getWidth(), $watermark->getHeight());
        }

        return $this;
    }

    public function destroy(): void
    {
        imagedestroy($this->image);
    }

    public function __destruct()
    {
        self::destroy();
    }
}
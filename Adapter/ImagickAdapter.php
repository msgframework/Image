<?php

namespace Msgframework\Lib\Image\Adapter;

use MSGFramework\Lib\Image\Exception\ImageException;
use MSGFramework\Lib\Image\Exception\ImageManipulateException;
use MSGFramework\Lib\Image\Exception\ImageNotSavedException;

class ImagickAdapter extends ImageAdapter implements ImageAdapterInterface
{
    private \Imagick $image;

    /**
     * @throws \ImagickException
     */
    function __construct($path)
    {
        parent::__construct($path);

        try {
            $this->image = new \Imagick($this->getPathName());
        } catch (\ImagickException $e) {
            throw new ImageException($e->getMessage());
        }
    }

    public function getWidth(): int
    {
        return $this->image->getImageWidth();
    }

    public function getHeight(): int
    {
        return $this->image->getImageHeight();
    }

    function resize($size = 100, int $site = 0): self
    {
        $size = explode("x", $size);

        if (count($size) == 2) {
            $size[0] = intval($size[0]);
            $size[1] = intval($size[1]);

            return $this->crop($size[0], $size[1]);
        } else {
            $size[0] = intval($size[0]);

            return $this->scale($size[0], $site);
        }
    }

    function crop(int $width, int $height): self
    {
        $width = min($width, $this->getWidth());
        $height = min($height, $this->getHeight());

        $size_ratio = max($width / $this->getWidth(), $height / $this->getHeight());

        $src_w = ceil($width / $size_ratio);
        $src_h = ceil($height / $size_ratio);

        $sx = floor(($this->getWidth() - $src_w) / 2);
        $sy = floor(($this->getHeight() - $src_h) / 2);
        try {
            $this->image->cropimage(
                intval($width),
                intval($height),
                intval($sx),
                intval($sy)
            );
        } catch (\ImagickException $e) {
            throw new ImageManipulateException($this->getPathName(), sprintf('crop to width: "%s" and height "%s"', $width, $height));
        }

        return $this;
    }

    function scale(int $size = 100, int $site = 0): self
    {
        switch ($site) {
            case self::IMAGE_SITE_WIDTH :
                if ($this->getWidth() <= $size) {
                    return $this;
                } else {
                    $width = $size;
                    $height = ($width / $this->getWidth()) * $this->getHeight();
                }

                break;

            case self::IMAGE_SITE_HEIGHT :
                if ($this->getHeight() <= $size) {
                    return $this;
                } else {
                    $height = $size;
                    $width = ($height / $this->getHeight()) * $this->getWidth();
                }

                break;

            case self::IMAGE_SITE_AUTO :
            default :
                if ($this->getWidth() >= $this->getHeight()) {
                    $width = $size;
                    $height = ($width / $this->getWidth()) * $this->getHeight();
                } else {
                    $height = $size;
                    $width = ($height / $this->getHeight()) * $this->getWidth();

                }
                break;
        }

        try {
            $this->image->resizeImage(intval($width), intval($height), \Imagick::FILTER_LANCZOS, 1);
        } catch (\ImagickException $e) {
            throw new ImageManipulateException($this->getPathName(), sprintf('resize to width: "%s" and height "%s"', $width, $height));
        }

        return $this;
    }

    function opacity(float $opacity = 1): self
    {
        try {
            $this->image->evaluateImage(\Imagick::EVALUATE_MULTIPLY, $opacity, \Imagick::CHANNEL_ALPHA);
        } catch (\ImagickException $e) {
            throw new ImageManipulateException($this->getPathName(), sprintf('set opacity to %s', $opacity));
        }

        return $this;
    }

    function quality(int $quality = 90): self
    {
        try {
            $this->image->setImageCompressionQuality($quality);
        } catch (\ImagickException $e) {
            throw new ImageManipulateException($this->getPathName(), sprintf('set quality to %s', $opacity));
        }

        return $this;
    }

    function save(string $path): void
    {
        try {
            $this->image->writeImage($path);
        } catch (\ImagickException $e) {
            throw new ImageNotSavedException($this->getPathName(), $path);
        }
    }

    function show()
    {
        try {
            header("Content-Type: image/jpg");
            echo $this->image->getImageBlob();
            $this->image->clear();
        } catch (\ImagickException $e) {
            throw new ImageException(sprintf('Can\'t show image "%s"', $this->getPathName()));
        }
    }

    function watermark(ImageAdapterInterface $watermark, int $position, int $margin, int $ratio, float $opacity = 100): self
    {
        $watermark->resize($this->getWidth() * ($ratio / 100), 1);

        $watermark->image->setImageMatte(true);
        $watermark->image->transformImageColorspace(\Imagick::COLORSPACE_SRGB);

        if ($position == 1) {
            $watermark_x = $margin;
            $watermark_y = $margin;
        } elseif ($position == 2) {
            $watermark_x = $this->getWidth() - $margin - $watermark->getWidth();
            $watermark_y = $margin;
        } elseif ($position == 3) {
            $watermark_x = $margin;
            $watermark_y = $this->getHeight() - $margin - $watermark->getHeight();
        } elseif ($position == 4) {
            $watermark_x = $this->getWidth() - $margin - $watermark->getWidth();
            $watermark_y = $this->getHeight() - $margin - $watermark->getHeight();
        } else {
            $watermark_x = ($this->getWidth() / 2) - ($watermark->getWidth() / 2);
            $watermark_y = ($this->getHeight() / 2) - ($watermark->getHeight() / 2);
        }

        $watermark->opacity($opacity);

        try {
            $this->image->compositeImage($watermark->image, \Imagick::COMPOSITE_OVER, intval($watermark_x), intval($watermark_y), \Imagick::CHANNEL_ALPHA);
        } catch (\ImagickException $e) {
            throw new ImageManipulateException($this->getPathName(), sprintf('watermarking by %s', $watermark->getPath()));
        }

        return $this;
    }

    public function destroy(): void
    {
        $this->image->clear();
    }

    public function __destruct()
    {
        self::destroy();
    }
}
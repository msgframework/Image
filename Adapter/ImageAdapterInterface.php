<?php

namespace Msgframework\Lib\Image\Adapter;

interface ImageAdapterInterface
{
    public function getWidth(): int;
    public function getHeight(): int;
    public function resize($size = 100, int $site = 0): self;
    public function crop(int $width, int $height): self;
    public function scale(int $size = 100, int $site = 0): self;
    public function opacity(float $opacity = 1): self;
    public function quality(int $quality = 90): self;
    public function save(string $path): void;
    public function show();
    public function watermark(ImageAdapterInterface $watermark, int $position, int $margin, int $ratio, float $opacity = 1);
}
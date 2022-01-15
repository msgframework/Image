<?php

namespace Msgframework\Lib\Image\Adapter;

use Msgframework\Lib\File\File;

class ImageAdapter extends File
{
    const SIDE_AUTO = 0;
    const SIDE_WIDTH = 1;
    const SIDE_HEIGHT = 2;

    const WATERMARK_TOP_LEFT = 1;
    const WATERMARK_TOP_CENTER = 2;
    const WATERMARK_TOP_RIGHT = 3;
    const WATERMARK_CENTER_LEFT = 4;
    const WATERMARK_CENTER_CENTER = 5;
    const WATERMARK_CENTER_RIGHT = 6;
    const WATERMARK_BOTTOM_LEFT = 7;
    const WATERMARK_BOTTOM_CENTER = 8;
    const WATERMARK_BOTTOM_RIGHT = 9;
}
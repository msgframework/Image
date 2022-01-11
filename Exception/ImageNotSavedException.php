<?php

namespace MSGFramework\Lib\Image\Exception;

class ImageNotSavedException extends ImageException
{
    public function __construct(string $path, string $newPath)
    {
        parent::__construct(sprintf('The image "%s" does not saved to "%s"', $path, $newPath));
    }
}

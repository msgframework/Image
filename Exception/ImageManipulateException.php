<?php

namespace MSGFramework\Lib\Image\Exception;

class ImageManipulateException extends ImageException
{
    public function __construct(string $path, string $message)
    {
        parent::__construct(sprintf('The image "%s" can\'t %s', $path, $message));
    }
}

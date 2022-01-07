<?php

namespace MSGFramework\Lib\Image\Exception;

/**
 * Thrown when a file was not found.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class ImageNotSavedException extends ImageException
{
    public function __construct(string $path, string $newPath)
    {
        parent::__construct(sprintf('The image "%s" does not saved to "%s"', $path, $newPath));
    }
}

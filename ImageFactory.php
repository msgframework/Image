<?php

namespace Msgframework\Lib\Image;

use Msgframework\Lib\Image\Adapter\ImageAdapterInterface;
use MSGFramework\Lib\Image\Exception\ImageException;

class ImageFactory
{
    public function getImage(string $path, string $adapter = 'auto'): ImageAdapterInterface
    {
        if (!file_exists($path)) {
            throw new \RuntimeException("File not found");
        }

        switch (strtolower($adapter))
        {
            case 'imagick':
            case 'imagemagick':
                if(extension_loaded('imagick') && class_exists("Imagick")) {
                    $adapterName = '\\Msgframework\\Lib\\Image\\Adapter\\ImagickAdapter';
                } else {
                    throw new \RuntimeException('Imagick extension not loaded');
                }
                break;

            case 'gd':
                if(extension_loaded('gd') && function_exists('gd_info')) {
                    $adapterName = '\\Msgframework\\Lib\\Image\\Adapter\\GDAdapter';
                } else {
                    throw new \RuntimeException('GD extension not loaded');
                }
                break;

            case 'gmagick':
                throw new \RuntimeException(sprintf('Currently unavailable, but coming soon adapter "%s"', $adapter));

            case 'auto':
                if (extension_loaded('imagick') && class_exists("Imagick"))
                {
                    $adapterName = '\\Msgframework\\Lib\\Image\\Adapter\\ImagickAdapter';
                }
                elseif(extension_loaded('gd') && function_exists('gd_info')) {
                    $adapterName = '\\Msgframework\\Lib\\Image\\Adapter\\ImagickAdapter';
                }
                break;

            default:
                throw new \RuntimeException(sprintf('Undefined adapter "%s"', $adapter));
        }

        try{
            return new $adapterName($path);
        } catch (ImageException $e) {
            throw new \RuntimeException("File not found");
        }
    }
}
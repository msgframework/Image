# The Image library

[![Latest Stable Version](http://poser.pugx.org/msgframework/image/v)](https://packagist.org/packages/msgframework/image) [![Total Downloads](http://poser.pugx.org/msgframework/image/downloads)](https://packagist.org/packages/msgframework/image) [![Latest Unstable Version](http://poser.pugx.org/msgframework/image/v/unstable)](https://packagist.org/packages/msgframework/image) [![License](http://poser.pugx.org/msgframework/image/license)](https://packagist.org/packages/msgframework/image) [![PHP Version Require](http://poser.pugx.org/msgframework/image/require/php)](https://packagist.org/packages/msgframework/image)

## About
This library help to manipulate with images: crop, resize, change quality and watermarking.

## Usage

### Load ImageFactory

``` php
use Msgframework\Lib\Image\ImageFactory;
use Msgframework\Lib\Image\Adapter\ImageAdapter;
...
// Create factory
$factory = new ImageFactory();
```

### Create Image with ImageFactory
``` php
...
$factory = new ImageFactory();

// Create ImageAdapter from $path with auto selected Adapter
$image = $factory->getImage($path);

// Create ImagickAdapter from $path
$image = $factory->getImage($path, 'Imagick');

// Create GDAdapter from $path
$image = $factory->getImage($path, 'GD');
```

### Create Image without ImageFactory

``` php
...
// Create ImagickAdapter from $path
$image = new \Msgframework\Lib\Image\Adapter\ImagickAdapter($path);

// Create GDAdapter from $path
$image = new \Msgframework\Lib\Image\Adapter\GDAdapter($path);
```

### Get Image dimensions

``` php
...
$image = $factory->getImage($path);

$image->getWidth(); // Return width in px
$image->getHeight(); // Return height in px
```

### Resize Image

``` php
...
$image = $factory->getImage($path);

$image->resize(300, ImageAdapter::SIDE_WIDTH); // Set image width 300px
...
$image->resize(500, ImageAdapter::SIDE_HEIGHT); // Set image width 500px
$image->resize(800, ImageAdapter::SIDE_AUTO); // Set image width/height 800px by longest side
```

### Scale Image
Used to scale to a given value on a specified side

``` php
...
$image = $factory->getImage($path);

$image->scale(300, ImageAdapter::SIDE_WIDTH); // Set image width 300px and scaled height
```

### Crop Image
Allows you to crop the image to a specified size without the appearance of voids

``` php
...
$image = $factory->getImage($path);

$image->crop(300, 500);
// If a similar image had dimensions of 400x400px, then the output will be an image of 300x400px
```

### Change Image opacity
Allows you to change the transparency of the image as a percentage from 0 to one hundred

``` php
...
$image = $factory->getImage($path);

$image->opacity(.5);
// Accepts float values from 0 to 1
```

### Change Image quality
Allows you to specify the image quality in percentage

``` php
...
$image = $factory->getImage($path);

$image->quality(80);
// Accepts int values from 0 to 100
```

### Save changed Image

``` php
...
$image = $factory->getImage($path);
...
$image->save($image->getPathName()); // Owerwrite Image
$image->save($newpath); // Save new Image
```

### Show Image
If you need to display modified images by link without storing them, for example, implement a preview
``` php
...
$image = $factory->getImage($path);
...
$image->resize(600, ImageAdapter::SIDE_WIDTH);
$image->show();
```

### Watermarking Image

``` php
...
$image = $factory->getImage($pathImage);
$watermark = $factory->getImage($pathWatermark);
...
$image->watermark($watermark, ImageAdapter::WATERMARK_CENTER_CENTER, 20, 70, 70);
$image->save();
```

### Watermark position codes
To position the watermark, you can use both ImageAdapter constants and codes from the table

|        | Left | Center | Right |
|--------|------|--------|-------|
| Top    | 1    | 2      | 3     |
| Center | 4    | 5      | 6     |
| Bottom | 7    | 8      | 9     |

### Destroy Image
If you have finished manipulating the image but still want to work on the file

``` php
...
$image = $factory->getImage($path);
...
$image->save()->destroy();
...
$dirPath = $image->getPath(); // Get path to image directory 
```

## Installation

You can install this package easily with [Composer](https://getcomposer.org/).

Just require the package with the following command:

    $ composer require msgframework/image

## Asset

All images used for PHPUnit tests were downloaded from the site unsplash.com
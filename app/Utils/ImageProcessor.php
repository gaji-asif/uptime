<?php

namespace App\Utils;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class ImageProcessor
{
    private $manager;
    private $source;
    private $originalImage;
    private $outputImage;

    const MAX_HEIGHT = 1000;
    const MAX_WIDTH = 1000;

    public function __construct($source)
    {
        $this->manager = new ImageManager(['driver' => 'gd']);
        $this->source = $source;
        $this->originalImage = $this->manager->make($source);
        $this->originalImage->orientate();
    }

    public function resize($height, $width)
    {
        // Make a new instance to prevent data loss
        $image = $this->manager->make($this->source);

        $originalWidth = $image->width();
        $originalHeight = $image->height();

        $isLandscape = $originalWidth > $originalHeight;
        $isPortrait = $originalWidth < $originalHeight;

        // Fallback for square images
        $finalWidth = $width;
        $finalHeight = $height;

        if ($isLandscape) {
            $finalWidth = $width;
            $finalHeight = floor($height * ($originalHeight / $originalWidth));
        }

        if ($isPortrait) {
            $finalWidth = floor($width * ($originalWidth / $originalHeight));
            $finalHeight = $height;
        }

        $image->resize($finalWidth, $finalHeight);

        return $image;
    }

    public function scale($percentage = 90)
    {
        $image = $this->resize(self::MAX_HEIGHT,self::MAX_WIDTH);

        $image->encode('jpg', $percentage);

        $this->outputImage = $image;

        return $this->outputImage;
    }

    public function save($path)
    {
        if (empty($this->outputImage)) {
            throw new \Exception('No image to process.');
        }

        if (empty($path)) {
            throw new \Exception('Image path cannot be empty.');
        }

        $directory = pathinfo($path, PATHINFO_DIRNAME);
        $filename = pathinfo($path, PATHINFO_BASENAME);

        Log::debug('directory', [$directory]);
        Log::debug('filename', [$filename]);

        $path = $directory . '/' . $filename;
        $image = $this->outputImage;

        if (! Storage::disk($this->getStorageDisk())->exists($directory)) {
            Storage::disk($this->getStorageDisk())->makeDirectory($directory);
        }

        Storage::disk($this->getStorageDisk())->put($path, $image->stream());

        Log::debug('destroying the image');
        $image->destroy();
    }

    private function getStorageDisk()
    {
        if (App::environment(['local', 'dev'])) {
            return 'public';
        }

        return 's3';
    }
}

<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageStorageService
{
    public function storeResized(UploadedFile $file, string $directory = 'listings'): string
    {
        $manager = new ImageManager(new Driver);

        $encoded = $manager->read($file->getRealPath())
            ->scaleDown(width: 1200)
            ->toJpeg(82);

        $path = $directory.'/'.(string) Str::uuid().'.jpg';

        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }
}

<?php

namespace App\Services;

use getID3;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;

class MediaServices {

    public function getDuration($file, string $mimeType): ?int
    {
        if (Str::startsWith($mimeType, 'audio') || Str::startsWith($mimeType, 'video')) {
            $getID3 = new getID3();
            $fileInfo = $getID3->analyze($file->getPathname());
            $duration = $fileInfo['playtime_seconds'] ?? 0;
        } else {
            $duration = null;
        }
        return $duration;
    }

    public function getFileType(string $mimeType): string
    {
        if (Str::startsWith($mimeType, 'image')) {
            return 'image';
        }
        
        if (Str::startsWith($mimeType, 'video')) {
            return 'video';
        }
        
        if (Str::startsWith($mimeType, 'audio')) {
            return 'audio';
        }
        
        return 'attachment';
    }





    public function makePhotoThumbnail($file, $originalPath, $mimeType): string|null
    {
        // create image manager with desired driver
        $manager = new ImageManager(new Driver());

        // read image from file system
        $image = $manager->read($file->getRealPath());

        // resize image proportionally to 300px width
        $image->scale(height: 200);

        // Kreiraj ime fajla za thumbnail
        $filename = pathinfo($originalPath, PATHINFO_FILENAME) . '_thumb.' . $file->getClientOriginalExtension();

        // Putanja unutar storage/app/public
        $thumbnailPath = 'thumbnails/' . $filename;

        // Snimi thumbnail u storage disk 'public'
        Storage::disk('private')->put($thumbnailPath, (string) $image->encode());

        // Vrati putanju thumbnail-a
        return $thumbnailPath;
    }   

    public function makeVideoThumbnail(string $relativeVideoPath, string $extension = 'jpg'): ?string
    {
        // apsolutna putanja do video fajla
        $absoluteVideoPath = storage_path('app/' . $relativeVideoPath);

        if (!file_exists($absoluteVideoPath)) {
            return null;
        }

        // thumbnails folder: storage/app/thumbnails
        $thumbnailDir = storage_path('app/thumbnails');

        if (!is_dir($thumbnailDir)) {
            mkdir($thumbnailDir, 0755, true);
        }

        // ime thumbnail-a: koristi ime originalnog fajla
        $filename = pathinfo($relativeVideoPath, PATHINFO_FILENAME) . '_thumb.' . $extension;

        // relativna putanja (za bazu)
        $thumbnailPath = 'thumbnails/' . $filename;

        // apsolutna putanja za ffmpeg
        $absoluteThumbnailPath = storage_path('app/' . $thumbnailPath);

        // uzmi frame na 1 sekundi (-ss 1)
        $cmd = sprintf(
            'ffmpeg -y -i %s -ss 00:00:01 -vframes 1 %s 2>/dev/null',
            escapeshellarg($absoluteVideoPath),
            escapeshellarg($absoluteThumbnailPath)
        );

        exec($cmd, $output, $exitCode);

        // proveri da li je FFmpeg uspeo
        if ($exitCode !== 0 || !file_exists($absoluteThumbnailPath)) {
            return null;
        }

        return $thumbnailPath;
    }
}
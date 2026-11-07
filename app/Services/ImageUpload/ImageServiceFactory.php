<?php

namespace App\Services\ImageUpload;

class ImageServiceFactory
{
    protected static array $mapping = [
        'organizer'     => \App\Services\ImageUpload\SingleImageUploadService::class,   // Create or Update
        'event'    => \App\Services\ImageUpload\SingleImageUploadService::class,   // Create or Update
        'venue' => \App\Services\ImageUpload\MultipleImageUploadService::class, // Create only
    ];

    public static function make(string $type): ImageUploadServiceInterface
    {
        return app(self::$mapping[$type]);
    }
}

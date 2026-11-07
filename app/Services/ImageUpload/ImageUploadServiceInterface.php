<?php

namespace App\Services\ImageUpload;

use App\Models\Image;
use Illuminate\Database\Eloquent\Model;
interface ImageUploadServiceInterface
{
    public function handle(Model $model, array $data): Image;
}

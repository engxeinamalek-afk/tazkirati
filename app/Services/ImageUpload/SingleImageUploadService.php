<?php

namespace App\Services\ImageUpload;

use App\Models\Image;
use Illuminate\Database\Eloquent\Model;
class SingleImageUploadService implements ImageUploadServiceInterface
{
    public function handle(Model $model, array $data):Image{
        $path = $data['image']->store('photos', 'public');
        $image= $model->image()->updateOrCreate(
                [
                    'imageable_id'   => $model->id
                ],
                ['path' => $path]
            );

        return $image;
    }
}

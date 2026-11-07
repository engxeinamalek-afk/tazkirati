<?php

namespace App\Services\ImageUpload;
use App\Models\Image;
use Illuminate\Database\Eloquent\Model;
class MultipleImageUploadService implements ImageUploadServiceInterface
{
    public function handle(Model $model, array $data):Image{
        $path = $data['image']->store('photos', 'public');
        $image= $model->images()->create([
                'path' => $path
            ]);

        return $image;
    }
}

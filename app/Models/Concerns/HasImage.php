<?php

namespace App\Models\Concerns;
use App\Models\Image;

trait HasImage
{
    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}

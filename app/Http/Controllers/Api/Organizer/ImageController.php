<?php

namespace App\Http\Controllers\Api\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreImageRequest;
use App\Http\Resources\ImageResource;
use Illuminate\Database\Eloquent\Model;
use App\Services\ImageUpload\ImageServiceFactory;
use App\Traits\ApiResponse;

class ImageController extends Controller
{
    use ApiResponse;
    public function store(StoreImageRequest $request, $imageable_type, Model $imageable){
        $data= $request->validated();
        $service = ImageServiceFactory::make($request->route('imageable_type'));
        $image= $service->handle($imageable, $data);

        // تخزين الصورة
        return $this->successResponse(new ImageResource($image), 'Image stored successfully');      
    }
}

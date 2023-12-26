<?php

namespace App\Http\Controllers;

use App\Actions\Image\CreateImageForUser;
use App\Actions\Image\DeleteImage;
use App\Actions\Image\GetAllImagesForUser;
use App\Actions\Image\UpdateImage;
use App\Http\Requests\DownloadImageRequest;
use App\Http\Requests\StoreImageRequest;
use App\Http\Requests\UpdateImageRequest;
use App\Http\Resources\ImageResource;
use App\Models\Image\Image;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Image::class, 'image');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ImageResource::collection(GetAllImagesForUser::run(auth()->user()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreImageRequest $request)
    {
        return new ImageResource(
            CreateImageForUser::run(
                auth()->user(),
                $request->validated(),
                $request->file('image'),
                $this->with()
            )
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Image $image)
    {
        return new ImageResource($image->loadMissing($this->with()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateImageRequest $request, Image $image)
    {
        return new ImageResource(
            UpdateImage::run($image, $request->validated(), $this->with())
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Image $image)
    {
        DeleteImage::run($image);
        return response()->noContent();
    }

    /**
     * Download resource file
     */
    public function download(DownloadImageRequest $request, Image $image)
    {
        $type = $request->type ?? 'original';
        return Storage::download("images/$image->id/$type-$image->name.$image->extension");
    }
}

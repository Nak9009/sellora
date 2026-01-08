<?php
namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait ImageUploadable
{
    public function uploadImage(UploadedFile $image, string $directory = 'uploads'): string
    {
        $filename = Str::random(40) . '.' . $image->getClientOriginalExtension();

        return Storage::disk('public')->putFileAs($directory, $image, $filename);
    }

    public function deleteImage(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }
}

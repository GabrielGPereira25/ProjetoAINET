<?php

namespace App\Traits;

use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait CategoryImageFileStorage
{
    public function storeCategoryImage(?UploadedFile $uploadedFile, Category $category): ?string
    {
        if ($uploadedFile) {
            $path = basename(Storage::disk('public')->putFile('categories', $uploadedFile));
            $category->image_url = $path;
            $category->save();
            return $path;
        }
        return null;
    }

    public function deleteCategoryImage(Category $category): bool
    {
        if ($category->image_url) {
            if (Storage::disk('public')->exists('categories/' . $category->image_url)) {
                Storage::disk('public')->delete('categories/' . $category->image_url);
                $category->image_url = null;
                $category->save();
                return true;
            }
            $category->image_url = null;
            $category->save();
        }
        return false;
    }

    public function deleteImageFile(?string $image_url): bool
    {
        if ($image_url !== null) {
            if (Storage::disk('public')->exists('categories/' . $image_url)) {
                Storage::disk('public')->delete('categories/' . $image_url);
                return true;
            }
        }
        return false;
    }
}

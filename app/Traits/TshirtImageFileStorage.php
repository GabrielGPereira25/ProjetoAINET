<?php

namespace App\Traits;

use App\Models\Tshirt_image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait TshirtImageFileStorage
{
    public function storeTshirtImage(UploadedFile $file, Tshirt_image $tshirtImage)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = $tshirtImage->id . '_' . uniqid() . '.' . $extension;

        if ($tshirtImage->customer_id == null) {
            $path = $file->storeAs('tshirt_images', $filename, 'public');
        } else {
            $path = $file->storeAs('tshirt_images_private', $filename, 'private');
        }

        $tshirtImage->image_url = $filename;
        $tshirtImage->save();
        return $path;
    }

    public function deleteTshirtImage(Tshirt_image $tshirtImage)
    {
        if ($tshirtImage->image_url) {
            if ($tshirtImage->customer_id == null) {
                if (Storage::disk('public')->exists("tshirt_images/{$tshirtImage->image_url}")) {
                    Storage::disk('public')->delete("tshirt_images/{$tshirtImage->image_url}");
                    return true;
                }
            } else {
                if (Storage::disk('private')->exists("tshirt_images_private/{$tshirtImage->image_url}")) {
                    Storage::disk('private')->delete("tshirt_images_private/{$tshirtImage->image_url}");
                    return true;
                }
            }
        }
        return false;
    }
}

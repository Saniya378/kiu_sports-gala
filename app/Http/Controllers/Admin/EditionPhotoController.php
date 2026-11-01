<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edition;
use App\Models\EditionPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class EditionPhotoController extends Controller
{
    /**
     * Store multiple photos for an edition.
     */
    public function store(Request $request, Edition $edition): RedirectResponse
    {
        $request->validate([
            'photos' => ['required', 'array', 'min:1'],
            'photos.*' => ['required', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:10240'],
            'sport_id' => ['nullable', 'exists:sports,id'],
            'caption' => ['nullable', 'string', 'max:255'],
        ], [
            'photos.required' => 'Please select at least one photo to upload.',
            'photos.*.image' => 'Each file must be a valid image format.',
            'photos.*.max' => 'Each photo must not exceed 10MB.',
        ]);

        $manager = new ImageManager(new Driver());
        $disk = Storage::disk('public');
        $disk->makeDirectory('editions/photos');

        $uploadedCount = 0;

        foreach ($request->file('photos') as $file) {
            $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
            $filename = Str::random(40) . '.' . $extension;
            $relativePath = 'editions/photos/' . $filename;
            $fullPath = $disk->path($relativePath);

            // Resize to max width 1200px while preserving aspect ratio
            $image = $manager->read($file->getRealPath());
            $image->scaleDown(width: 1200);
            $image->save($fullPath);

            EditionPhoto::create([
                'edition_id' => $edition->id,
                'sport_id' => $request->sport_id,
                'image' => $relativePath,
                'caption' => $request->caption,
            ]);

            $uploadedCount++;
        }

        return back()->with('success', "{$uploadedCount} edition photo(s) uploaded successfully.");
    }

    /**
     * Delete an edition photo.
     */
    public function destroy(EditionPhoto $photo): RedirectResponse
    {
        if (Storage::disk('public')->exists($photo->image)) {
            Storage::disk('public')->delete($photo->image);
        }

        $photo->delete();

        return back()->with('success', 'Edition photo deleted successfully.');
    }
}


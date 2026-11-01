<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use App\Models\MatchPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class MatchPhotoController extends Controller
{
    /**
     * Store multiple match photos for a fixture.
     */
    public function store(Request $request, Fixture $fixture): RedirectResponse
    {
        $request->validate([
            'photos' => ['required', 'array', 'min:1'],
            'photos.*' => ['required', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:10240'],
            'caption' => ['nullable', 'string', 'max:255'],
        ], [
            'photos.required' => 'Please select at least one photo to upload.',
            'photos.*.image' => 'Each file must be a valid image.',
            'photos.*.max' => 'Each photo must not exceed 10MB in size.',
        ]);

        $manager = new ImageManager(new Driver());
        $disk = Storage::disk('public');
        $disk->makeDirectory('matches');

        $uploadedCount = 0;

        foreach ($request->file('photos') as $file) {
            $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
            $filename = Str::random(40) . '.' . $extension;
            $relativePath = 'matches/' . $filename;
            $fullPath = $disk->path($relativePath);

            // Resize to max width 1200px while maintaining aspect ratio
            $image = $manager->read($file->getRealPath());
            $image->scaleDown(width: 1200);
            $image->save($fullPath);

            MatchPhoto::create([
                'fixture_id' => $fixture->id,
                'image' => $relativePath,
                'caption' => $request->caption,
                'uploaded_by' => $request->user()?->id,
            ]);

            $uploadedCount++;
        }

        return back()->with('success', "{$uploadedCount} photo(s) uploaded and saved successfully.");
    }

    /**
     * Remove the specified photo from storage and database.
     */
    public function destroy(MatchPhoto $photo): RedirectResponse
    {
        if (Storage::disk('public')->exists($photo->image)) {
            Storage::disk('public')->delete($photo->image);
        }

        $photo->delete();

        return back()->with('success', 'Match photo deleted successfully.');
    }
}

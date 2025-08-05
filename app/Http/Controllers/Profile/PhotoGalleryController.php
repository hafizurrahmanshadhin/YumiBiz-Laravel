<?php

namespace App\Http\Controllers\Profile;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditProfileRequest;
use Exception;
use Illuminate\Http\JsonResponse;

class PhotoGalleryController extends Controller {
    /**
     * Upload images to the user's photo gallery.
     *
     * @param EditProfileRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function UploadImage(EditProfileRequest $request): JsonResponse {
        try {
            $user = auth()->user();
            if (!$user) {
                return Helper::jsonResponse(false, 'User not authenticated', 401);
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $key => $image) {
                    $imageName = ($key + 1) . '_' . time() . '_' . $image->getClientOriginalName();
                    //! 1st parameter image, 2nd parameter folder name, 3rd parameter image name
                    $imagePath = Helper::fileUpload($image, 'user_images', $imageName);

                    $user->photoGalleries()->create([
                        'image' => $imagePath,
                    ]);
                }
                $data = $user->photoGalleries;
                return Helper::jsonResponse(true, 'Images uploaded successfully', 200, $data);
            }
            return Helper::jsonResponse(false, 'No images provided', 400);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete an image from the user's photo gallery.
     *
     * @param int $imageId
     * @return JsonResponse
     * @throws Exception
     */
    public function DeleteImage(int $imageId): JsonResponse {
        try {
            $user = auth()->user();
            if (!$user) {
                return Helper::jsonResponse(false, 'User not authenticated', 401);
            }

            $image = $user->photoGalleries()->find($imageId);
            if (!$image) {
                return Helper::jsonResponse(false, 'Image not found', 404);
            }

            Helper::fileDelete($image->image);
            $image->delete();

            return Helper::jsonResponse(true, 'Image deleted successfully', 200);
        } catch (Exception $e) {
            throw $e;
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AvatarUploadRequest;
use App\Services\AvatarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvatarController extends Controller
{
    protected $avatarService;

    public function __construct(AvatarService $avatarService)
    {
        $this->avatarService = $avatarService;
    }

    /**
     * Upload user avatar
     */
    public function upload(AvatarUploadRequest $request)
    {
        try {
            $user = Auth::user();

            // Upload avatar
            $avatarPath = $this->avatarService->uploadAvatar(
                $request->file('avatar'),
                $user->id
            );

            // Update user record
            $user->update(['avatar' => $avatarPath]);

            return response()->json([
                'success' => true,
                'message' => 'Avatar uploaded successfully',
                'data' => [
                    'avatar_path' => $avatarPath,
                    'avatar_url' => $this->avatarService->getAvatarUrl($avatarPath),
                    'user' => $user->fresh()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload avatar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete user avatar
     */
    public function delete()
    {
        try {
            $user = Auth::user();

            // Delete avatar file
            $this->avatarService->deleteOldAvatar($user->id);

            // Update user record
            $user->update(['avatar' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Avatar deleted successfully',
                'data' => [
                    'user' => $user->fresh()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete avatar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user avatar
     */
    public function show($userId = null)
    {
        try {
            $user = $userId ? \App\Models\User::findOrFail($userId) : Auth::user();

            return response()->json([
                'success' => true,
                'data' => [
                    'avatar_path' => $user->avatar,
                    'avatar_url' => $this->avatarService->getAvatarUrl($user->avatar),
                    'user_id' => $user->id,
                    'user_name' => $user->name
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}

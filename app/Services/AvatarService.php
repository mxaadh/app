<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AvatarService
{
    public function uploadAvatar(UploadedFile $file, $userId)
    {
        // Delete old avatar if exists
        $this->deleteOldAvatar($userId);

        // Generate unique filename with original extension
        $extension = $file->getClientOriginalExtension();
        $filename = 'avatar_' . $userId . '_' . time() . '.' . $extension;

        // Store the image directly without processing
        $path = $file->storeAs('avatars', $filename, 'public');

        return $path;
    }

    public function deleteOldAvatar($userId)
    {
        $user = \App\Models\User::find($userId);

        if ($user && $user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
    }

    public function getAvatarUrl($avatarPath)
    {
        if (!$avatarPath) {
            return asset('images/default-avatar.jpg'); // Default avatar
        }

        return Storage::disk('public')->url($avatarPath);
    }
}

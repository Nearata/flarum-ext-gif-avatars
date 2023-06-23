<?php

namespace Nearata\GifAvatars\User;

use Flarum\User\User;
use Illuminate\Support\Str;
use Laminas\Diactoros\UploadedFile;

class AvatarUploader extends \Flarum\User\AvatarUploader
{
    public function uploadGif(User $user, UploadedFile $file)
    {
        $avatarPath = Str::random().'.gif';

        $this->removeFileAfterSave($user);
        $user->changeAvatarPath($avatarPath);

        $this->uploadDir->put($avatarPath, $file->getStream());
    }
}

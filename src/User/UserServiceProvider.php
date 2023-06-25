<?php

namespace Nearata\GifAvatars\User;

use Flarum\Foundation\AbstractServiceProvider;
use Flarum\User\Command\UploadAvatarHandler;

class UserServiceProvider extends AbstractServiceProvider
{
    public function boot()
    {
        $this->container
            ->when(UploadAvatarHandler::class)
            ->needs(\Flarum\User\AvatarUploader::class)
            ->give(AvatarUploader::class);
    }
}

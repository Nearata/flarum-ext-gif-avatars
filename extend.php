<?php

namespace Nearata\GifAvatars;

use Flarum\Extend;
use Nearata\GifAvatars\Api\Controller\UploadAvatarController;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/less/forum.less'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/less/admin.less'),

    new Extend\Locales(__DIR__.'/locale'),

    (new Extend\Routes('api'))
        ->remove('users.avatar.upload')
        ->post('/users/{id}/avatar', 'users.avatar.upload', UploadAvatarController::class),
];

<?php

namespace Nearata\GifAvatars\Api\Controller;

use Flarum\Http\RequestUtil;
use Flarum\User\AvatarValidator;
use Flarum\User\UserRepository;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Intervention\Image\ImageManager;
use Laminas\Diactoros\UploadedFile;
use Nearata\GifAvatars\User\AvatarUploader;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class UploadAvatarController extends \Flarum\Api\Controller\UploadAvatarController
{
    public function __construct(
        protected Dispatcher $dispatcher,
        protected ImageManager $imageManager,
        protected UserRepository $users,
        protected AvatarUploader $uploader,
        protected AvatarValidator $validator
    ) {
        parent::__construct($dispatcher);
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $userId = Arr::get($request->getQueryParams(), 'id');
        $actor = RequestUtil::getActor($request);

        $file = Arr::get($request->getUploadedFiles(), 'avatar');

        $this->validator->assertValid(['avatar' => $file]);

        if (! $this->isGif($file)) {
            return parent::data($request, $document);
        }

        $user = $this->users->findOrFail($userId);

        $this->uploader->uploadGif($user, $file);

        $user->save();

        return $user;
    }

    /**
     * ref: https://github.com/sindresorhus/is-gif/blob/main/index.js
     *
     * checks the signature
     */
    private function isGif(UploadedFile $file): bool
    {
        $buffer = array_values(unpack('C3', $file->getStream()->read(3)));

        if (! $buffer || count($buffer) < 3) {
            return false;
        }

        return $buffer[0] === 0x47
            && $buffer[1] === 0x49
            && $buffer[2] === 0x46;
    }
}

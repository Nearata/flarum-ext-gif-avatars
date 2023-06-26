<?php

namespace Nearata\GifAvatars\User;

use Flarum\User\User;
use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Support\Str;
use Intervention\Image\Image;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;

class AvatarUploader extends \Flarum\User\AvatarUploader
{
    public function __construct(
        protected Factory $filesystemFactory,
        protected LoggerInterface $logger)
    {
        parent::__construct($filesystemFactory);
    }

    public function upload(User $user, Image $image)
    {
        if ($image->mime() !== 'image/gif' || $user->cannot('nearata-gif-avatars.use-gifs')) {
            parent::upload($user, $image);

            return;
        }

        $path = $image->basePath();

        $this->gifsicle($path, $path);

        $avatarPath = Str::random().'.gif';

        $this->removeFileAfterSave($user);
        $user->changeAvatarPath($avatarPath);

        $this->uploadDir->put($avatarPath, @file_get_contents($path));
    }

    private function gifsicle(string $path)
    {
        $process = Process::fromShellCommandline('gifsicle --version');
        $process->run();

        if (! $process->isSuccessful()) {
            return;
        }

        $process = Process::fromShellCommandline("gifsicle --resize-fit 100x100 $path -o $path");
        $process->run();

        if (! $process->isSuccessful()) {
            $this->logger->warning('[nearata/flarum-ext-gif-avatars] :: '.$process->getErrorOutput());

            return;
        }
    }
}

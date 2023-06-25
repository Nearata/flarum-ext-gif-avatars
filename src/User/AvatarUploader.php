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
        if ($image->mime() !== 'image/gif') {
            parent::upload($user, $image);

            return;
        }

        $from = $image->basePath();
        $to = str_replace($image->extension, 'gif', $from);

        $this->gifsicle($from, $to);

        $avatarPath = Str::random().'.gif';

        $this->removeFileAfterSave($user);
        $user->changeAvatarPath($avatarPath);

        $this->uploadDir->put($avatarPath, @file_get_contents($to));
    }

    private function gifsicle(string $from, string $to)
    {
        $process = Process::fromShellCommandline('gifsicle --version');
        $process->run();

        if (! $process->isSuccessful()) {
            return;
        }

        $process = Process::fromShellCommandline("gifsicle --resize-fit 100x100 $from -o $to");
        $process->run();

        if (! $process->isSuccessful()) {
            $this->logger->warning('[nearata/flarum-ext-gif-avatars] :: '.$process->getErrorOutput());

            return;
        }
    }
}

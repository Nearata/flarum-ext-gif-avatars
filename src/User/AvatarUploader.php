<?php

namespace Nearata\GifAvatars\User;

use Flarum\User\User;
use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Support\Str;
use Laminas\Diactoros\UploadedFile;
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

    public function uploadGif(User $user, UploadedFile $file)
    {
        $avatarPath = Str::random().'.gif';

        $this->removeFileAfterSave($user);
        $user->changeAvatarPath($avatarPath);

        $this->uploadDir->put($avatarPath, $file->getStream());

        $path = (string) $this->uploadDir->path($avatarPath);

        $this->gifsicle($path);
    }

    /**
     * never used, kept for reference
     */
    private function imageMagick(string $path)
    {
        $process = Process::fromShellCommandline('magick --version');
        $process->run();

        if (! $process->isSuccessful()) {
            $this->log($process);

            return;
        }

        $process = Process::fromShellCommandline("magick mogrify -coalesce $path");
        $process->run();

        if (! $process->isSuccessful()) {
            $this->log($process);

            return;
        }

        $process = Process::fromShellCommandline('magick mogrify -resize "100x100>" '.$path);
        $process->run();

        if (! $process->isSuccessful()) {
            $this->log($process);

            return;
        }
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
            $this->log($process);

            return;
        }
    }

    private function log(Process $process)
    {
        $this->logger->warning('[nearata/flarum-ext-gif-avatars] :: '.$process->getErrorOutput());
    }
}

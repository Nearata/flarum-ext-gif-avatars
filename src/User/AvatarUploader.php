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

        $this->imagickResize($path, $path);

        $avatarPath = Str::random().'.gif';

        $this->removeFileAfterSave($user);
        $user->changeAvatarPath($avatarPath);

        $this->uploadDir->put($avatarPath, @file_get_contents($path));
    }

    private function imagickResize(string $path)
    {
        try {
            // Create new Imagick object
            $imagick = new \Imagick($path);

            // Coalesce the gif to ensure correct colors across frames
            $imagick = $imagick->coalesceImages();

            // Resize all frames
            foreach($imagick as $frame){
                // resize image in each frame
                $frame->thumbnailImage(100, 100, true, true);
                
                // Set the page of the frame to ensure the size and offsets are correct
                $frame->setImagePage(100, 100, 0, 0);
            }

            // Optimize the gif for space
            $imagick = $imagick->optimizeImageLayers();

            // Write all frames to disk
            $imagick->writeImages($path, true);
        } catch (\ImagickException $e) {
            $this->logger->warning('[nearata/flarum-ext-gif-avatars] :: ' . $e->getMessage());
        }
    }
}

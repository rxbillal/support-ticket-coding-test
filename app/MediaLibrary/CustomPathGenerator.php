<?php

namespace App\MediaLibrary;

use App\Models\FAQ;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\TicketReplay;
use App\Models\User;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

/**
 * Class CustomPathGenerator
 */
class CustomPathGenerator implements PathGenerator
{
    /**
     * @param  Media  $media
     *
     * @return string
     */
    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media).'thumbnails/';
    }

    /**
     * @param  Media  $media
     *
     * @return string
     */
    public function getPath(Media $media): string
    {
        $path = '{PARENT_DIR}'.DIRECTORY_SEPARATOR.$media->id.DIRECTORY_SEPARATOR;

        switch ($media->collection_name) {
            case User::PROFILE:
                return str_replace('{PARENT_DIR}', 'profile-pictures', $path);
            case Setting::PATH:
                return str_replace('{PARENT_DIR}', 'settings', $path);
            case Ticket::COLLECTION_TICKET:
                return str_replace('{PARENT_DIR}', 'tickets', $path);
            case TicketReplay::COLLECTION_TICKET:
                return str_replace('{PARENT_DIR}', 'tickets_reply', $path);
            case FAQ::FaqImg:
                return str_replace('{PARENT_DIR}', FAQ::FaqImg, $path);
            case 'default':
                return '';
        }
    }

    /**
     * @param  Media  $media
     *
     * @return string
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media).'rs-images/';
    }
}

<?php

declare(strict_types=1);

namespace App\Shared\Enum;

enum SocialNetworkEnum: string
{
    case FACEBOOK = 'facebook';
    case BLUESKY = 'bluesky';
    case INSTAGRAM = 'instagram';
    case LINKEDIN = 'linkedin';
    case X = 'x';
    case MASTODON = 'mastodon';
    case TIKTOK = 'tiktok';
    case YOUTUBE = 'youtube';
    case TWITCH = 'twitch';
    case GITHUB = 'github';

    /**
     * @return array<string, string>
     */
    public static function toArray(): array
    {
        $networks = [];

        foreach (self::cases() as $network) {
            $networks[$network->name] = $network->value;
        }

        return $networks;
    }

    /**
     * @return array<string, string>
     */
    public static function toLabelArray(): array
    {
        $networks = [];

        foreach (self::cases() as $network) {
            $networks[$network->name] = ucfirst($network->value);
        }

        asort($networks);

        return $networks;
    }

    public static function getIcon(self $network): string
    {
        return match ($network) {
            self::FACEBOOK => 'ph:facebook-logo-light.svg',
            self::BLUESKY => 'ph:butterfly-light.svg',
            self::INSTAGRAM => 'ph:instagram-logo-light.svg',
            self::LINKEDIN => 'ph:linkedin-logo-light.svg',
            self::X => 'ph:x-logo-light.svg',
            self::MASTODON => 'ph:mastodon-logo-light.svg',
            self::TIKTOK => 'ph:tiktok-logo-light.svg',
            self::YOUTUBE => 'ph:youtube-logo-light.svg',
            self::TWITCH => 'ph:twitch-logo-light.svg',
            self::GITHUB => 'ph:github-logo-light.svg',
        };
    }
}

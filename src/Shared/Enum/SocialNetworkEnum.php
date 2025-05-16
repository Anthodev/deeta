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
    case WEB = 'web';

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
            self::FACEBOOK => 'ph:facebook-logo-light',
            self::BLUESKY => 'ph:butterfly-light',
            self::INSTAGRAM => 'ph:instagram-logo-light',
            self::LINKEDIN => 'ph:linkedin-logo-light',
            self::X => 'ph:x-logo-light',
            self::MASTODON => 'ph:mastodon-logo-light',
            self::TIKTOK => 'ph:tiktok-logo-light',
            self::YOUTUBE => 'ph:youtube-logo-light',
            self::TWITCH => 'ph:twitch-logo-light',
            self::GITHUB => 'ph:github-logo-light',
            default => 'ph:globe-light',
        };
    }

    public static function getIconFromName(string $name): string
    {
        return self::getIcon(self::from($name));
    }

    /**
     * @return string[]
     */
    public static function getColorFromName(string $name): array
    {
        return match (self::from($name)) {
            self::FACEBOOK => ['bg' => '#1A77F2', 'text' => '#fff'],
            self::BLUESKY, self::LINKEDIN => ['bg' => '#0077B5', 'text' => '#fff'],
            self::INSTAGRAM => ['bg' => '#E4405F', 'text' => '#fff'],
            self::X => ['bg' => '#1DA1F2', 'text' => '#fff'],
            self::MASTODON => ['bg' => '#3088D4', 'text' => '#fff'],
            self::TIKTOK => ['bg' => '#000000', 'text' => '#fff'],
            self::YOUTUBE => ['bg' => '#FF0000', 'text' => '#fff'],
            self::TWITCH => ['bg' => '#6441A5', 'text' => '#fff'],
            self::GITHUB => ['bg' => '#24292e', 'text' => '#fff'],
            default => ['bg' => '#fff', 'text' => '#000'],
        };
    }
}

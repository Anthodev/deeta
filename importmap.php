<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    '@symfony/ux-live-component' => [
        'path' => './vendor/symfony/ux-live-component/assets/dist/live_controller.js',
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    'daisyui' => [
        'version' => '5.0.0-beta.9',
    ],
    'daisyui/daisyui.min.css' => [
        'version' => '5.0.0-beta.9',
        'type' => 'css',
    ],
    'postcss' => [
        'version' => '8.5.3',
    ],
    'picocolors' => [
        'version' => '1.1.1',
    ],
    'nanoid/non-secure' => [
        'version' => '3.3.8',
    ],
    '@tailwindcss/postcss' => [
        'version' => '4.0.9',
    ],
    '@catppuccin/daisyui' => [
        'version' => '1.2.1',
    ],
    '@catppuccin/palette' => [
        'version' => '1.7.1',
    ],
    '@catppuccin/daisyui/dist/catppuccin.min.css' => [
        'version' => '1.2.1',
        'type' => 'css',
    ],
    '@hotwired/turbo' => [
        'version' => '7.3.0',
    ],
];

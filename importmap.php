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
    '@kurkle/color' => [
        'version' => '0.3.4',
    ],
    'canvas-confetti' => [
        'version' => '1.9.3',
    ],
    '@phosphor-icons/web' => [
        'version' => '2.1.2',
    ],
    'leaflet' => [
        'version' => '1.9.4',
    ]
];

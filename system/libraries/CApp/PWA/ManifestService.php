<?php

class CApp_PWA_ManifestService {
    public function generate($startUrl) {
        $basicManifest = [
            'name' => CF::config('pwa.manifest.name'),
            'short_name' => CF::config('pwa.manifest.short_name'),
            'start_url' => $startUrl,
            'display' => CF::config('pwa.manifest.display'),
            'theme_color' => CF::config('pwa.manifest.theme_color'),
            'background_color' => CF::config('pwa.manifest.background_color'),
            'orientation' => CF::config('pwa.manifest.orientation'),
            'status_bar' => CF::config('pwa.manifest.status_bar'),
            'splash' => CF::config('pwa.manifest.splash')
        ];

        foreach (CF::config('pwa.manifest.icons') as $size => $file) {
            $fileInfo = pathinfo($file['path']);
            $basicManifest['icons'][] = [
                'src' => $file['path'],
                'type' => 'image/' . $fileInfo['extension'],
                'sizes' => (isset($file['sizes'])) ? $file['sizes'] : $size,
                'purpose' => $file['purpose']
            ];
        }
        if (CF::config('pwa.manifest.shortcuts')) {
            foreach (CF::config('pwa.manifest.shortcuts') as $shortcut) {
                if (array_key_exists('icons', $shortcut)) {
                    $fileInfo = pathinfo($shortcut['icons']['src']);
                    $icon = [
                        'src' => $shortcut['icons']['src'],
                        'type' => 'image/' . $fileInfo['extension'],
                        'purpose' => $shortcut['icons']['purpose']
                    ];
                    if (isset($shortcut['icons']['sizes'])) {
                        $icon['sizes'] = $shortcut['icons']['sizes'];
                    }
                } else {
                    $icon = [];
                }

                $basicManifest['shortcuts'][] = [
                    'name' => c::trans($shortcut['name']),
                    'description' => c::trans($shortcut['description']),
                    'url' => $shortcut['url'],
                    'icons' => [
                        $icon
                    ]
                ];
            }
        }

        foreach (CF::config('pwa.manifest.custom') as $tag => $value) {
            $basicManifest[$tag] = $value;
        }

        return $basicManifest;
    }
}

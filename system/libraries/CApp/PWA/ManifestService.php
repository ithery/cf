<?php

class CApp_PWA_ManifestService {
    use CApp_PWA_Trait_GroupConfigTrait;

    protected $group;

    public function __construct($group) {
        $this->group = $group;
    }

    public function generate() {
        $basicManifest = [
            'name' => $this->getGroupConfig('manifest.name'),
            'short_name' => $this->getGroupConfig('manifest.short_name'),
            'description' => $this->getGroupConfig('manifest.description', ''),
            'start_url' => $this->getGroupConfig('manifest.start_url'),
            'display' => $this->getGroupConfig('manifest.display'),
            'theme_color' => $this->getGroupConfig('manifest.theme_color'),
            'background_color' => $this->getGroupConfig('manifest.background_color'),
            'orientation' => $this->getGroupConfig('manifest.orientation'),
            'status_bar' => $this->getGroupConfig('manifest.status_bar'),
            'scope' => $this->getGroupConfig('manifest.scope'),
            'dir' => $this->getGroupConfig('manifest.dir'),
            'lang' => $this->getGroupConfig('manifest.lang'),
            'display_override' => $this->getGroupConfig('manifest.display_override', []),
            'categories' => $this->getGroupConfig('manifest.categories'),
        ];
        foreach ($this->getGroupConfig('manifest.icons') as $size => $file) {
            $fileInfo = pathinfo($file['path']);
            $basicManifest['icons'][] = [
                'src' => $file['path'],
                'type' => 'image/' . $fileInfo['extension'],
                'sizes' => (isset($file['sizes'])) ? $file['sizes'] : $size,
                'purpose' => $file['purpose']
            ];
        }
        if ($this->getGroupConfig('manifest.shortcuts')) {
            foreach ($this->getGroupConfig('manifest.shortcuts') as $shortcut) {
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

        foreach ($this->getGroupConfig('manifest.custom') as $tag => $value) {
            $basicManifest[$tag] = $value;
        }

        return $basicManifest;
    }
}

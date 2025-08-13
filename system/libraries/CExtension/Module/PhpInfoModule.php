<?php

class CExtension_Module_PhpInfoModule extends CExtension_ModuleAbstract {
    public $name = 'phpinfo';

    public $views = __DIR__ . '/../resources/views';

    public $menu = [
        'title' => 'PHP info',
        'path' => 'phpinfo',
        'icon' => 'fa-exclamation',
    ];

    public function toCollection() {
        $what = $this->config('what', INFO_ALL);

        ob_start();

        phpinfo($what);

        $phpinfo = ['phpinfo' => c::collect()];

        if (preg_match_all('#(?:<h2>(?:<a name=".*?">)?(.*?)(?:</a>)?</h2>)|(?:<tr(?: class=".*?")?><t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>)?)?</tr>)#s', ob_get_clean(), $matches, PREG_SET_ORDER)) {
            c::collect($matches)->each(function ($match) use (&$phpinfo) {
                if (strlen($match[1])) {
                    $phpinfo[$match[1]] = c::collect();
                } elseif (isset($match[3])) {
                    $keys = array_keys($phpinfo);

                    $phpinfo[end($keys)][$match[2]] = isset($match[4]) ? c::collect([$match[3], $match[4]]) : $match[3];
                } else {
                    $keys = array_keys($phpinfo);

                    $phpinfo[end($keys)][] = $match[2];
                }
            });
        }

        ob_end_clean();

        return c::collect($phpinfo);
    }
}

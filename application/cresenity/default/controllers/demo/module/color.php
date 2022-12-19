<?php

class Controller_Demo_Module_Color extends \Cresenity\Demo\Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $app = c::app();
        $app->title('Color');

        $app->addH5('Color Format');
        $color = CColor::create('#CC131F');

        $app->addDiv()->add($this->createColorBadge($color));
        $app->addDiv()->add('is Dark:' . CDebug::dumper()->getDump($color->isDark()));

        $app->addDiv()->add('is Light:' . CDebug::dumper()->getDump($color->isLight()));

        $app->addDiv()->add('RGB Values:' . CDebug::dumper()->getDump($color->toRgb()->values()));
        $app->addDiv()->add('RGBA Values:' . CDebug::dumper()->getDump($color->toRgba()->values()));
        $app->addDiv()->add('Hex Values:' . CDebug::dumper()->getDump($color->toHex()->values()));
        $app->addDiv()->add('Hexa Values:' . CDebug::dumper()->getDump($color->toHexa()->values()));
        $app->addDiv()->add('HSL Values:' . CDebug::dumper()->getDump($color->toHsl()->values()));
        $app->addDiv()->add('HSLA Values:' . CDebug::dumper()->getDump($color->toHsla()->values()));
        $app->addDiv()->add('HSV Values:' . CDebug::dumper()->getDump($color->toHsv()->values()));
        $app->addDiv()->add('Tint 50%:' . $this->createColorBadge($color->tint(50)));
        $app->addDiv()->add('Lighten 20%:' . $this->createColorBadge($color->lighten(20)));
        $app->addDiv()->add('Darken 20%:' . $this->createColorBadge($color->darken(20)));
        $app->addDiv()->add('Brighten 20%:' . $this->createColorBadge($color->brighten(20)));
        $app->addDiv()->add('Desaturate 20%:' . $this->createColorBadge($color->desaturate(20)));
        $app->addDiv()->add('fadeOut 20%:' . $this->createColorBadge($color->fadeOut(20)));
        $app->addDiv()->add('fadeIn 20%:' . $this->createColorBadge($color->fadeIn(20)));
        $app->addDiv()->add('Grayscale:' . $this->createColorBadge($color->grayscale()));

        return $app;
    }

    protected function createColorBadge(CColor_FormatAbstract $color) {
        $backgroundColor = '#' . implode('', $color->toHex()->values());
        $foregroundColor = $color->isDark() ? '#ffffff' : '#000000';
        return '<span class="badge" style="color:'.$foregroundColor.'!important;background-color:'.$backgroundColor.'!important">'.$backgroundColor.'</span>';
    }
}

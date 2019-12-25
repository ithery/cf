<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CEmail_Builder_Renderer {

    protected $content;
    protected $options;

    public function __construct($content, $options) {
        $this->content = $content;
        $this->options = $options;
    }

    public function getOption($key, $defaultValue = null) {
        return carr::get($this->options, $key, $defaultValue);
    }

    public function render() {
        $lang = $this->getOption('lang');
        $langAttribute = '';
        if (strlen($lang) > 0) {
            $langAttribute = 'lang="' . $lang . '" ';
        }
        $backgroundColor = $this->getOption('backgroundColor');
        $backgroundColorAttribute = '';
        if (strlen($backgroundColor) > 0) {
            $backgroundColorAttribute = 'style="background-color:' . $backgroundColor . ';';
        }
        $title = $this->getOption('title', '');
        $breakpoint = $this->getOption('breakpoint', '480px');
//        $componentHeadStyleHtml = carr::reduce($this->getOption('componentHeadStyle',[]), function($result,$compHeadStyle) use($breakpoint){
//            return $result."\n".$compHeadStyle($breakpoint);
//        });
//        ${reduce(
//          componentsHeadStyle,
//          (result, compHeadStyle) => `${result}\n${compHeadStyle(breakpoint)}`,
//          '',
//        )}
        $componentHeadStyleHtml = '';
        $headStyleHtml = '';
        $styleHtml = implode('', $this->getOption('style', []));
        $headRaw = $this->getOption('headRaw');
        $headRawHtml = '';
        if ($headRaw != null) {
            $headRaw = carr::filter($headRaw, function($item) {
                        return $item != null;
                    });
            $headRawHtml .= implode("\n", $headRaw);
        }


        return '
    <!doctype html>
    <html ' . $langAttribute . 'xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
      <head>
        <title>
          ' . $title . '
        </title>
        <!--[if !mso]><!-- -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!--<![endif]-->
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style type="text/css">
          #outlook a { padding:0; }
          body { margin:0;padding:0;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%; }
          table, td { border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt; }
          img { border:0;height:auto;line-height:100%; outline:none;text-decoration:none;-ms-interpolation-mode:bicubic; }
          p { display:block;margin:13px 0; }
        </style>
        <!--[if mso]>
        <xml>
        <o:OfficeDocumentSettings>
          <o:AllowPNG/>
          <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
        </xml>
        <![endif]-->
        <!--[if lte mso 11]>
        <style type="text/css">
          .mj-outlook-group-fix { width:100% !important; }
        </style>
        <![endif]-->
        ' . $this->buildFontTags() . '
        ' . $this->buildMediaQueriesTags() . '
        <style type="text/css">
        ' . $componentHeadStyleHtml . '
        ' . $headStyleHtml . '
        ' . $styleHtml . '    
        </style>
        ' . $headRawHtml . '
      </head>
      <body' . $backgroundColorAttribute . '>
        ' . $this->buildPreview() . '
        ' . $this->content . '
      </body>
    </html>
  ';
    }

    public function buildPreview() {
        $content = $this->getOption('preview', '');
        if ($content === '') {
            return '';
        }

        return '
    <div style="display:none;font-size:1px;color:#ffffff;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;">
      ' . $content . '
    </div>
  ';
    }

    public function buildFontTags() {
        $content = $this->content;
        $fonts = $this->getOption('fonts', []);
        $inlineStyle = $this->getOption('inlineStyle', '');
        $toImport = [];
        foreach ($fonts as $name => $url) {

            $regex = '#"[^"]*font-family:[^"]*' . $name . '[^"]*"#mi';
            $inlineRegex = '#font-family:[^;}]*' . $name . '#mi';
            $inlineCallback = function($s) use ($inlineRegex) {

                return preg_match($inlineRegex, $s);
            };
            if (preg_match($regex, $this->content) || carr::some($inlineStyle, $inlineCallback)) {
                $toImport[] = $url;
            }
        }


        if (count($toImport) > 0) {
            $toImportLink = implode("\n", carr::map($toImport, function($url) {
                        return '<link href="' . $url . '" rel="stylesheet" type="text/css">';
                    }));
            $toImportStyle = implode("\n", carr::map($toImport, function($url) {
                        return '@import url(' . $url . ');';
                    }));

            return '
      <!--[if !mso]><!-->
        ' . $toImportLink . '
        <style type="text/css">
          ' . $toImportStyle . '
        </style>
      <!--<![endif]-->\n
    ';
        }

        return '';
    }

    public function buildMediaQueriesTags() {
        $breakpoint = $this->getOption('breakpoint');
        $mediaQueries = $this->getOption('mediaQueries', []);
        $forceOWADesktop = $this->getOption('forceOWADesktop', false);
        if (count($mediaQueries) == 0) {
            return '';
        }
        $baseMediaQueries = carr::map($mediaQueries, function($mediaQuery, $className) {
                    return '.' . $className . ' ' . $mediaQuery;
                });
        $owaStyle = '';
        if ($forceOWADesktop) {
            $owaQueries = carr::map($baseMediaQueries, function($mq) {
                        return '[owa] ' . $mq;
                    });
            $owaStyle = '<style type="text/css">\n' . implode("\n", $owaStyle) . '\n</style>';
        }
        $baseMediaQueriesStyle = implode("\n", $baseMediaQueries);


        return '
    <style type="text/css">
      @media only screen and (min-width:${breakpoint}) {
        ' . $baseMediaQueriesStyle . '
      }
    </style>
    ' . $owaStyle . '
  ';
    }

}

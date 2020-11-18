<?php

/**
 * Description of DevCloud
 *
 * @author Hery
 */
class CDevSuite_Windows_DevCloud extends CDevSuite_Devcloud {

    protected $requiredFiles = [
        'winsw.exe',
        'php71.ps1',
        'php72.ps1',
        'php73.ps1',
        'php74.ps1',
        'ngrok.exe',
        'configuredns.bat',
        'nginx/nginx.exe',
        'nginx/conf/fastcgi.conf',
        'nginx/conf/koi-utf',
        'nginx/conf/koi-win',
        'nginx/conf/mime.types',
        'nginx/conf/scgi_params',
        'nginx/conf/uwsgi_params',
        'nginx/conf/win-utf',
        'nginx/contrib/README',
        'nginx/contrib/geo2nginx.pl',
        'nginx/contrib/vim/ftdetect/nginx.vim',
        'nginx/contrib/vim/ftplugin/nginx.vim',
        'nginx/contrib/vim/indent/nginx.vim',
        'nginx/contrib/vim/syntax/nginx.vim',
        'nginx/contrib/unicode2nginx/koi-utf',
        'nginx/contrib/unicode2nginx/unicode-to-nginx',
        'nginx/contrib/unicode2nginx/win-utf',
        'nginx/docs/LICENSE',
        'nginx/docs/OpenSSL.LICENSE',
        'nginx/docs/PCRE.LICENSE',
        'nginx/docs/README',
        'nginx/docs/zlib.LICENSE',
        'nginx/html/50x.html',
        'nginx/html/index.html',
        //acrylic
        'acrylic/AcrylicConfiguration.ini',
        'acrylic/AcrylicConsole.exe',
        'acrylic/AcrylicService.exe',
        'acrylic/AcrylicUI.exe',
        'acrylic/AcrylicUI.exe.manifest',
        'acrylic/License.txt',
        'acrylic/Readme.txt',
    ];

}

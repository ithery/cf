<!doctype html>
<html class="theme-<?php echo $theme; ?>">
<!--
<?php echo $throwableString;?>
-->
<head>
    <!-- Hide dumps asap -->
    <style>
        pre.sf-dump {
            display: none !important;
        }
    </style>

    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex, nofollow">

    <title><?php echo $title; ?></title>
    <link href="<?php echo '/media/css/cresenity-exception.css?' . uniqid(); ?>" rel="stylesheet" />
</head>
<body class="scrollbar-lg">
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;"><symbol id="arrow-down-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M413.1 222.5l22.2 22.2c9.4 9.4 9.4 24.6 0 33.9L241 473c-9.4 9.4-24.6 9.4-33.9 0L12.7 278.6c-9.4-9.4-9.4-24.6 0-33.9l22.2-22.2c9.5-9.5 25-9.3 34.3.4L184 343.4V56c0-13.3 10.7-24 24-24h32c13.3 0 24 10.7 24 24v287.4l114.8-120.5c9.3-9.8 24.8-10 34.3-.4z"></path></symbol><symbol id="arrow-up-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M34.9 289.5l-22.2-22.2c-9.4-9.4-9.4-24.6 0-33.9L207 39c9.4-9.4 24.6-9.4 33.9 0l194.3 194.3c9.4 9.4 9.4 24.6 0 33.9L413 289.4c-9.5 9.5-25 9.3-34.3-.4L264 168.6V456c0 13.3-10.7 24-24 24h-32c-13.3 0-24-10.7-24-24V168.6L69.2 289.1c-9.3 9.8-24.8 10-34.3.4z"></path></symbol><symbol id="clipboard-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M336 64h-80c0-35.3-28.7-64-64-64s-64 28.7-64 64H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48zM192 40c13.3 0 24 10.7 24 24s-10.7 24-24 24-24-10.7-24-24 10.7-24 24-24zm144 418c0 3.3-2.7 6-6 6H54c-3.3 0-6-2.7-6-6V118c0-3.3 2.7-6 6-6h42v36c0 6.6 5.4 12 12 12h168c6.6 0 12-5.4 12-12v-36h42c3.3 0 6 2.7 6 6z"></path></symbol><symbol id="lightbulb-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512"><path d="M176 80c-52.94 0-96 43.06-96 96 0 8.84 7.16 16 16 16s16-7.16 16-16c0-35.3 28.72-64 64-64 8.84 0 16-7.16 16-16s-7.16-16-16-16zM96.06 459.17c0 3.15.93 6.22 2.68 8.84l24.51 36.84c2.97 4.46 7.97 7.14 13.32 7.14h78.85c5.36 0 10.36-2.68 13.32-7.14l24.51-36.84c1.74-2.62 2.67-5.7 2.68-8.84l.05-43.18H96.02l.04 43.18zM176 0C73.72 0 0 82.97 0 176c0 44.37 16.45 84.85 43.56 115.78 16.64 18.99 42.74 58.8 52.42 92.16v.06h48v-.12c-.01-4.77-.72-9.51-2.15-14.07-5.59-17.81-22.82-64.77-62.17-109.67-20.54-23.43-31.52-53.15-31.61-84.14-.2-73.64 59.67-128 127.95-128 70.58 0 128 57.42 128 128 0 30.97-11.24 60.85-31.65 84.14-39.11 44.61-56.42 91.47-62.1 109.46a47.507 47.507 0 0 0-2.22 14.3v.1h48v-.05c9.68-33.37 35.78-73.18 52.42-92.16C335.55 260.85 352 220.37 352 176 352 78.8 273.2 0 176 0z"></path></symbol><symbol id="pencil-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M497.9 142.1l-46.1 46.1c-4.7 4.7-12.3 4.7-17 0l-111-111c-4.7-4.7-4.7-12.3 0-17l46.1-46.1c18.7-18.7 49.1-18.7 67.9 0l60.1 60.1c18.8 18.7 18.8 49.1 0 67.9zM284.2 99.8L21.6 362.4.4 483.9c-2.9 16.4 11.4 30.6 27.8 27.8l121.5-21.3 262.6-262.6c4.7-4.7 4.7-12.3 0-17l-111-111c-4.8-4.7-12.4-4.7-17.1 0zM124.1 339.9c-5.5-5.5-5.5-14.3 0-19.8l154-154c5.5-5.5 14.3-5.5 19.8 0s5.5 14.3 0 19.8l-154 154c-5.5 5.5-14.3 5.5-19.8 0zM88 424h48v36.3l-64.5 11.3-31.1-31.1L51.7 376H88v48z"></path></symbol><symbol id="plus-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm144 276c0 6.6-5.4 12-12 12h-92v92c0 6.6-5.4 12-12 12h-56c-6.6 0-12-5.4-12-12v-92h-92c-6.6 0-12-5.4-12-12v-56c0-6.6 5.4-12 12-12h92v-92c0-6.6 5.4-12 12-12h56c6.6 0 12 5.4 12 12v92h92c6.6 0 12 5.4 12 12v56z"></path></symbol><symbol id="share-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M503.691 189.836L327.687 37.851C312.281 24.546 288 35.347 288 56.015v80.053C127.371 137.907 0 170.1 0 322.326c0 61.441 39.581 122.309 83.333 154.132 13.653 9.931 33.111-2.533 28.077-18.631C66.066 312.814 132.917 274.316 288 272.085V360c0 20.7 24.3 31.453 39.687 18.164l176.004-152c11.071-9.562 11.086-26.753 0-36.328z"></path></symbol></svg>
    <div class="layout-col mt-12">
        <div class="card card-has-header block mb-12">
            <div class="card-header">
                <div class="grid items-center rounded-t border-b border-tint-300 text-xs text-tint-600" style="grid-template-columns: 1fr 1fr;">
                    <div class="grid cols-auto justify-start gap-2 px-4 py-2">
                        <div class="flex items-center">
                            <a href="http://flareapp.io/docs/ignition-for-laravel/introduction" target="_blank" title="Ignition docs">
                                <svg class="w-4 h-5 mr-4" viewBox="0 0 428 988"><polygon points="428,247.1 428,494.1 214,617.5 214,369.3" style="fill: rgb(250, 78, 121);"></polygon><polygon points="0,988 0,741 214,617.5 214,864.1" style="fill: rgb(255, 240, 130);"></polygon><polygon points="214,123.9 214,617.5 0,494.1 0,0" style="fill: rgb(230, 0, 58);"></polygon><polygon points="214,864.1 214,617.5 428,741 428,988" style="fill: rgb(255, 225, 0);"></polygon></svg>
                            </a>
                            <span class="ui-path ">
                                <span>/<wbr></span><span>home/<wbr></span><span>appittro/<wbr></span><span>public_html/<wbr></span><span class="font-semibold"></span>
                            </span>
                        </div>
                    </div>
                    <div class="grid cols-auto items-center justify-end gap-4 px-4 py-2"></div>
                </div>
            </div>
            <div class="card-details">
                <div class="card-details-overflow scrollbar">
                    <div class="overflow-hidden text-2xl">
                        <div class="grid grid-cols-auto grid-flow-col gap-2 items-center justify-start">
                            <span class="ui-exception-class "><?php echo carr::get($report, 'exception_class'); ?><wbr></span>
                        </div>
                        <span class="ui-exception-message  mt-1">Illegal string offset 'path'</span>
                    </div>
                    <div><a class="ui-url" href="http://devcloud.dev.ittron.co.id/manager/git/manage/graph/101?path=master" target="_blank">http://devcloud.dev.ittron.co.id/manager/git/manage/graph/101?path=master</a>
        </div>
        </div>
        </div>
        </div>
    </div>
    <div class="tabs">
        <nav class="tab-nav">
            <ul class="tab-bar">
                <li>
                    <button class="tab ">Stack<span class="hidden sm:inline"> trace</span></button>
                </li>
                <li>
                    <button class="tab tab-active">Request</button>
                </li>
                <li>
                    <button class="tab ">App</button>
                </li>
                <li><button class="tab ">User</button></li>
                <li><button class="tab ">Context</button></li>
                <li><button class="tab ">Debug</button></li>
            </ul>
            <div class="tab-delimiter"></div>
            <div role="combobox" aria-expanded="false" aria-haspopup="listbox" aria-labelledby="downshift-0-label">
                <label id="downshift-0-label" class="hidden">Share options menu</label>
                <div>
                    <button class="tab flex items-center ">
                        Share<svg class="icon ml-2"><use xlink:href="#share-icon"></use></svg>
                    </button>
                </div>
            </div>
        </nav>
        <div class="tab-main">
            <div class="tab-content">
                <div class="layout-col">
                    <div class="tab-content-section border-none">
                        <h3 class="definition-list-title">Request</h3>
                        <dl class="definition-list">
                            <dt class="definition-label">URL</dt>
                            <dd class="definition-value">http://devcloud.dev.ittron.co.id/manager/git/manage/graph/101?path=master</dd>
                            <dt class="definition-label">Method</dt>
                            <dd class="definition-value">GET</dd>
                        </dl>
                    </div>
                    <div class="tab-content-section">
                        <h3 class="definition-list-title">Headers</h3>
                        <dl class="definition-list">
                            <dt class="definition-label">accept</dt><dd class="definition-value">text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9</dd><dt class="definition-label">accept-encoding</dt><dd class="definition-value">gzip, deflate</dd><dt class="definition-label">accept-language</dt><dd class="definition-value">en-US,en;q=0.9</dd><dt class="definition-label">connection</dt><dd class="definition-value">keep-alive</dd><dt class="definition-label">cookie</dt><dd class="definition-value">_ga_K04R3QHDFL=GS1.1.1634802662.7.1.1634804754.0; remember_admin_bebed4ae53e2c426c4fab07986402873b6a81f5a=7%7Czfx25O6sxAdpT0ZAcDDlyhCCM85cbxrt5A9d3q8gwDJ4hIXDHUV1h2yUVimz%7C25d55ad283aa400af464c76d713c07ad; cresenityapp_session=rtid4m3793knpvi68uoscrqk04; _ga=GA1.3.1373244580.1634101469; _gid=GA1.3.1868139613.1634998334</dd><dt class="definition-label">host</dt><dd class="definition-value">devcloud.dev.ittron.co.id</dd><dt class="definition-label">user-agent</dt><dd class="definition-value">Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.81 Safari/537.36</dd><dt class="definition-label">cache-control</dt><dd class="definition-value">max-age=0</dd><dt class="definition-label">upgrade-insecure-requests</dt><dd class="definition-value">1</dd></dl></div><div class="tab-content-section"><h3 class="definition-list-title">Query string</h3><dl class="definition-list"><dt class="definition-label">path</dt><dd class="definition-value">master</dd></dl></div><div class="tab-content-section"><h3 class="definition-list-title">Body</h3><div class="definition-list"><div class="definition-list-empty">—</div></div></div><div class="tab-content-section"><h3 class="definition-list-title">Files</h3><div class="definition-list"><div class="definition-list-empty">—</div></div></div><div class="tab-content-section"><h3 class="definition-list-title">Session</h3><div class="definition-list"><div class="definition-list-empty">—</div></div></div><div class="tab-content-section"><h3 class="definition-list-title">Cookies</h3><dl class="definition-list"><dt class="definition-label">_ga_K04R3QHDFL</dt><dd class="definition-value">GS1.1.1634802662.7.1.1634804754.0</dd><dt class="definition-label">remember_admin_bebed4ae53e2c426c4fab07986402873b6a81f5a</dt><dd class="definition-value">7|zfx25O6sxAdpT0ZAcDDlyhCCM85cbxrt5A9d3q8gwDJ4hIXDHUV1h2yUVimz|25d55ad283aa400af464c76d713c07ad</dd><dt class="definition-label">cresenityapp_session</dt><dd class="definition-value">rtid4m3793knpvi68uoscrqk04</dd><dt class="definition-label">_ga</dt><dd class="definition-value">GA1.3.1373244580.1634101469</dd><dt class="definition-label">_gid</dt><dd class="definition-value">GA1.3.1868139613.1634998334</dd></dl>
                </div>
                </div>
                </div>
                </div>
    </div>
    <script src="<?php echo '/media/js/cresenity-exception.js?' . uniqid(); ?>"></script>
</body>
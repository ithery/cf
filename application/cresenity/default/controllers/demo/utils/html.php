<?php

class Controller_Demo_Utils_Html extends \Cresenity\Demo\Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $app = c::app();
        // c::manager()->registerJs([
        //     'script' => 'https://cdn.tailwindcss.com',
        //     'attributes' => [
        //         'defer' => 'defer'
        //     ]
        // ]);
        // $app->addDiv()->addClass('text-3xl font-bold underline')->add(CBase_HtmlBuilder::obfuscate('Hello World!'));

        $app->title('Html');
        $app->addH4()->add('c::clsx()');

        $divGroup = $app->addDiv()->addClass('mb-3');
        $divGroup->addDiv()->addClass('text-muted')->add(c::e('// Strings (variadic)'));
        $divGroup->addDiv()->add(c::e("c::clsx('foo', true ? 'bar' : false, 'baz');"));
        $result = c::clsx('foo', true ? 'bar' : false, 'baz');
        $divGroup->addDiv()->addClass('text-muted')->add(c::e("//=> 'foo bar baz'"));
        $divGroup->addDiv()->add($result);

        $divGroup = $app->addDiv()->addClass('mb-3');
        $divGroup->addDiv()->addClass('text-muted')->add(c::e('// Objects'));
        $divGroup->addDiv()->add(c::e("c::clsx(['foo' => true, 'bar' => false, 'baz' => true]);"));
        $result = c::clsx(['foo' => true, 'bar' => false, 'baz' => true]);
        $divGroup->addDiv()->addClass('text-muted')->add(c::e("//=> 'foo baz'"));
        $divGroup->addDiv()->add($result);

        $divGroup = $app->addDiv()->addClass('mb-3');
        $divGroup->addDiv()->addClass('text-muted')->add(c::e('// Objects (variadic)'));
        $divGroup->addDiv()->add(c::e("c::clsx(['foo' => true], ['bar' => false], null, ['--foobar' => 'hello']);"));
        $result = c::clsx(['foo' => true], ['bar' => false], null, ['--foobar' => 'hello']);
        $divGroup->addDiv()->addClass('text-muted')->add(c::e("//=> 'foo --foobar'"));
        $divGroup->addDiv()->add($result);

        $divGroup = $app->addDiv()->addClass('mb-3');
        $divGroup->addDiv()->addClass('text-muted')->add(c::e('// Arrays'));
        $divGroup->addDiv()->add(c::e("c::clsx(['foo', 0, false, 'bar']);"));
        $result = c::clsx(['foo', 0, false, 'bar']);
        $divGroup->addDiv()->addClass('text-muted')->add(c::e("//=> 'foo bar'"));
        $divGroup->addDiv()->add($result);

        $divGroup = $app->addDiv()->addClass('mb-3');
        $divGroup->addDiv()->addClass('text-muted')->add(c::e('// Arrays (variadic)'));
        $divGroup->addDiv()->add(c::e("c::clsx(['foo', 0, false, 'bar']);"));
        $result = c::clsx(['foo'], ['', 0, false, 'bar'], [['baz', [['hello'], 'there']]]);
        $divGroup->addDiv()->addClass('text-muted')->add(c::e("//=> 'foo bar baz hello there'"));
        $divGroup->addDiv()->add($result);

        $divGroup = $app->addDiv()->addClass('mb-3');
        $divGroup->addDiv()->addClass('text-muted')->add(c::e('// Kitchen sink (with nesting)'));
        $divGroup->addDiv()->add(c::e("c::clsx('foo', [1 ? 'bar' : false, ['baz' => false, 'bat' => null], ['hello', ['world']]], 'cya');"));
        $result = c::clsx('foo', [1 ? 'bar' : false, ['baz' => false, 'bat' => null], ['hello', ['world']]], 'cya');
        $divGroup->addDiv()->addClass('text-muted')->add(c::e("//=> 'foo bar hello world cya'"));
        $divGroup->addDiv()->add($result);

        return $app;
    }
}

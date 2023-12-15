<?php

class Controller_Demo_Module_String_Regex extends \Cresenity\Demo\Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $app = c::app();
        $app->title('String - Regex');

        $app->addH5()->add('Using Match');
        $app->addP()->addPrismCode()->add(c::e("CString::regex()->match('/a/', 'abc'); // `MatchResult` object"));
        $app->addP()->add(cdbg::varDump(CString::regex()->match('/a/', 'abc'), true));
        $app->addP()->addPrismCode()->add(c::e("CString::regex()->match('/a/', 'abc')->hasMatch(); // true"));
        $app->addP()->add(cdbg::varDump(CString::regex()->match('/a/', 'abc')->hasMatch(), true));
        $app->addP()->addPrismCode()->add(c::e("CString::regex()->match('/a/', 'abc')->result(); // 'a'"));
        $app->addP()->add(cdbg::varDump(CString::regex()->match('/a/', 'abc')->result(), true));

        $app->addH5()->addClass('pt-3')->add('Capturing groups with `match`');
        $app->addP()->addPrismCode()->add(c::e("CString::regex()->match('/a(b)/', 'abc')->result(); // 'ab'"));
        $app->addP()->add(cdbg::varDump(CString::regex()->match('/a(b)/', 'abc')->result(), true));
        $app->addP()->addPrismCode()->add(c::e("CString::regex()->match('/a(b)/', 'abc')->group(1); // 'b'"));
        $app->addP()->add(cdbg::varDump(CString::regex()->match('/a(b)/', 'abc')->group(1), true));

        $app->addH5()->addClass('pt-3')->add('Setting defaults');
        $app->addP()->addPrismCode()->add(c::e("CString::regex()->match('/a(b)/', 'xyz')->resultOr('default'); // 'default'"));
        $app->addP()->add(cdbg::varDump(CString::regex()->match('/a(b)/', 'xyz')->resultOr('default'), true));
        $app->addP()->addPrismCode()->add(c::e("CString::regex()->match('/a(b)/', 'xyz')->groupOr(1, 'default'); // 'default'"));
        $app->addP()->add(cdbg::varDump(CString::regex()->match('/a(b)/', 'xyz')->groupOr(1, 'default'), true));

        $app->addH5()->addClass('pt-3')->add('Using `matchAll`');
        $app->addP()->addPrismCode()->add(c::e("CString::regex()->matchAll('/a/', 'abcabc')->hasMatch(); // true"));
        $app->addP()->add(cdbg::varDump(CString::regex()->matchAll('/a/', 'abcabc')->hasMatch(), true));
        $app->addP()->addPrismCode()->add(c::e("CString::regex()->matchAll('/a/', 'abcabc')->results(); // Array of `MatchResult` objects"));
        $app->addP()->add(cdbg::varDump(CString::regex()->matchAll('/a/', 'abcabc')->results(), true));

        $app->addH5()->addClass('pt-3')->add('Using replace`');
        $app->addP()->addPrismCode()->add(c::e("CString::regex()->replace('/a/', 'b', 'abc')->result(); // 'bbc';"));
        $app->addP()->add(cdbg::varDump(CString::regex()->replace('/a/', 'b', 'abc')->result(), true));
        $app->addP()->addPrismCode()->add(c::e(<<<PHP
CString::regex()->replace('/a/', function (CString_Regex_MatchResult \$result) {
    return \$result->result() . 'Hello!';
}, 'abc')->result(); // 'aHello!bc';
PHP));
        $app->addP()->add(cdbg::varDump(CString::regex()->replace('/a/', function (CString_Regex_MatchResult $result) {
            return $result->result() . 'Hello!';
        }, 'abc')->result(), true));

        return $app;
    }
}

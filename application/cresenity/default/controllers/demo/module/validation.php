<?php

class Controller_Demo_Module_Validation extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $name = '';
        $email = '';
        $password = '';
        $passwordConfirm = '';
        $country = '';
        $url = '';
        $post = c::request()->post();
        $validationData = [
            'name' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', CValidation::rule()->password()->min(8)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()],
            'country' => ['required', CValidation::rule()->closure(function ($attribute, $value, Closure $fail) {
                $country = \Cresenity\Demo\Model\Country::where('name', '=', $value)->first();

                if ($country == null) {
                    $fail(c::e("{$attribute} {$value} tidak ditemukan."));
                }
            })],
            'url' => ['url'],
        ];

        if ($post) {
            c::msg('success', 'Form Submitted with data:<br/><pre>' . json_encode($post, JSON_PRETTY_PRINT) . '</pre>');
            $validator = c::validator($post, $validationData);

            if (!$validator->check()) {
                c::msg('error', $validator->errors()->first());
            }
            // set default value
            $name = carr::get($post, 'name');
            $email = carr::get($post, 'email');
            $password = carr::get($post, 'password');
            $passwordConfirm = carr::get($post, 'password_confirmation');
            $country = carr::get($post, 'country');
            $url = carr::get($post, 'url');
        }

        $app->setTitle('Validation');
        // $widget = $app->addWidget()->addClass('mb-3')->setTitle('Simply Validation');
        // $email = 'absd.com';
        // $widget->addDiv()->add('Email: ' . $email);
        // $widget->addDiv()->add('Is Email: ' . (c::validate($email, 'email') ? 'True' : 'False'));
        // $email = 'absd@dd.com';
        // $widget->addDiv()->add('Email: ' . $email);
        // $widget->addDiv()->add('Is Email: ' . (c::validate($email, 'email') ? 'True' : 'False'));

        $widget = $app->addWidget()->setTitle('Form Demo');
        $form = $widget->addForm();
        $form->addField()->setLabel('Name')->addTextControl('name')->setPlaceholder('Your name')->setValue($name);
        $form->addField()->setLabel('Email')->addEmailControl('email')->setPlaceholder('Input Email..')->setValue($email);
        $form->addField()->setLabel('Password')->addPasswordControl('password')->setPlaceholder('Input Password..')->setValue($password);
        $form->addField()->setLabel('Retype Password')->addPasswordControl('password_confirmation')->setPlaceholder('Retype Password..')->setValue($passwordConfirm);
        $form->addField()->setLabel('Country')->addTextControl('country')->setPlaceholder('Country')->setValue($country);
        $form->addField()->setLabel('Url')->addTextControl('url')->setPlaceholder('Url')->setValue($url);

        $form->addActionList()->addAction()->setSubmit()->setLabel('Submit');
        $form->setValidation($validationData);

        return $app;
    }
}

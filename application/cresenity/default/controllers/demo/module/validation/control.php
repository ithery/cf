<?php

class Controller_Demo_Module_Validation_Control extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $name = '';
        $email = '';
        $password = '';
        $passwordConfirm = '';
        $country = '';
        $url = '';
        $post = c::request()->post();
        $countryExistsRule = CValidation::rule()->closure(function ($attribute, $value, Closure $fail) {
            $country = \Cresenity\Demo\Model\Country::where('name', '=', $value)->first();

            if ($country == null) {
                $fail(c::e("{$attribute} {$value} tidak ditemukan."));
            }
        });
        $passwordRule = CValidation::rule()->password()->min(8)
            ->mixedCase()
            ->letters()
            ->numbers()
            ->symbols()
            ->uncompromised();

        if ($post) {
            c::msg('success', 'Form Submitted with data:<br/><pre>' . json_encode($post, JSON_PRETTY_PRINT) . '</pre>');
            $validator = c::validator($post, [
                'name' => ['required'],
                'email' => ['required', 'email'],
                'password' => ['required', 'confirmed', $passwordRule],
                'country' => ['required', $countryExistsRule],
                'url' => ['url'],
            ]);

            if (!$validator->check()) {
                c::msg('error', $validator->errors()->first());
            }
            $name = carr::get($post, 'name');
            $email = carr::get($post, 'email');
            $password = carr::get($post, 'password');
            $passwordConfirm = carr::get($post, 'password_confirmation');
            $country = carr::get($post, 'country');
            $url = carr::get($post, 'url');
        }

        $app->setTitle('Validation - Control-level Demo');

        $widget = $app->addWidget()->setTitle('Control-level Validation Demo');
        $widget->addDiv()->addClass('mb-3 text-muted')
            ->add('Same rules as the Form Demo page (required, email, confirmed password with strength rules, a custom closure rule, url), but each rule is chained directly on its control via addControl()->addValidation() instead of a central Form::setValidation() array.');
        $form = $widget->addForm();
        $form->addField()->setLabel('Name')
            ->addControl('name', 'text')
            ->setPlaceholder('Your name')
            ->setValue($name)
            ->addValidation('required');
        $form->addField()->setLabel('Email')
            ->addControl('email', 'email')
            ->setPlaceholder('Input Email..')
            ->setValue($email)
            ->addValidation('required')
            ->addValidation('email');
        $form->addField()->setLabel('Password')
            ->addControl('password', 'password')
            ->setPlaceholder('Input Password..')
            ->setValue($password)
            ->addValidation('required')
            ->addValidation('confirmed')
            ->addValidation($passwordRule);
        $form->addField()->setLabel('Retype Password')
            ->addControl('password_confirmation', 'password')
            ->setPlaceholder('Retype Password..')
            ->setValue($passwordConfirm);
        $form->addField()->setLabel('Country')
            ->addControl('country', 'text')
            ->setPlaceholder('Country')
            ->setValue($country)
            ->addValidation('required')
            ->addValidation($countryExistsRule);
        $form->addField()->setLabel('Url')
            ->addControl('url', 'text')
            ->setPlaceholder('Url')
            ->setValue($url)
            ->addValidation('url');
        $form->addActionList()->addAction()->setSubmit()->setLabel('Submit');

        return $app;
    }
}

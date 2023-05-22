<?php

class Controller_Demo_Module_Validation extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $name = '';
        $select = '';
        $email = '';
        $radio = 'radio1';
        $post = c::request()->post();
        $validationData = [
            'name' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', CValidation_Rule_Password::min(8)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()]
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
        }

        $app->setTitle('Form');
        $widget = $app->addWidget()->setTitle('Form Demo');
        $form = $widget->addForm();
        $form->addField()->setLabel('Name')->addTextControl('name')->setPlaceholder('Your name')->setValue($name);
        $form->addField()->setLabel('Email')->addEmailControl('email')->setPlaceholder('Input Email..')->setValue($email);
        $form->addField()->setLabel('Password')->addPasswordControl('password')->setPlaceholder('Input Password..')->setValue($password);

        $form->addActionList()->addAction()->setSubmit()->setLabel('Submit');
        $form->setValidation($validationData);

        return $app;
    }
}

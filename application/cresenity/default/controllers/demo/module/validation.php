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
        ];

        if ($post) {
            c::msg('success', 'Form Submitted with data:<br/><pre>' . json_encode($post, JSON_PRETTY_PRINT) . '</pre>');
            $validator = c::validator($post, $validationData);
            if (!$validator->check()) {
                c::msg('error', $validator->errors()->first());
            }
        }

        $app->setTitle('Form');
        $widget = $app->addWidget()->setTitle('Form Demo');
        $form = $widget->addForm();
        $form->addField()->setLabel('Name')->addTextControl('name')->setPlaceholder('Your name')->setValue($name);
        $form->addField()->setLabel('Email')->addEmailControl('email')->setPlaceholder('Input Email..')->setValue($email);

        $form->addActionList()->addAction()->setSubmit()->setLabel('Submit');
        $form->setValidation($validationData);

        return $app;
    }
}

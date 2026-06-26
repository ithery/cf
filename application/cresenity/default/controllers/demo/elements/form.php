<?php

class Controller_Demo_Elements_Form extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $name = '';
        $select = '';
        $email = '';
        $phone = '';
        $password = '';
        $bio = '';
        $radio = 'male';
        $agree = '';
        $post = c::request()->post();
        $validationData = [
            'name' => ['required'],
            'email' => ['required'],
        ];

        if ($post) {
            $app->addAlert()->setTypeSuccess()->add(
                '<strong>Form submitted!</strong><pre class="mt-2 mb-0">' . json_encode($post, JSON_PRETTY_PRINT) . '</pre>'
            );
            $validator = c::validator($post, $validationData);
            if (!$validator->check()) {
                $app->addAlert()->setTypeDanger()->add($validator->errors()->first());
            }
            $name = carr::get($post, 'name');
            $select = carr::get($post, 'category');
            $email = carr::get($post, 'email');
            $phone = carr::get($post, 'phone');
            $bio = carr::get($post, 'bio');
            $radio = carr::get($post, 'gender', 'male');
            $agree = carr::get($post, 'agree');
        }

        $app->setTitle('Form');

        $form = $app->addForm();

        // Personal Information
        $widget = $form->addWidget()->setTitle('Personal Information')->setIcon('ti-user');
        $row = $widget->addDiv()->addClass('row');
        $row->addDiv()->addClass('col-md-6')
            ->addField()->setLabel('Full Name')->addTextControl('name')
            ->setPlaceholder('Enter your full name')->setValue($name);
        $row->addDiv()->addClass('col-md-6')
            ->addField()->setLabel('Email Address')->addEmailControl('email')
            ->setPlaceholder('you@example.com')->setValue($email);

        $row = $widget->addDiv()->addClass('row');
        $row->addDiv()->addClass('col-md-6')
            ->addField()->setLabel('Phone Number')->addTextControl('phone')
            ->setPlaceholder('+62 xxx xxxx xxxx')->setValue($phone);
        $row->addDiv()->addClass('col-md-6')
            ->addField()->setLabel('Category')->addSelectControl('category')
            ->setList(['' => '-- Select --', 'individual' => 'Individual', 'business' => 'Business', 'enterprise' => 'Enterprise'])
            ->setValue($select);

        // Additional Details
        $widget = $form->addWidget()->setTitle('Additional Details')->setIcon('ti-pencil');
        $widget->addField()->setLabel('Bio')->addTextareaControl('bio')
            ->setPlaceholder('Tell us about yourself...')->setValue($bio);

        $row = $widget->addDiv()->addClass('row');
        $genderField = $row->addDiv()->addClass('col-md-6')->addField()->setLabel('Gender');
        $genderField->addRadioControl()->setLabel('Male')->setName('gender')->setValue('male')->setChecked($radio == 'male');
        $genderField->addRadioControl()->setLabel('Female')->setName('gender')->setValue('female')->setChecked($radio == 'female');

        $row->addDiv()->addClass('col-md-6')
            ->addField()->setLabel('Password')->addPasswordControl('password')
            ->setPlaceholder('Min 8 characters');

        $widget->addField()->addCheckboxControl('agree')->setLabel('I agree to the Terms and Conditions');

        // Actions
        $actions = $form->addActionList();
        $actions->setStyle('form-action');
        $actions->addAction()->setSubmit()->setLabel('Save')->addClass('btn-primary');
        $actions->addAction()->setLabel('Cancel')->addClass('btn-outline-secondary');

        $form->setValidation($validationData);

        return $app;
    }
}

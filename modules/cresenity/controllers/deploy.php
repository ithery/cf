<?php

    class Deploy_Controller extends CController {

        public function index(){
            $app = CApp::instance();
            
            $post = $_POST;
            
            if (isset($post)) {
                $this->git_pull();
            }
            
            $form = $app->add_form();
            $form->add_field()->set_label('Domain')->add_control('domain', 'text');
            $form->add_action();
            
            echo $app->render();
        }
        
        public function git_pull_test() {
            $post = $_POST;
            $domain = carr::get($post, 'domain');
            
            Git::set_bin('git');
            $repo = Git::open("C:/xampp/htdocs_pippo/application/" .$domain ."/");
            $res = $repo->run('fetch --progress --prune origin');
            $res = $repo->run('merge --no-commit --ff origin/Test');
            $res = $repo->test_git();
            var_dump($res);
        }

    }
    
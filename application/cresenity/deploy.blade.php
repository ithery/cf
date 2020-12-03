@servers(['web' => 'localhost'])
@task('deploy')
    cd D:\\xampp\htdocs_pipo\application\bistar
    git pull origin master
@endtask

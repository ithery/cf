<?php




$name = \Cresenity\Demo\DemoVariable::username();

$pageWrapperAttr = '';
?>

@extends('layouts.base')
@section('content')
<div id="wrapper" class="layout-wrapper layout-2">
    <div class="layout-inner">

        @include('demo.sidenav')


        <div id="page-wrapper" class="layout-container" <?php echo $pageWrapperAttr; ?>>
            @include('demo.navbar')
            <div class="main layout-content">
                <div class="main-inner container-fluid flex-grow-1 container-p-y">

                    <div class="row page-heading">
                        <div class="col-lg-12">

                            <?php if ($show_title): ?>
                                <h4 class="font-weight-bold py-3 mb-4"><?php echo $title ?></h4>
                            <?php endif; ?>

                        </div>
                    </div>


                    <div class="wrapper wrapper-content">
                        <?php
                        $msg = cmsg::flash_all();
                        if (strlen($msg) > 0) {
                            echo '<div class="row-fluid"><div class="span12">' . $msg . '</div></div>';
                        }
                        ?>
                        <div class="row">
                            <div class="col-lg-12">

                                <div class="container">

                                <?php echo $content; ?>

                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="layout-footer footer bg-footer-theme">
                <div class="container-fluid d-flex flex-wrap justify-content-between text-center container-p-x pb-3">
                    <div class="pt-3">
                        <span class="footer-text font-weight-bolder">
                            &copy; <?php echo date('Y'); ?> CF V <?php echo CF::version(); ?>
                        </span>
                    </div> <!-- /span12 -->
                </div>
                <div></div> <!-- /row -->

            </div> <!-- /footer -->
        </div><!-- /page-wrapper -->
    </div>
    <div class="layout-overlay layout-sidenav-toggle"></div>
</div><!-- /wrapper -->


@endsection

@push('custom-scripts')

@endpush

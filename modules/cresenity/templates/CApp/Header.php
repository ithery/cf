<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!isset($title)) {
    $title = '';
}
?>
<form class="capp-header-form" id="<?php $this->element()->id() . '-form' ?>" >
    <div class = "section-header d-flex align-items-center">
        <h3><?php echo $title; ?></h3>
        <div class="ml-auto section-header-actions">
            <?php echo $this->section('actions'); ?>
        </div>
    </div>

</form>
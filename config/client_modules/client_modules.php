<?php

return array(
    "json2" => array(
        "js" => array("libs" . DS . "json2.js"),
    ),
    "excanvas" => array(
        "js" => array("libs" . DS . "excanvas.min.js"),
    ),
    "canvas-to-blob" => array(
        "js" => array("libs" . DS . "canvas-to-blob.min.js"),
    ),
    "load-image" => array(
        "js" => array("libs" . DS . "load-image.min.js"),
    ),
    "tmpl" => array(
        "js" => array("libs" . DS . "tmpl.min.js"),
    ),
    "jquery" => array(
        "js" => array("libs" . DS . "jquery.js"),
    ),
    "bootstrap" => array(
        "js" => array("libs" . DS . "bootstrap.min.js"),
        "css" => array("bootstrap.css", "bootstrap-responsive.css"),
    ),
    "bootstrap-switch" => array(
        "js" => array("plugins".DS."bootstrap-switch".DS."bootstrap-switch.js"),
        "css" => array("plugins".DS."bootstrap-switch".DS."bootstrap-switch.css"),
    ),
    "font-awesome" => array(
        "css" => array("font-awesome.css"),
    ),
    "jquery.ui" => array(
        "js" => array("libs" . DS . "jquery.ui.custom.js"),
        "css"=>array("plugins".DS."jquery-ui".DS."smoothness".DS."jquery-ui.css","plugins".DS."jquery-ui".DS."smoothness".DS."jquery.ui.theme.css"),
        "requirements" => array("jquery"),
    ),
    "jquery.dialog2" => array(
        "js" => array("plugins" . DS . "dialog2" . DS . "jquery.dialog2.js", "plugins" . DS . "dialog2" . DS . "jquery.dialog2.helpers.js"),
        "css" => array("plugins" . DS . "dialog2" . DS . "jquery.dialog2.css"),
        "requirements" => array("jquery", "bootstrap"),
    ),
    "jquery.datatable" => array(
        "js" => array(
            "plugins" . DS . "datatable" . DS . "jquery.dataTables.js",
            "plugins" . DS . "datatable" . DS . "TableTools.min.js",
            "plugins" . DS . "datatable" . DS . "ColReorder.min.js",
            "plugins" . DS . "datatable" . DS . "ColVis.min.js",
            "plugins" . DS . "datatable" . DS . "jquery.dataTables.columnFilter.js",
        ),
        "requirements" => array("jquery", "bootstrap"),
    ),
	"jquery.datatable.tabletools" => array(
        "js" => array(
            "plugins" . DS . "datatable" . DS . "TableTools.min.js",
        ),
        "requirements" => array("jquery.datatable"),
    ),
	"jquery.datatable.colreorder" => array(
        "js" => array(
            "plugins" . DS . "datatable" . DS . "ColReorder.min.js",
        ),
        "requirements" => array("jquery.datatable"),
    ),
	"jquery.datatable.colvis" => array(
        "js" => array(
            "plugins" . DS . "datatable" . DS . "ColVis.min.js",
        ),
        "requirements" => array("jquery.datatable"),
    ),
	"jquery.datatable.columnfilter" => array(
        "js" => array(
            "plugins" . DS . "datatable" . DS . "jquery.dataTables.columnFilter.js",
        ),
        "requirements" => array("jquery.datatable"),
    ),
    "chosen" => array(
        "js" => array("plugins" . DS . "chosen" . DS . "chosen.jquery.min.js"),
        "css" => array("plugins" . DS . "chosen" . DS . "chosen.css"),
        "requirements" => array("jquery"),
    ),
    "jquery.nestable" => array(
        "js" => array("plugins" . DS . "nestable" . DS . "jquery.nestable.js"),
        "css" => array("plugins" . DS . "nestable" . DS . "jquery.nestable.css"),
        "requirements" => array("jquery"),
    ),
    "cresenity" => array(
        "js" => array(
            "cresenity.func.js",
            "cresenity.js",
            "cresenity.item_batch.js",
            "cresenity.pricing_detail.js",
        ),
        "css" => array(
            "cresenity.colors.css",
            "cresenity.main.css",
            "cresenity.responsive.css",
            "cresenity.pos.css",
            "cresenity.retail.css",
            "cresenity.widget.css",
            "cresenity.table.css",
            "cresenity.css",
        ),
    ),
    "vkeyboard" => array(
        "js" => array("plugins" . DS . "vkeyboard" . DS . "bootstrap-vkeyboard.js"),
        "css" => array("plugins" . DS . "vkeyboard" . DS . "bootstrap-vkeyboard.css"),
    ),
    "mockjax" => array(
        "js" => array("plugins" . DS . "mockjax" . DS . "jquery.mockjax.js"),
    ),
    "fileupload" => array(
        "js" => array("plugins" . DS . "fileupload" . DS . "bootstrap-fileupload.min.js"),
    ),
    "peity" => array(
        "js" => array("plugins" . DS . "peity" . DS . "jquery.peity.min.js"),
    ),
    "flot" => array(
        "js" => array(
            "plugins" . DS . "flot" . DS . "jquery.flot.min.js",
            "plugins" . DS . "flot" . DS . "jquery.flot.bar.order.min.js",
            "plugins" . DS . "flot" . DS . "jquery.flot.pie.min.js",
            "plugins" . DS . "flot" . DS . "jquery.flot.resize.min.js",
            "plugins" . DS . "flot" . DS . "jquery.flot.stack.js",
        ),
    ),
    "colorpicker" => array(
        "js" => array("plugins" . DS . "colorpicker" . DS . "bootstrap-colorpicker.js"),
        "css" => array("plugins" . DS . "colorpicker" . DS . "colorpicker.css"),
    ),
    "wysihtml5" => array(
        "js" => array(
            "libs" . DS . "wysihtml5-0.3.0.js",
            "plugins" . DS . "wysihtml5" . DS . "bootstrap-wysihtml5.js",
        ),
        "css" => array("plugins" . DS . "wysihtml5" . DS . "bootstrap-wysihtml5.css"),
        "requirements" => array("jquery"),
    ),
    "notify" => array(
        "js" => array("plugins" . DS . "notify" . DS . "bootstrap-notify.js"),
        "css" => array(
            "plugins" . DS . "notify" . DS . "bootstrap-notify.css",
            "plugins" . DS . "notify" . DS . "bootstrap-notify-alert-backgloss.css",
        ),
    ),
    "bootbox" => array(
        "js" => array("plugins" . DS . "bootbox" . DS . "jquery.bootbox.js"),
    ),
    "form" => array(
        "js" => array("plugins" . DS . "form" . DS . "jquery.form.js"),
        "requirements" => array("jquery"),
    ),
    "controls" => array(
        "js" => array("plugins" . DS . "controls" . DS . "jquery.controls.js"),
        "requirements" => array("jquery"),
    ),
    "event" => array(
        "js" => array(
            "plugins" . DS . "event" . DS . "jquery.event.move.js",
            "plugins" . DS . "event" . DS . "jquery.event.swipe.js"
        ),
    ),
    "slimscroll" => array(
        "js" => array(
            "plugins" . DS . "slimscroll" . DS . "jquery.slimscroll.js",
            "plugins" . DS . "slimscroll" . DS . "jquery.slimscroll-horizontal.js",
        ),
    ),
    "effects" => array(
        "js" => array(
            "plugins" . DS . "effects" . DS . "jquery.effects.core.js",
            "plugins" . DS . "effects" . DS . "jquery.effects.slide.js",
        ),
    ),
    "validation" => array(
        "js" => array(
            "plugins" . DS . "validation-engine" . DS . "jquery.validationEngine.js",
            "plugins" . DS . "validation-engine" . DS . "languages" . DS . "jquery.validationEngine-en.js",
        ),
        "css" => array("plugins" . DS . "validation-engine" . DS . "jquery.validationEngine.css"),
    ),
    "ckeditor" => array(
        "js" => array("plugins" . DS . "ckeditor" . DS . "ckeditor.js"),
    ),
    "isotope" => array(
        "js" => array("plugins" . DS . "isotope" . DS . "jquery.isotope.min.js"),
    ),
    "easing" => array(
        "js" => array("plugins" . DS . "easing" . DS . "jquery-easing-1.3.js"),
    ),
    "plupload" => array(
        "js" => array(
            "plugins" . DS . "plupload" . DS . "plupload.full.js",
            "plugins" . DS . "plupload" . DS . "jquery.plupload.queue.js",
        ),
        "css" => array("plugins" . DS . "plupload" . DS . "jquery.plupload.queue.css"),
    ),
    "servertime" => array(
        "js" => array("plugins" . DS . "servertime" . DS . "jquery.servertime.js"),
    ),
    "uniform" => array(
        "js" => array("plugins" . DS . "uniform" . DS . "jquery.uniform.js"),
        "css" => array("plugins" . DS . "uniform" . DS . "uniform.css"),
        "requirements" => array("jquery"),
    ),
    "select2" => array(
        "js" => array("plugins" . DS . "select2" . DS . "select2.js"),
        "css" => array("plugins" . DS . "select2" . DS . "select2.css"),
        "requirements" => array("jquery"),
    ),
    "datepicker" => array(
        "js" => array("plugins" . DS . "datepicker" . DS . "bootstrap-datepicker.js"),
        "css" => array("plugins" . DS . "datepicker" . DS . "datepicker.css"),
    ),
    "timepicker" => array(
        "js" => array("plugins" . DS . "timepicker" . DS . "bootstrap-timepicker.min.js"),
        "css" => array("plugins" . DS . "timepicker" . DS . "bootstrap-timepicker.min.css"),
    ),
    "image-gallery" => array(
        "css" => array("plugins" . DS . "image-gallery" . DS . "bootstrap-image-gallery.min.css"),
    ),
    "modernizr" => array(
        "js" => array("libs" . DS . "modernizr.custom.js"),
    ),
    "multiselect" => array(
        "js" => array("plugins" . DS . "multiselect" . DS . "jquery.multi-select.js"),
        "css" => array("plugins" . DS . "multiselect" . DS . "multi-select.css"),
    ),
    "terminal" => array(
        "js" => array(
            "plugins" . DS . "terminal" . DS . "jquery.mousewheel-min.js",
            "plugins" . DS . "terminal" . DS . "jquery.terminal-min.js",
        ),
    ),
    "elfinder" => array(
        "js" => array("plugins" . DS . "elfinder" . DS . "elfinder.min.js"),
        "css" => array("plugins" . DS . "elfinder" . DS . "elfinder.min.css"),
    ),
    "prettify" => array(
        "js" => array("plugins" . DS . "google-code-prettify" . DS . "prettify.js"),
        "css" => array("plugins" . DS . "google-code-prettify" . DS . "prettify.css"),
    ),
);
?>
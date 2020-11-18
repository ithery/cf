<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<style type="text/css">
<?php include CF::find_file('views', 'kohana_errors', FALSE, 'css') ?>
</style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title><?php echo $error ?></title>
</head>
<body>
<div id="framework_error" style="width:42em;margin:20px auto;">
<h3><?php echo chtml::specialchars($error) ?></h3>
<p><?php echo chtml::specialchars($description) ?></p>
<?php  if (!IN_PRODUCTION || (isset($_GET['show_debug_error']) && $_GET['show_debug_error'] == '1')|| (isset($show_debug_error) && $show_debug_error )): ?>
<?php if ( ! empty($line) AND ! empty($file)): ?>
<p><?php echo CF::lang('core.error_file_line', $file, $line) ?></p>
<?php endif ?>
<p><code class="block"><?php echo $message ?></code></p>
<?php if ( ! empty($trace)): ?>
<h3><?php echo CF::lang('core.stack_trace') ?></h3>
<pre>
<?php echo $trace ?>
</pre>
<?php endif ?>
<p class="stats"><?php echo CF::lang('core.stats_footer') ?></p>
<?php  endif; ?>
</div>
</body>
</html>
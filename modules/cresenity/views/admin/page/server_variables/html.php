<?php
	
	$info = $_SERVER;
?>

<?php
	echo "<table class=\"table table-striped table-bordered\">\n";
	foreach($info as $key => $val) {
        
		if(is_array($val))
			echo "<tr><td>$key</td><td>$val[0]</td><td>$val[1]</td></tr>\n";
		elseif(is_string($key))
			echo "<tr><td>$key</td><td>$val</td></tr>\n";
		else
			echo "<tr><td>$val</td></tr>\n";
  
       
    }
	 echo "</table>\n";
?>
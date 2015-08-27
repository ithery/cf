<?php defined('SYSPATH') OR die('No direct access allowed.');

class CView extends View {
	public static function factory($name = NULL, $data = NULL, $type = NULL) {
        
		return new CView($name, $data, $type);
    }
	
	public static function load_view($view_filename, $input_data) {
		if ($view_filename == '')
			return;

		// Buffering on
		ob_start();

		// Import the view variables to local namespace
		extract($input_data, EXTR_SKIP);

		// Views are straight HTML pages with embedded PHP, so importing them
		// this way insures that $this can be accessed as if the user was in
		// the controller, which gives the easiest access to libraries in views
		try
		{
			include $view_filename;
		}
		catch (Exception $e)
		{
			ob_end_clean();
			throw $e;
		}

		// Fetch the output and close the buffer
		return ob_get_clean();
	}
	
	public static function exists($name) {
        $filename = CF::find_file('views', $name, false);
		return strlen($filename)>0;
    }
	
	/**
     * Renders a view. 
	 * Overwrite View.render
     *
     * @param   boolean   set to TRUE to echo the output instead of returning it
     * @param   callback  special renderer to pass the output through
     * @return  string    if print is FALSE
     * @return  void      if print is TRUE
     */
    public function render($print = FALSE, $renderer = FALSE) {
        if (empty($this->kohana_filename))
            throw new CF_Exception('core.view_set_filename');
        if (is_string($this->kohana_filetype)) {
            // Merge global and local data, local overrides global with the same name
            $data = array_merge(View::$kohana_global_data, $this->kohana_local_data);

//            var_dump(CF::$instance);
            // Load the view in the controller for access to $this
            $output = self::load_view($this->kohana_filename, $data);

            if ($renderer !== FALSE AND is_callable($renderer, TRUE)) {
                // Pass the output through the user defined renderer
                $output = call_user_func($renderer, $output);
            }

            if ($print === TRUE) {
                // Display the output
                echo $output;
                return;
            }
        } else {
            // Set the content type and size
            header('Content-Type: ' . $this->kohana_filetype[0]);

            if ($print === TRUE) {
                if ($file = fopen($this->kohana_filename, 'rb')) {
                    // Display the output
                    fpassthru($file);
                    fclose($file);
                }
                return;
            }

            // Fetch the file contents
            $output = file_get_contents($this->kohana_filename);
        }

        return $output;
    }
}
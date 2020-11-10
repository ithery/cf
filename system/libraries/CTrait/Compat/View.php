<?php

/**
 * Description of View
 *
 * @author Hery
 */
trait CTrait_Compat_View {

    /**
     * Load a view.
     *
     * @param   view_filename   filename of view
     * @param   input_data  data to pass to view
     * @return  string    
     */
    public static function load_view($view_filename, $input_data) {
        return static::loadView($view_filename, $input_data);
    }

}

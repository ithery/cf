<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jan 6, 2018, 6:33:59 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTemplate extends CView {

    protected static $viewFolder = 'templates';

    /**
     * Creates a new CView using the given parameters.
     *
     * @param   string  view name
     * @param   array   pre-load data
     * @param   string  type of file: html, css, js, etc.
     * @return  object
     */
    public static function factory($name = NULL, $data = NULL, $type = NULL) {
        return new CTemplate($name, $data, $type);
    }

    /**
     * Sets the view filename.
     *
     * @chainable
     * @param   string  view filename
     * @param   string  view file type
     * @return  object
     */
    public function set_filename($name, $type = NULL) {

        if ($type == NULL) {
            // Load the filename and set the content type
            $this->filename = CF::find_file(self::$viewFolder, $name, TRUE);
            $this->filetype = EXT;
        } else {
            // Check if the filetype is allowed by the configuration
            if (!in_array($type, CF::config('view.allowed_filetypes')))
                throw new CF_Exception('core.invalid_filetype', $type);

            // Load the filename and set the content type
            $this->filename = CF::find_file(self::$viewFolder, $name, TRUE, $type);
            $this->filetype = CF::config('mimes.' . $type);

            if ($this->filetype == NULL) {
                // Use the specified type
                $this->filetype = $type;
            }
        }

        return $this;
    }

    /**
     * Renders a view.
     *
     * @param   boolean   set to TRUE to echo the output instead of returning it
     * @param   callback  special renderer to pass the output through
     * @return  string    if print is FALSE
     * @return  void      if print is TRUE
     */
    public function render($print = FALSE, $renderer = FALSE) {
        if (empty($this->filename)) {
            throw new CException('The requested template, :template_name, could not be found', array(':template_name' => $this->filename));
        }
        return parent::render();
    }

}

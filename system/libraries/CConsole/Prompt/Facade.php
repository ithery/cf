<?php

/**
 * Facade for Laravel Prompts, ported to CF conventions.
 *
 * Accessed via c::prompt()->text(...), c::prompt()->select(...), etc.
 */
class CConsole_Prompt_Facade {
    /**
     * Prompt the user for text input.
     *
     * @param string        $label
     * @param string        $placeholder
     * @param string        $default
     * @param bool|string   $required
     * @param null|callable $validate
     * @param string        $hint
     * @param null|Closure  $transform
     *
     * @return string
     */
    public function text($label, $placeholder = '', $default = '', $required = false, $validate = null, $hint = '', $transform = null) {
        return (new CConsole_Prompt_TextPrompt($label, $placeholder, $default, $required, $validate, $hint, $transform))->prompt();
    }

    /**
     * Prompt the user for text input with auto-completion.
     *
     * @param string               $label
     * @param array|Closure|CCollection $options
     * @param string               $placeholder
     * @param string               $default
     * @param bool|string          $required
     * @param null|callable        $validate
     * @param string               $hint
     * @param null|Closure         $transform
     *
     * @return string
     */
    public function autocomplete($label, $options = [], $placeholder = '', $default = '', $required = false, $validate = null, $hint = '', $transform = null) {
        return (new CConsole_Prompt_AutoCompletePrompt($label, $options, $placeholder, $default, $required, $validate, $hint, $transform))->prompt();
    }

    /**
     * Prompt the user for number input.
     *
     * @param string        $label
     * @param string        $placeholder
     * @param string        $default
     * @param bool|string   $required
     * @param null|callable $validate
     * @param string        $hint
     * @param null|int      $min
     * @param null|int      $max
     * @param null|int      $step
     *
     * @return int|string
     */
    public function number($label, $placeholder = '', $default = '', $required = false, $validate = null, $hint = '', $min = null, $max = null, $step = null) {
        return (new CConsole_Prompt_NumberPrompt($label, $placeholder, $default, $required, $validate, $hint, null, $min, $max, $step))->prompt();
    }

    /**
     * Prompt the user for multiline text input.
     *
     * @param string        $label
     * @param string        $placeholder
     * @param string        $default
     * @param bool|string   $required
     * @param null|callable $validate
     * @param string        $hint
     * @param int           $rows
     * @param null|Closure  $transform
     *
     * @return string
     */
    public function textarea($label, $placeholder = '', $default = '', $required = false, $validate = null, $hint = '', $rows = 5, $transform = null) {
        return (new CConsole_Prompt_TextareaPrompt($label, $placeholder, $default, $required, $validate, $hint, $rows, $transform))->prompt();
    }

    /**
     * Prompt the user for input, hiding the value.
     *
     * @param string        $label
     * @param string        $placeholder
     * @param bool|string   $required
     * @param null|callable $validate
     * @param string        $hint
     * @param null|Closure  $transform
     *
     * @return string
     */
    public function password($label, $placeholder = '', $required = false, $validate = null, $hint = '', $transform = null) {
        return (new CConsole_Prompt_PasswordPrompt($label, $placeholder, $required, $validate, $hint, $transform))->prompt();
    }

    /**
     * Prompt the user to select an option.
     *
     * @param string             $label
     * @param array|CCollection  $options
     * @param null|int|string    $default
     * @param int                $scroll
     * @param null|callable      $validate
     * @param string             $hint
     * @param bool|string        $required
     * @param null|Closure       $transform
     * @param string|Closure     $info
     *
     * @return int|string
     */
    public function select($label, $options, $default = null, $scroll = 5, $validate = null, $hint = '', $required = true, $transform = null, $info = '') {
        return (new CConsole_Prompt_SelectPrompt($label, $options, $default, $scroll, $validate, $hint, $required, $transform, $info))->prompt();
    }

    /**
     * Prompt the user to select multiple options.
     *
     * @param string            $label
     * @param array|CCollection $options
     * @param array|CCollection $default
     * @param int               $scroll
     * @param bool|string       $required
     * @param null|callable     $validate
     * @param string            $hint
     * @param null|Closure      $transform
     * @param string|Closure    $info
     *
     * @return array
     */
    public function multiselect($label, $options, $default = [], $scroll = 5, $required = false, $validate = null, $hint = 'Use the space bar to select options.', $transform = null, $info = '') {
        return (new CConsole_Prompt_MultiSelectPrompt($label, $options, $default, $scroll, $required, $validate, $hint, $transform, $info))->prompt();
    }

    /**
     * Prompt the user to confirm an action.
     *
     * @param string        $label
     * @param bool          $default
     * @param string        $yes
     * @param string        $no
     * @param bool|string   $required
     * @param null|callable $validate
     * @param string        $hint
     * @param null|Closure  $transform
     *
     * @return bool
     */
    public function confirm($label, $default = true, $yes = 'Yes', $no = 'No', $required = false, $validate = null, $hint = '', $transform = null) {
        return (new CConsole_Prompt_ConfirmPrompt($label, $default, $yes, $no, $required, $validate, $hint, $transform))->prompt();
    }

    /**
     * Prompt the user to continue or cancel after pausing.
     *
     * @param string $message
     *
     * @return bool
     */
    public function pause($message = 'Press enter to continue...') {
        return (new CConsole_Prompt_PausePrompt($message))->prompt();
    }

    /**
     * Clear the terminal.
     *
     * @return void
     */
    public function clear() {
        (new CConsole_Prompt_Clear())->display();
    }

    /**
     * Prompt the user for text input with auto-completion.
     *
     * @param string            $label
     * @param array|Closure|CCollection $options
     * @param string            $placeholder
     * @param string            $default
     * @param int               $scroll
     * @param bool|string       $required
     * @param null|callable     $validate
     * @param string            $hint
     * @param null|Closure      $transform
     * @param string|Closure    $info
     *
     * @return string
     */
    public function suggest($label, $options, $placeholder = '', $default = '', $scroll = 5, $required = false, $validate = null, $hint = '', $transform = null, $info = '') {
        return (new CConsole_Prompt_SuggestPrompt($label, $options, $placeholder, $default, $scroll, $required, $validate, $hint, $transform, $info))->prompt();
    }

    /**
     * Allow the user to search for an option.
     *
     * @param string         $label
     * @param Closure        $options
     * @param string         $placeholder
     * @param int            $scroll
     * @param null|callable  $validate
     * @param string         $hint
     * @param bool|string    $required
     * @param null|Closure   $transform
     * @param string|Closure $info
     *
     * @return int|string
     */
    public function search($label, Closure $options, $placeholder = '', $scroll = 5, $validate = null, $hint = '', $required = true, $transform = null, $info = '') {
        return (new CConsole_Prompt_SearchPrompt($label, $options, $placeholder, $scroll, $validate, $hint, $required, $transform, $info))->prompt();
    }

    /**
     * Allow the user to search for multiple options.
     *
     * @param string         $label
     * @param Closure        $options
     * @param string         $placeholder
     * @param int            $scroll
     * @param bool|string    $required
     * @param null|callable  $validate
     * @param string         $hint
     * @param null|Closure   $transform
     * @param string|Closure $info
     *
     * @return array
     */
    public function multisearch($label, Closure $options, $placeholder = '', $scroll = 5, $required = false, $validate = null, $hint = 'Use the space bar to select options.', $transform = null, $info = '') {
        return (new CConsole_Prompt_MultiSearchPrompt($label, $options, $placeholder, $scroll, $required, $validate, $hint, $transform, $info))->prompt();
    }

    /**
     * Render a spinner while the given callback is executing.
     *
     * @param Closure $callback
     * @param string  $message
     *
     * @return mixed
     */
    public function spin(Closure $callback, $message = '') {
        return (new CConsole_Prompt_Spinner($message))->spin($callback);
    }

    /**
     * Display a note.
     *
     * @param string      $message
     * @param null|string $type
     *
     * @return void
     */
    public function note($message, $type = null) {
        (new CConsole_Prompt_Note($message, $type))->display();
    }

    /**
     * Display a callout.
     *
     * @param string       $label
     * @param string|array $content
     * @param null|string  $type
     * @param string       $info
     *
     * @return void
     */
    public function callout($label, $content, $type = null, $info = '') {
        (new CConsole_Prompt_Callout($label, $content, $type, $info))->display();
    }

    /**
     * Display an error.
     *
     * @param string $message
     *
     * @return void
     */
    public function error($message) {
        (new CConsole_Prompt_Note($message, 'error'))->display();
    }

    /**
     * Display a warning.
     *
     * @param string $message
     *
     * @return void
     */
    public function warning($message) {
        (new CConsole_Prompt_Note($message, 'warning'))->display();
    }

    /**
     * Display an alert.
     *
     * @param string $message
     *
     * @return void
     */
    public function alert($message) {
        (new CConsole_Prompt_Note($message, 'alert'))->display();
    }

    /**
     * Display an informational message.
     *
     * @param string $message
     *
     * @return void
     */
    public function info($message) {
        (new CConsole_Prompt_Note($message, 'info'))->display();
    }

    /**
     * Display an introduction.
     *
     * @param string $message
     *
     * @return void
     */
    public function intro($message) {
        (new CConsole_Prompt_Note($message, 'intro'))->display();
    }

    /**
     * Display a closing message.
     *
     * @param string $message
     *
     * @return void
     */
    public function outro($message) {
        (new CConsole_Prompt_Note($message, 'outro'))->display();
    }

    /**
     * Send a notification to the user. (macOS and Linux only)
     *
     * @param string $title
     * @param string $body
     * @param string $subtitle macOS only
     * @param string $sound    macOS only
     * @param string $icon     Linux only
     *
     * @return void
     */
    public function notify($title, $body = '', $subtitle = '', $sound = '', $icon = '') {
        (new CConsole_Prompt_NotifyPrompt($title, $body, $subtitle, $sound, $icon))->display();
    }

    /**
     * Display a table.
     *
     * @param array|CCollection      $headers
     * @param null|array|CCollection $rows
     *
     * @return void
     */
    public function table($headers = [], $rows = null) {
        (new CConsole_Prompt_Table($headers, $rows))->display();
    }

    /**
     * Display a grid.
     *
     * @param array|CCollection $items
     * @param null|int          $maxWidth
     *
     * @return void
     */
    public function grid($items = [], $maxWidth = null) {
        (new CConsole_Prompt_Grid($items, $maxWidth))->display();
    }

    /**
     * Display a progress bar.
     *
     * @param string        $label
     * @param int|iterable  $steps
     * @param null|Closure  $callback
     * @param string        $hint
     *
     * @return array|CConsole_Prompt_Progress
     */
    public function progress($label, $steps, $callback = null, $hint = '') {
        $progress = new CConsole_Prompt_Progress($label, $steps, $hint);

        if ($callback !== null) {
            return $progress->map($callback);
        }

        return $progress;
    }

    /**
     * Start a new multi-step form.
     *
     * @return CConsole_Prompt_FormBuilder
     */
    public function form() {
        return new CConsole_Prompt_FormBuilder();
    }

    /**
     * Update the title of the terminal.
     *
     * @param string $title
     *
     * @return void
     */
    public function title($title) {
        (new CConsole_Prompt_Title($title))->display();
    }

    /**
     * Display a stream of text.
     *
     * @return CConsole_Prompt_Stream
     */
    public function stream() {
        return new CConsole_Prompt_Stream();
    }

    /**
     * Display a task with a spinner and live output.
     *
     * @param string       $label
     * @param Closure      $callback
     * @param null|int     $limit
     * @param bool         $keepSummary
     * @param null|string  $subLabel
     *
     * @return mixed
     */
    public function task($label, Closure $callback, $limit = null, $keepSummary = false, $subLabel = null) {
        return (new CConsole_Prompt_Task($label, $limit === null ? 10 : $limit, $keepSummary, $subLabel))->run($callback);
    }

    /**
     * Display an interactive data table.
     *
     * @param array|CCollection      $headers
     * @param null|array|CCollection $rows
     * @param int                    $scroll
     * @param string                 $label
     * @param string                 $hint
     * @param bool|string            $required
     * @param null|callable          $validate
     * @param null|Closure           $transform
     * @param null|Closure           $filter
     *
     * @return mixed
     */
    public function datatable($headers = [], $rows = null, $scroll = 10, $label = '', $hint = '', $required = false, $validate = null, $transform = null, $filter = null) {
        return (new CConsole_Prompt_DataTablePrompt($headers, $rows, $scroll, $label, $hint, $required, $validate, $transform, $filter))->prompt();
    }
}

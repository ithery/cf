<?php

class CConsole_Prompt_FormBuilder {
    /**
     * Each step that should be executed.
     *
     * @var CConsole_Prompt_FormStep[]
     */
    protected $steps = [];

    /**
     * The responses provided by each step.
     *
     * @var array
     */
    protected $responses = [];

    /**
     * Add a new step.
     *
     * @param Closure     $step
     * @param null|string $name
     * @param bool        $ignoreWhenReverting
     *
     * @return $this
     */
    public function add(Closure $step, $name = null, $ignoreWhenReverting = false) {
        $this->steps[] = new CConsole_Prompt_FormStep($step, true, $name, $ignoreWhenReverting);

        return $this;
    }

    /**
     * Add a new conditional step.
     *
     * @param bool|Closure $condition
     * @param Closure      $step
     * @param null|string  $name
     * @param bool         $ignoreWhenReverting
     *
     * @return $this
     */
    public function addIf($condition, Closure $step, $name = null, $ignoreWhenReverting = false) {
        $this->steps[] = new CConsole_Prompt_FormStep($step, $condition, $name, $ignoreWhenReverting);

        return $this;
    }

    /**
     * Run all of the given steps.
     *
     * @return array
     */
    public function submit() {
        $index = 0;
        $wasReverted = false;

        while ($index < count($this->steps)) {
            $step = $this->steps[$index];

            if ($wasReverted && $index > 0 && $step->shouldIgnoreWhenReverting($this->responses)) {
                $index--;

                continue;
            }

            $wasReverted = false;

            $key = $step->name !== null ? $step->name : $index;

            if ($index > 0) {
                CConsole_Prompt::revertUsing(function () use (&$wasReverted) {
                    $wasReverted = true;
                });
            } else {
                CConsole_Prompt::preventReverting();
            }

            try {
                $this->responses[$key] = $step->run(
                    $this->responses,
                    isset($this->responses[$key]) ? $this->responses[$key] : null
                );
            } catch (CConsole_Prompt_Exceptions_FormRevertedException $e) {
                $wasReverted = true;
            }

            if ($wasReverted) {
                $index--;
            } else {
                $index++;
            }
        }

        CConsole_Prompt::preventReverting();

        return $this->responses;
    }

    /**
     * Prompt the user for text input.
     *
     * @param string        $label
     * @param string        $placeholder
     * @param string        $default
     * @param bool|string   $required
     * @param null|callable $validate
     * @param string        $hint
     * @param null|string   $name
     * @param null|Closure  $transform
     *
     * @return $this
     */
    public function text($label, $placeholder = '', $default = '', $required = false, $validate = null, $hint = '', $name = null, $transform = null) {
        return $this->add(function ($responses, $previousResponse) use ($label, $placeholder, $default, $required, $validate, $hint, $transform) {
            if ($previousResponse !== null) {
                $default = $previousResponse;
            }

            return (new CConsole_Prompt_TextPrompt($label, $placeholder, $default, $required, $validate, $hint, $transform))->prompt();
        }, $name);
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
     * @param null|string   $name
     * @param null|Closure  $transform
     *
     * @return $this
     */
    public function textarea($label, $placeholder = '', $default = '', $required = false, $validate = null, $hint = '', $rows = 5, $name = null, $transform = null) {
        return $this->add(function ($responses, $previousResponse) use ($label, $placeholder, $default, $required, $validate, $hint, $rows, $transform) {
            if ($previousResponse !== null) {
                $default = $previousResponse;
            }

            return (new CConsole_Prompt_TextareaPrompt($label, $placeholder, $default, $required, $validate, $hint, $rows, $transform))->prompt();
        }, $name);
    }

    /**
     * Prompt the user for input, hiding the value.
     *
     * @param string        $label
     * @param string        $placeholder
     * @param bool|string   $required
     * @param null|callable $validate
     * @param string        $hint
     * @param null|string   $name
     * @param null|Closure  $transform
     *
     * @return $this
     */
    public function password($label, $placeholder = '', $required = false, $validate = null, $hint = '', $name = null, $transform = null) {
        return $this->add(function ($responses, $previousResponse) use ($label, $placeholder, $required, $validate, $hint, $transform) {
            return (new CConsole_Prompt_PasswordPrompt($label, $placeholder, $required, $validate, $hint, $transform))->prompt();
        }, $name);
    }

    /**
     * Prompt the user to select an option.
     *
     * @param string            $label
     * @param array|CCollection $options
     * @param null|int|string   $default
     * @param int               $scroll
     * @param null|callable     $validate
     * @param string            $hint
     * @param bool|string       $required
     * @param null|string       $name
     * @param null|Closure      $transform
     *
     * @return $this
     */
    public function select($label, $options, $default = null, $scroll = 5, $validate = null, $hint = '', $required = true, $name = null, $transform = null) {
        return $this->add(function ($responses, $previousResponse) use ($label, $options, $default, $scroll, $validate, $hint, $required, $transform) {
            if ($previousResponse !== null) {
                $default = $previousResponse;
            }

            return (new CConsole_Prompt_SelectPrompt($label, $options, $default, $scroll, $validate, $hint, $required, $transform))->prompt();
        }, $name);
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
     * @param null|string       $name
     * @param null|Closure      $transform
     *
     * @return $this
     */
    public function multiselect($label, $options, $default = [], $scroll = 5, $required = false, $validate = null, $hint = 'Use the space bar to select options.', $name = null, $transform = null) {
        return $this->add(function ($responses, $previousResponse) use ($label, $options, $default, $scroll, $required, $validate, $hint, $transform) {
            if ($previousResponse !== null) {
                $default = $previousResponse;
            }

            return (new CConsole_Prompt_MultiSelectPrompt($label, $options, $default, $scroll, $required, $validate, $hint, $transform))->prompt();
        }, $name);
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
     * @param null|string   $name
     * @param null|Closure  $transform
     *
     * @return $this
     */
    public function confirm($label, $default = true, $yes = 'Yes', $no = 'No', $required = false, $validate = null, $hint = '', $name = null, $transform = null) {
        return $this->add(function ($responses, $previousResponse) use ($label, $default, $yes, $no, $required, $validate, $hint, $transform) {
            if ($previousResponse !== null) {
                $default = $previousResponse;
            }

            return (new CConsole_Prompt_ConfirmPrompt($label, $default, $yes, $no, $required, $validate, $hint, $transform))->prompt();
        }, $name);
    }

    /**
     * Prompt the user to continue or cancel after pausing.
     *
     * @param string      $message
     * @param null|string $name
     *
     * @return $this
     */
    public function pause($message = 'Press enter to continue...', $name = null) {
        return $this->add(function ($responses, $previousResponse) use ($message) {
            return (new CConsole_Prompt_PausePrompt($message))->prompt();
        }, $name);
    }

    /**
     * Prompt the user for text input with auto-completion.
     *
     * @param string                    $label
     * @param array|Closure|CCollection $options
     * @param string                    $placeholder
     * @param string                    $default
     * @param int                       $scroll
     * @param bool|string               $required
     * @param null|callable             $validate
     * @param string                    $hint
     * @param null|string               $name
     * @param null|Closure              $transform
     *
     * @return $this
     */
    public function suggest($label, $options, $placeholder = '', $default = '', $scroll = 5, $required = false, $validate = null, $hint = '', $name = null, $transform = null) {
        return $this->add(function ($responses, $previousResponse) use ($label, $options, $placeholder, $default, $scroll, $required, $validate, $hint, $transform) {
            if ($previousResponse !== null) {
                $default = $previousResponse;
            }

            return (new CConsole_Prompt_SuggestPrompt($label, $options, $placeholder, $default, $scroll, $required, $validate, $hint, $transform))->prompt();
        }, $name);
    }

    /**
     * Allow the user to search for an option.
     *
     * @param string        $label
     * @param Closure       $options
     * @param string        $placeholder
     * @param int           $scroll
     * @param null|callable $validate
     * @param string        $hint
     * @param bool|string   $required
     * @param null|string   $name
     * @param null|Closure  $transform
     *
     * @return $this
     */
    public function search($label, Closure $options, $placeholder = '', $scroll = 5, $validate = null, $hint = '', $required = true, $name = null, $transform = null) {
        return $this->add(function ($responses, $previousResponse) use ($label, $options, $placeholder, $scroll, $validate, $hint, $required, $transform) {
            return (new CConsole_Prompt_SearchPrompt($label, $options, $placeholder, $scroll, $validate, $hint, $required, $transform))->prompt();
        }, $name);
    }

    /**
     * Allow the user to search for multiple options.
     *
     * @param string        $label
     * @param Closure       $options
     * @param string        $placeholder
     * @param int           $scroll
     * @param bool|string   $required
     * @param null|callable $validate
     * @param string        $hint
     * @param null|string   $name
     * @param null|Closure  $transform
     *
     * @return $this
     */
    public function multisearch($label, Closure $options, $placeholder = '', $scroll = 5, $required = false, $validate = null, $hint = 'Use the space bar to select options.', $name = null, $transform = null) {
        return $this->add(function ($responses, $previousResponse) use ($label, $options, $placeholder, $scroll, $required, $validate, $hint, $transform) {
            return (new CConsole_Prompt_MultiSearchPrompt($label, $options, $placeholder, $scroll, $required, $validate, $hint, $transform))->prompt();
        }, $name);
    }

    /**
     * Render a spinner while the given callback is executing.
     *
     * @param Closure     $callback
     * @param string      $message
     * @param null|string $name
     *
     * @return $this
     */
    public function spin(Closure $callback, $message = '', $name = null) {
        return $this->add(function ($responses, $previousResponse) use ($callback, $message) {
            return (new CConsole_Prompt_Spinner($message))->spin($callback);
        }, $name, true);
    }

    /**
     * Display a note.
     *
     * @param string      $message
     * @param null|string $type
     * @param null|string $name
     *
     * @return $this
     */
    public function note($message, $type = null, $name = null) {
        return $this->add(function ($responses, $previousResponse) use ($message, $type) {
            return (new CConsole_Prompt_Note($message, $type))->display();
        }, $name, true);
    }

    /**
     * Display an error.
     *
     * @param string      $message
     * @param null|string $name
     *
     * @return $this
     */
    public function error($message, $name = null) {
        return $this->add(function ($responses, $previousResponse) use ($message) {
            return (new CConsole_Prompt_Note($message, 'error'))->display();
        }, $name, true);
    }

    /**
     * Display a warning.
     *
     * @param string      $message
     * @param null|string $name
     *
     * @return $this
     */
    public function warning($message, $name = null) {
        return $this->add(function ($responses, $previousResponse) use ($message) {
            return (new CConsole_Prompt_Note($message, 'warning'))->display();
        }, $name, true);
    }

    /**
     * Display an alert.
     *
     * @param string      $message
     * @param null|string $name
     *
     * @return $this
     */
    public function alert($message, $name = null) {
        return $this->add(function ($responses, $previousResponse) use ($message) {
            return (new CConsole_Prompt_Note($message, 'alert'))->display();
        }, $name, true);
    }

    /**
     * Display an informational message.
     *
     * @param string      $message
     * @param null|string $name
     *
     * @return $this
     */
    public function info($message, $name = null) {
        return $this->add(function ($responses, $previousResponse) use ($message) {
            return (new CConsole_Prompt_Note($message, 'info'))->display();
        }, $name, true);
    }

    /**
     * Display an introduction.
     *
     * @param string      $message
     * @param null|string $name
     *
     * @return $this
     */
    public function intro($message, $name = null) {
        return $this->add(function ($responses, $previousResponse) use ($message) {
            return (new CConsole_Prompt_Note($message, 'intro'))->display();
        }, $name, true);
    }

    /**
     * Display a closing message.
     *
     * @param string      $message
     * @param null|string $name
     *
     * @return $this
     */
    public function outro($message, $name = null) {
        return $this->add(function ($responses, $previousResponse) use ($message) {
            return (new CConsole_Prompt_Note($message, 'outro'))->display();
        }, $name, true);
    }

    /**
     * Display a table.
     *
     * @param array|CCollection      $headers
     * @param null|array|CCollection $rows
     * @param null|string            $name
     *
     * @return $this
     */
    public function table($headers = [], $rows = null, $name = null) {
        return $this->add(function ($responses, $previousResponse) use ($headers, $rows) {
            return (new CConsole_Prompt_Table($headers, $rows))->display();
        }, $name, true);
    }

    /**
     * Display a progress bar.
     *
     * @param string       $label
     * @param int|iterable $steps
     * @param null|Closure $callback
     * @param string       $hint
     * @param null|string  $name
     *
     * @return $this
     */
    public function progress($label, $steps, $callback = null, $hint = '', $name = null) {
        return $this->add(function ($responses, $previousResponse) use ($label, $steps, $callback, $hint) {
            $progress = new CConsole_Prompt_Progress($label, $steps, $hint);

            if ($callback !== null) {
                return $progress->map($callback);
            }

            return $progress;
        }, $name, true);
    }
}

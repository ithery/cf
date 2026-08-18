<?php

trait CConsole_Prompt_Concerns_Themes {
    /**
     * The name of the active theme.
     *
     * @var string
     */
    protected static $theme = 'default';

    /**
     * The available themes.
     *
     * @var array<string, array<class-string<CConsole_Prompt>, class-string<object&callable>>>
     */
    protected static $themes = [
        'default' => [
            CConsole_Prompt_TextPrompt::class => CConsole_Prompt_Themes_Default_TextPromptRenderer::class,
            CConsole_Prompt_NumberPrompt::class => CConsole_Prompt_Themes_Default_NumberPromptRenderer::class,
            CConsole_Prompt_TextareaPrompt::class => CConsole_Prompt_Themes_Default_TextareaPromptRenderer::class,
            CConsole_Prompt_PasswordPrompt::class => CConsole_Prompt_Themes_Default_PasswordPromptRenderer::class,
            CConsole_Prompt_SelectPrompt::class => CConsole_Prompt_Themes_Default_SelectPromptRenderer::class,
            CConsole_Prompt_MultiSelectPrompt::class => CConsole_Prompt_Themes_Default_MultiSelectPromptRenderer::class,
            CConsole_Prompt_ConfirmPrompt::class => CConsole_Prompt_Themes_Default_ConfirmPromptRenderer::class,
            CConsole_Prompt_PausePrompt::class => CConsole_Prompt_Themes_Default_PausePromptRenderer::class,
            CConsole_Prompt_SearchPrompt::class => CConsole_Prompt_Themes_Default_SearchPromptRenderer::class,
            CConsole_Prompt_MultiSearchPrompt::class => CConsole_Prompt_Themes_Default_MultiSearchPromptRenderer::class,
            CConsole_Prompt_SuggestPrompt::class => CConsole_Prompt_Themes_Default_SuggestPromptRenderer::class,
            CConsole_Prompt_Spinner::class => CConsole_Prompt_Themes_Default_SpinnerRenderer::class,
            CConsole_Prompt_Note::class => CConsole_Prompt_Themes_Default_NoteRenderer::class,
            CConsole_Prompt_Table::class => CConsole_Prompt_Themes_Default_TableRenderer::class,
            CConsole_Prompt_Progress::class => CConsole_Prompt_Themes_Default_ProgressRenderer::class,
            CConsole_Prompt_Clear::class => CConsole_Prompt_Themes_Default_ClearRenderer::class,
            CConsole_Prompt_Grid::class => CConsole_Prompt_Themes_Default_GridRenderer::class,
            CConsole_Prompt_AutoCompletePrompt::class => CConsole_Prompt_Themes_Default_AutoCompletePromptRenderer::class,
            CConsole_Prompt_Title::class => CConsole_Prompt_Themes_Default_TitleRenderer::class,
            CConsole_Prompt_Stream::class => CConsole_Prompt_Themes_Default_StreamRenderer::class,
            CConsole_Prompt_Task::class => CConsole_Prompt_Themes_Default_TaskRenderer::class,
            CConsole_Prompt_DataTablePrompt::class => CConsole_Prompt_Themes_Default_DataTableRenderer::class,
            CConsole_Prompt_Callout::class => CConsole_Prompt_Themes_Default_CalloutRenderer::class,
        ],
    ];

    /**
     * Get or set the active theme.
     *
     * @param null|string $name
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    public static function theme($name = null) {
        if ($name === null) {
            return static::$theme;
        }

        if (!isset(static::$themes[$name])) {
            throw new InvalidArgumentException("Prompt theme [{$name}] not found.");
        }

        return static::$theme = $name;
    }

    /**
     * Add a new theme.
     *
     * @param string $name
     * @param array<class-string<CConsole_Prompt>, class-string<object&callable>> $renderers
     *
     * @return void
     */
    public static function addTheme($name, array $renderers) {
        if ($name === 'default') {
            throw new InvalidArgumentException('The default theme cannot be overridden.');
        }

        static::$themes[$name] = $renderers;
    }

    /**
     * Get the renderer for the current prompt.
     *
     * @return callable
     */
    protected function getRenderer() {
        $class = get_class($this);
        $rendererClass = carr::get(static::$themes[static::$theme], $class, carr::get(static::$themes['default'], $class));

        return new $rendererClass($this);
    }

    /**
     * Render the prompt using the active theme.
     *
     * @return string
     */
    protected function renderTheme() {
        $renderer = $this->getRenderer();

        return (string) $renderer($this);
    }
}

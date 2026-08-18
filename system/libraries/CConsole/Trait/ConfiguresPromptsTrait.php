<?php

use Symfony\Component\Console\Input\InputInterface;

trait CConsole_Trait_ConfiguresPromptsTrait {
    /**
     * Configure the prompt fallbacks.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return void
     */
    protected function configurePrompts(InputInterface $input) {
        CConsole_Prompt::setOutput($this->output);

        CConsole_Prompt::interactive(($input->isInteractive() && defined('STDIN') && stream_isatty(STDIN)) || CF::isTesting());

        CConsole_Prompt::fallbackWhen(c::windowsOs() || CF::isTesting());

        CConsole_Prompt_TextPrompt::fallbackUsing(function (CConsole_Prompt_TextPrompt $prompt) {
            return $this->promptUntilValid(
                function () use ($prompt) {
                    return $this->components->ask($prompt->label, $prompt->default ?: null) ?? '';
                },
                $prompt->required,
                $prompt->validate
            );
        });

        CConsole_Prompt_PasswordPrompt::fallbackUsing(function (CConsole_Prompt_PasswordPrompt $prompt) {
            return $this->promptUntilValid(
                function () use ($prompt) {
                    return $this->components->secret($prompt->label) ?? '';
                },
                $prompt->required,
                $prompt->validate
            );
        });

        CConsole_Prompt_ConfirmPrompt::fallbackUsing(function (CConsole_Prompt_ConfirmPrompt $prompt) {
            return $this->promptUntilValid(
                function () use ($prompt) {
                    return $this->components->confirm($prompt->label, $prompt->default);
                },
                $prompt->required,
                $prompt->validate
            );
        });

        CConsole_Prompt_SelectPrompt::fallbackUsing(function (CConsole_Prompt_SelectPrompt $prompt) {
            return $this->promptUntilValid(
                function () use ($prompt) {
                    return $this->components->choice($prompt->label, $prompt->options, $prompt->default);
                },
                false,
                $prompt->validate
            );
        });

        CConsole_Prompt_MultiSelectPrompt::fallbackUsing(function (CConsole_Prompt_MultiSelectPrompt $prompt) {
            if ($prompt->default !== []) {
                return $this->promptUntilValid(
                    function () use ($prompt) {
                        return $this->components->choice($prompt->label, $prompt->options, implode(',', $prompt->default), null, true);
                    },
                    $prompt->required,
                    $prompt->validate
                );
            }

            return $this->promptUntilValid(
                function () use ($prompt) {
                    return c::collect($this->components->choice($prompt->label, array_merge(['' => 'None'], $prompt->options), 'None', null, true))
                        ->reject('')
                        ->all();
                },
                $prompt->required,
                $prompt->validate
            );
        });

        CConsole_Prompt_SuggestPrompt::fallbackUsing(function (CConsole_Prompt_SuggestPrompt $prompt) {
            return $this->promptUntilValid(
                function () use ($prompt) {
                    return $this->components->askWithCompletion($prompt->label, $prompt->options, $prompt->default ?: null) ?? '';
                },
                $prompt->required,
                $prompt->validate
            );
        });

        CConsole_Prompt_SearchPrompt::fallbackUsing(function (CConsole_Prompt_SearchPrompt $prompt) {
            return $this->promptUntilValid(
                function () use ($prompt) {
                    $query = $this->components->ask($prompt->label);

                    $options = call_user_func($prompt->options, $query);

                    return $this->components->choice($prompt->label, $options);
                },
                false,
                $prompt->validate
            );
        });

        CConsole_Prompt_MultiSearchPrompt::fallbackUsing(function (CConsole_Prompt_MultiSearchPrompt $prompt) {
            return $this->promptUntilValid(
                function () use ($prompt) {
                    $query = $this->components->ask($prompt->label);

                    $options = call_user_func($prompt->options, $query);

                    if ($prompt->required === false) {
                        if ($this->isListArray($options)) {
                            return c::collect($this->components->choice($prompt->label, array_merge(['None'], $options), 'None', null, true))
                                ->reject('None')
                                ->values()
                                ->all();
                        }

                        return c::collect($this->components->choice($prompt->label, array_merge(['' => 'None'], $options), '', null, true))
                            ->reject('')
                            ->values()
                            ->all();
                    }

                    return $this->components->choice($prompt->label, $options, null, null, true);
                },
                $prompt->required,
                $prompt->validate
            );
        });
    }

    /**
     * Prompt the user until the given validation callback passes.
     *
     * @param Closure     $prompt
     * @param bool|string $required
     * @param null|Closure $validate
     *
     * @return mixed
     */
    protected function promptUntilValid($prompt, $required, $validate) {
        while (true) {
            $result = $prompt();

            if ($required && ($result === '' || $result === [] || $result === false)) {
                $this->components->error(is_string($required) ? $required : 'Required.');

                continue;
            }

            if ($validate) {
                $error = $validate($result);

                if (is_string($error) && strlen($error) > 0) {
                    $this->components->error($error);

                    continue;
                }
            }

            return $result;
        }
    }

    /**
     * Determine whether the given array is a list (sequential integer keys starting at 0).
     *
     * @param array $array
     *
     * @return bool
     */
    protected function isListArray(array $array) {
        return array_keys($array) === range(0, count($array) - 1);
    }

    /**
     * Restore the prompts output.
     *
     * @return void
     */
    protected function restorePrompts() {
        CConsole_Prompt::setOutput($this->output);
    }
}

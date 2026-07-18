<?php

class CConsole_Prompt_Themes_Default_CalloutRenderer extends CConsole_Prompt_Themes_Default_Renderer {
    use CConsole_Prompt_Themes_Default_Concerns_DrawsBoxes;

    /**
     * Render the text prompt.
     *
     * @param CConsole_Prompt_Callout $prompt
     *
     * @return string
     */
    public function __invoke($prompt) {
        $content = is_array($prompt->content) ? $prompt->content : [$prompt->content];

        $sections = [];

        foreach ($content as $part) {
            $result = $this->resolvePart($part);

            if (is_array($result)) {
                $sections[] = implode(PHP_EOL, $result);
            } else {
                $sections[] = implode(PHP_EOL, $this->ansiWordwrap($result, $this->minWidth));
            }
        }

        $message = implode(PHP_EOL . PHP_EOL, $sections);

        if ($prompt->type === 'error') {
            return $this->box(
                $this->red($this->truncate('⚠ ' . $prompt->label, $prompt->terminal()->cols() - 6)),
                $message,
                '',
                'red',
                $prompt->info
            );
        }

        if ($prompt->type === 'warning') {
            return $this->box(
                $this->yellow($this->truncate('⚠ ' . $prompt->label, $prompt->terminal()->cols() - 6)),
                $message,
                '',
                'yellow',
                $prompt->info
            );
        }

        return $this->box(
            $this->cyan($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
            $message,
            '',
            'gray',
            $prompt->info
        );
    }

    /**
     * Resolve a part of the callout content into a string or array of strings.
     *
     * @param CConsole_Prompt_Elements_ElementContract|string $part
     *
     * @return array<int, string>|string
     */
    protected function resolvePart($part) {
        if (is_string($part)) {
            return $this->autoFormat($part);
        }
        if ($part instanceof CConsole_Prompt_Elements_Heading) {
            return $this->bold($this->autoFormat($part->text));
        }
        if ($part instanceof CConsole_Prompt_Elements_BulletedList) {
            return $this->renderBulletedList($part);
        }
        if ($part instanceof CConsole_Prompt_Elements_NumberedList) {
            return $this->renderNumberedList($part);
        }
        if ($part instanceof CConsole_Prompt_Elements_KeyValueList) {
            return $this->renderKeyValueList($part);
        }
        if ($part instanceof CConsole_Prompt_Elements_Link) {
            return $this->renderLink($part);
        }

        $type = is_object($part) ? get_class($part) : gettype($part);

        throw new InvalidArgumentException('Unsupported callout content part: ' . $type);
    }

    /**
     * Render a bulleted list element.
     *
     * @param CConsole_Prompt_Elements_BulletedList $part
     *
     * @return array<int, string>
     */
    protected function renderBulletedList($part) {
        $finalLines = [];

        foreach ($part->items as $i => $p) {
            $p = $this->autoFormat($p);
            $lines = $this->ansiWordwrap($p, $this->minWidth - 2);
            $partLines = [];

            if ($part->spaced && $i !== 0) {
                $partLines[] = '';
            }

            foreach ($lines as $index => $line) {
                if ($index === 0) {
                    $partLines[] = $this->dim('·') . ' ' . $line;
                } else {
                    $partLines[] = '  ' . $line;
                }
            }

            $finalLines[] = implode(PHP_EOL, $partLines);
        }

        return $finalLines;
    }

    /**
     * Render a numbered list element.
     *
     * @param CConsole_Prompt_Elements_NumberedList $part
     *
     * @return array<int, string>
     */
    protected function renderNumberedList($part) {
        $finalLines = [];
        // +1 for "."
        $widestNumber = mb_strwidth((string) count($part->items)) + 1;

        foreach ($part->items as $i => $p) {
            $partLines = [];
            // -1 for ' ' after number
            $p = $this->autoFormat($p);
            $lines = $this->ansiWordwrap($p, $this->minWidth - $widestNumber - 1);

            if ($part->spaced && $i !== 0) {
                $partLines[] = '';
            }

            foreach ($lines as $index => $line) {
                if ($index === 0) {
                    $partLines[] = $this->dim(CConsole_Prompt_Support_Utils::mbStrPad(($i + 1) . '.', $widestNumber, ' ', STR_PAD_LEFT)) . ' ' . $line;
                } else {
                    // +1 for ' ' after number
                    $partLines[] = str_repeat(' ', $widestNumber + 1) . $line;
                }
            }

            $finalLines[] = implode(PHP_EOL, $partLines);
        }

        return $finalLines;
    }

    /**
     * Render a key-value list element.
     *
     * @param CConsole_Prompt_Elements_KeyValueList $part
     *
     * @return array<int, string>
     */
    protected function renderKeyValueList($part) {
        $items = $part->items;
        $keys = array_keys($items);
        $widestKey = max(array_map(function ($key) {
            return mb_strwidth($key);
        }, $keys));

        $finalLines = [];

        foreach ($items as $key => $value) {
            $paddedKey = CConsole_Prompt_Support_Utils::mbStrPad($key, $widestKey);
            $value = $this->autoFormat($value);
            $lines = $this->ansiWordwrap($value, $this->minWidth - $widestKey - 2);

            foreach ($lines as $index => $line) {
                if ($index === 0) {
                    $finalLines[] = $this->dim($paddedKey) . '  ' . $line;
                } else {
                    $finalLines[] = str_repeat(' ', $widestKey + 2) . $line;
                }
            }
        }

        return $finalLines;
    }

    /**
     * Render a link element.
     *
     * @param CConsole_Prompt_Elements_Link $part
     *
     * @return string
     */
    protected function renderLink($part) {
        $text = $part->underline
            ? "\e[4;36m{$part->label}\e[0m"
            : $this->cyan($part->label);

        return "\e]8;;{$part->url}\e\\{$text}\e]8;;\e\\";
    }

    /**
     * Auto-format the text by applying styles to specific patterns, such as inline code blocks.
     *
     * @param string $text
     *
     * @return string
     */
    protected function autoFormat($text) {
        $text = preg_replace('/`([^`]+)`/', $this->cyan('`$1`'), $text);

        $text = preg_replace_callback('/\e\]8;;(.+?)\e\\\\(.*?)\e\]8;;\e\\\\/', function ($matches) {
            $visibleText = $this->stripEscapeSequences($matches[2]);
            $hadUnderline = cstr::contains($matches[2], "\e[4m");
            $styled = $hadUnderline
                ? "\e[4;36m{$visibleText}\e[0m"
                : $this->cyan($visibleText);

            return "\e]8;;{$matches[1]}\e\\{$styled}\e]8;;\e\\";
        }, $text);

        return $text;
    }
}

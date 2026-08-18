<?php

class CConsole_Prompt_Themes_Default_NoteRenderer extends CConsole_Prompt_Themes_Default_Renderer {
    /**
     * Render the note.
     *
     * @param CConsole_Prompt_Note $note
     *
     * @return $this
     */
    public function __invoke($note) {
        $lines = explode(PHP_EOL, $note->message);

        switch ($note->type) {
            case 'intro':
            case 'outro':
                $lines = array_map(function ($line) {
                    return " {$line} ";
                }, $lines);
                $longest = max(array_map(function ($line) {
                    return mb_strlen($line);
                }, $lines));

                foreach ($lines as $line) {
                    $line = CConsole_Prompt_Support_Utils::mbStrPad($line, $longest, ' ');
                    $this->line(" {$this->bgCyan($this->black($line))}");
                }

                return $this;

            case 'warning':
                foreach ($lines as $line) {
                    $this->line($this->yellow(" {$line}"));
                }

                return $this;

            case 'error':
                foreach ($lines as $line) {
                    $this->line($this->red(" {$line}"));
                }

                return $this;

            case 'alert':
                foreach ($lines as $line) {
                    $this->line(" {$this->bgRed($this->white(" {$line} "))}");
                }

                return $this;

            case 'info':
                foreach ($lines as $line) {
                    $this->line($this->green(" {$line}"));
                }

                return $this;

            default:
                foreach ($lines as $line) {
                    $this->line(" {$line}");
                }

                return $this;
        }
    }
}

<?php

class CConsole_Prompt_Themes_Default_ProgressRenderer extends CConsole_Prompt_Themes_Default_Renderer {
    use CConsole_Prompt_Themes_Default_Concerns_DrawsBoxes;

    /**
     * The character to use for the progress bar.
     *
     * @var string
     */
    protected $barCharacter = '█';

    /**
     * Render the progress bar.
     *
     * @param CConsole_Prompt_Progress $progress
     *
     * @return string
     */
    public function __invoke($progress) {
        $filled = str_repeat($this->barCharacter, (int) ceil($progress->percentage() * min($this->minWidth, $progress->terminal()->cols() - 6)));

        if ($progress->state === 'submit') {
            return $this->box(
                $this->dim($this->truncate($progress->label, $progress->terminal()->cols() - 6)),
                $this->dim($filled),
                '',
                'gray',
                $this->fractionCompleted($progress)
            );
        }

        if ($progress->state === 'error') {
            return $this->box(
                $this->truncate($progress->label, $progress->terminal()->cols() - 6),
                $this->dim($filled),
                '',
                'red',
                $this->fractionCompleted($progress)
            );
        }

        if ($progress->state === 'cancel') {
            return $this->box(
                $this->truncate($progress->label, $progress->terminal()->cols() - 6),
                $this->dim($filled),
                '',
                'red',
                $this->fractionCompleted($progress)
            )->error($progress->cancelMessage);
        }

        return $this->box(
            $this->cyan($this->truncate($progress->label, $progress->terminal()->cols() - 6)),
            $this->dim($filled),
            '',
            'gray',
            $this->fractionCompleted($progress)
        )->when(
            $progress->hint,
            function () use ($progress) {
                $this->hint($progress->hint);
            },
            function () {
                $this->newLine(); // Space for errors
            }
        );
    }

    /**
     * @param CConsole_Prompt_Progress $progress
     *
     * @return string
     */
    protected function fractionCompleted($progress) {
        return number_format($progress->progress) . ' / ' . number_format($progress->total);
    }
}

<?php

class CConsole_Prompt_Themes_Default_StreamRenderer extends CConsole_Prompt_Themes_Default_Renderer {
    /**
     * Render the stream.
     *
     * @param CConsole_Prompt_Stream $stream
     *
     * @return $this
     */
    public function __invoke($stream) {
        foreach ($stream->lines() as $line) {
            $this->line(" {$line}");
        }

        return $this;
    }
}

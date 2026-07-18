<?php

trait CConsole_Prompt_Concerns_HasInfo {
    /**
     * Get the resolved info text.
     *
     * @return string
     */
    public function infoText() {
        if ($this->info instanceof Closure) {
            $value = call_user_func($this->info, $this->highlightedValue());

            return $value === null ? '' : $value;
        }

        return $this->info;
    }
}

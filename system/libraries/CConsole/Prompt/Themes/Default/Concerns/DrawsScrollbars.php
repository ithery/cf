<?php

trait CConsole_Prompt_Themes_Default_Concerns_DrawsScrollbars {
    /**
     * Render a scrollbar beside the visible items.
     *
     * @param array<int, string>|CCollection $visible
     * @param int                             $firstVisible
     * @param int                             $height
     * @param int                             $total
     * @param int                             $width
     * @param string                          $color
     *
     * @return array<int, string>|CCollection
     */
    protected function scrollbar($visible, $firstVisible, $height, $total, $width, $color = 'cyan') {
        if ($height >= $total) {
            return $visible;
        }

        $scrollPosition = $this->scrollPosition($firstVisible, $height, $total);

        $lines = $visible instanceof CCollection ? $visible->all() : $visible;

        $result = array_map(function ($line, $index) use ($scrollPosition, $color, $width) {
            if ($index === $scrollPosition) {
                $replaced = preg_replace('/.$/', $this->{$color}('┃'), $this->pad($line, $width));
            } else {
                $replaced = preg_replace('/.$/', $this->gray('│'), $this->pad($line, $width));
            }

            return $replaced === null ? '' : $replaced;
        }, array_values($lines), range(0, count($lines) - 1));

        return $visible instanceof CCollection ? new CCollection($result) : $result;
    }

    /**
     * Return the position where the scrollbar "handle" should be rendered.
     *
     * @param int $firstVisible
     * @param int $height
     * @param int $total
     *
     * @return int
     */
    protected function scrollPosition($firstVisible, $height, $total) {
        if ($firstVisible === 0) {
            return 0;
        }

        $maxPosition = $total - $height;

        if ($firstVisible === $maxPosition) {
            return $height - 1;
        }

        if ($height <= 2) {
            return -1;
        }

        $percent = $firstVisible / $maxPosition;

        return (int) round($percent * ($height - 3)) + 1;
    }
}

<?php

class CConsole_Prompt_Themes_Default_DataTableRenderer extends CConsole_Prompt_Themes_Default_Renderer implements CConsole_Prompt_Themes_Contracts_Scrolling {
    use CConsole_Prompt_Themes_Default_Concerns_DrawsBoxes;
    use CConsole_Prompt_Themes_Default_Concerns_DrawsScrollbars;

    /**
     * Render the data table.
     *
     * @param CConsole_Prompt_DataTablePrompt $prompt
     *
     * @return string
     */
    public function __invoke($prompt) {
        $maxWidth = $prompt->terminal()->cols() - 6;

        if ($prompt->state === 'submit') {
            return $this->renderSubmit($prompt, $maxWidth);
        }
        if ($prompt->state === 'cancel') {
            return $this->renderCancel($prompt, $maxWidth);
        }

        return $this->renderActive($prompt, $maxWidth);
    }

    /**
     * Render the submit state.
     *
     * @param CConsole_Prompt_DataTablePrompt $prompt
     * @param int                               $maxWidth
     *
     * @return string
     */
    protected function renderSubmit($prompt, $maxWidth) {
        $row = $prompt->selectedRow();
        $display = $row ? $this->truncate(implode(', ', $row), $maxWidth) : '';

        return $this->box(
            $this->dim($this->truncate($prompt->label, $maxWidth)),
            $display
        );
    }

    /**
     * Render the cancel state.
     *
     * @param CConsole_Prompt_DataTablePrompt $prompt
     * @param int                               $maxWidth
     *
     * @return string
     */
    protected function renderCancel($prompt, $maxWidth) {
        $filtered = $prompt->filteredRows();
        $visible = $prompt->visible();

        $numCols = !empty($prompt->headers)
            ? count($prompt->headers)
            : max(array_map('count', $prompt->rows));

        $widths = $this->computeColumnWidths($prompt->headers, $prompt->rows, $numCols, $maxWidth);
        $innerWidth = array_sum($widths) + ($numCols * 2) + ($numCols - 1) + 2;

        // Top border (red)
        $titleText = $this->truncate($prompt->label, $maxWidth);
        $titleLength = mb_strwidth($this->stripEscapeSequences($titleText));
        $topBorderFill = max(0, $innerWidth - $titleLength - 2);
        $this->line($this->red(' ┌') . " {$titleText} " . $this->red(str_repeat('─', $topBorderFill) . '┐'));

        // Search line (dimmed, to prevent layout shift)
        $searchContent = $this->renderSearchLine($prompt, $innerWidth - 2);
        $this->line($this->red(' │') . ' ' . $this->dim($this->pad($searchContent, $innerWidth - 2)) . ' ' . $this->red('│'));

        // Column separator
        $this->line(' ' . $this->renderBorder('├', '┬', '┤', $widths, 'red'));

        // Header cells (strikethrough + dim)
        if (!empty($prompt->headers)) {
            $headerCells = [];

            foreach ($widths as $i => $w) {
                $header = isset($prompt->headers[$i]) ? $prompt->headers[$i] : '';
                $text = is_array($header) ? implode(' ', $header) : $header;
                $headerCells[] = $this->dim(' ' . $this->pad($this->strikethrough($this->truncate($text, $w)), $w) . ' ');
            }

            $headerLine = implode($this->red('│'), $headerCells) . '  ';
            $this->line($this->red(' │') . $this->pad($headerLine, $innerWidth) . $this->red('│'));

            $this->line(' ' . $this->renderBorder('├', '┼', '┤', $widths, 'red'));
        }

        // Data rows (strikethrough + dim)
        $dataLines = $this->renderDataRows($prompt, $filtered, $visible, $widths, $numCols, $innerWidth, true);

        foreach ($dataLines as $dataLine) {
            $this->line($this->red(' │') . $this->pad($dataLine, $innerWidth) . $this->red('│'));
        }

        // Bottom border (red)
        $this->line(' ' . $this->renderBorder('└', '┴', '┘', $widths, 'red'));

        return $this->error($prompt->cancelMessage);
    }

    /**
     * Render the active/browse/search state.
     *
     * @param CConsole_Prompt_DataTablePrompt $prompt
     * @param int                               $maxWidth
     *
     * @return string
     */
    protected function renderActive($prompt, $maxWidth) {
        $filtered = $prompt->filteredRows();
        $total = count($filtered);
        $visible = $prompt->visible();

        $numCols = !empty($prompt->headers)
            ? count($prompt->headers)
            : max(array_map('count', $prompt->rows));

        // Compute column widths from ALL rows (not filtered) to prevent layout shift when searching
        $widths = $this->computeColumnWidths($prompt->headers, $prompt->rows, $numCols, $maxWidth);

        // Inner width between the outer │ chars:
        // cells (sum of w+2 padding each) + separators (numCols-1) + 2 (scrollbar area)
        $innerWidth = array_sum($widths) + ($numCols * 2) + ($numCols - 1) + 2;

        // Top border: ┌ Title ───┐
        $titleText = $this->cyan($this->truncate($prompt->label, $maxWidth));
        $titleLength = mb_strwidth($this->stripEscapeSequences($titleText));
        $topBorderFill = max(0, $innerWidth - $titleLength - 2);
        $this->line($this->gray(' ┌') . " {$titleText} " . $this->gray(str_repeat('─', $topBorderFill) . '┐'));

        // Search line: │ / Search              │
        $searchContent = $this->renderSearchLine($prompt, $innerWidth - 2);
        $this->line($this->gray(' │') . ' ' . $this->pad($searchContent, $innerWidth - 2) . ' ' . $this->gray('│'));

        if ($total === 0) {
            // No results: simple box without column separators
            $this->line(' ' . $this->renderSimpleBorder('├', '┤', $innerWidth));

            $message = $prompt->searchValue() !== '' ? 'No results found.' : 'No rows.';
            $emptyLine = $this->pad(' ' . $this->dim($message), $innerWidth);
            $this->line($this->gray(' │') . $this->pad($emptyLine, $innerWidth) . $this->gray('│'));

            $this->line(' ' . $this->renderSimpleBorder('└', '┘', $innerWidth));
        } else {
            // Column separator: ├──────┬────────┤
            $this->line(' ' . $this->renderBorder('├', '┬', '┤', $widths));

            // Header cells: │ Header │ Header   │
            if (!empty($prompt->headers)) {
                $headerCells = [];

                foreach ($widths as $i => $w) {
                    $header = isset($prompt->headers[$i]) ? $prompt->headers[$i] : '';
                    $text = is_array($header) ? implode(' ', $header) : $header;
                    $headerCells[] = $this->dim(' ' . $this->pad($this->truncate($text, $w), $w) . ' ');
                }

                $headerLine = implode($this->gray('│'), $headerCells) . '  ';
                $this->line($this->gray(' │') . $this->pad($headerLine, $innerWidth) . $this->gray('│'));

                // Header separator: ├──────┼────────┤
                $this->line(' ' . $this->renderBorder('├', '┼', '┤', $widths));
            }

            // Data rows
            $dataLines = $this->renderDataRows($prompt, $filtered, $visible, $widths, $numCols, $innerWidth);

            foreach ($dataLines as $dataLine) {
                $this->line($this->gray(' │') . $this->pad($dataLine, $innerWidth) . $this->gray('│'));
            }

            // Bottom border: └──────┴────────┘
            $this->line(' ' . $this->renderBorder('└', '┴', '┘', $widths));

            // Info line below the box (only when not all rows are visible)
            if ($total > $prompt->scroll) {
                $firstRow = $prompt->firstVisible + 1;
                $lastRow = min($prompt->firstVisible + $prompt->scroll, $total);
                $suffix = $prompt->searchValue() !== '' ? ' results' : '';
                $info = $this->dim('  Viewing ') . $firstRow . '-' . $lastRow . $this->dim(' of ') . $total . $suffix;
                $this->line($info);
            }
        }

        if ($prompt->state === 'error') {
            return $this->warning($this->truncate($prompt->error, $prompt->terminal()->cols() - 5));
        }

        if ($prompt->hint) {
            return $this->hint($prompt->hint);
        }

        return $this->newLine();
    }

    /**
     * Render a column-aware border line.
     *
     * @param string          $left
     * @param string          $mid
     * @param string          $right
     * @param array<int, int> $widths
     * @param string          $color
     *
     * @return string
     */
    protected function renderBorder($left, $mid, $right, array $widths, $color = 'gray') {
        $segments = array_map(function ($w) {
            return str_repeat('─', $w + 2);
        }, $widths);

        return $this->{$color}($left . implode($mid, $segments) . '──' . $right);
    }

    /**
     * Render a simple border line without column separators.
     *
     * @param string $left
     * @param string $right
     * @param int    $innerWidth
     * @param string $color
     *
     * @return string
     */
    protected function renderSimpleBorder($left, $right, $innerWidth, $color = 'gray') {
        return $this->{$color}($left . str_repeat('─', $innerWidth) . $right);
    }

    /**
     * Render the search line content.
     *
     * @param CConsole_Prompt_DataTablePrompt $prompt
     * @param int                               $maxWidth
     *
     * @return string
     */
    protected function renderSearchLine($prompt, $maxWidth) {
        if ($prompt->state === 'search') {
            return $this->cyan('/') . ' ' . $prompt->searchWithCursor($maxWidth - 4);
        }

        if ($prompt->searchValue() !== '') {
            return $this->dim('/') . ' ' . $prompt->searchValue();
        }

        return $this->dim('/ Search');
    }

    /**
     * Render data rows with scrollbar support.
     *
     * @param CConsole_Prompt_DataTablePrompt          $prompt
     * @param array<int|string, array<int, string>> $filtered
     * @param array<int|string, array<int, string>> $visible
     * @param array<int, int>                        $widths
     * @param int                                     $numCols
     * @param int                                     $innerWidth
     * @param bool                                    $strikethrough
     *
     * @return array<int, string>
     */
    protected function renderDataRows($prompt, array $filtered, array $visible, array $widths, $numCols, $innerWidth, $strikethrough = false) {
        $total = count($filtered);

        // Build an empty row template for padding
        $emptyRow = implode($this->gray('│'), array_map(
            function ($w) {
                return str_repeat(' ', $w + 2);
            },
            $widths
        )) . '  ';

        $filteredKeys = array_keys($filtered);
        $highlightedKey = isset($filteredKeys[$prompt->highlighted]) ? $filteredKeys[$prompt->highlighted] : null;
        $isSearching = $prompt->state === 'search';
        $fixedHeight = $prompt->scroll;

        // Render all visible logical rows into visual lines, tracking which
        // logical row each visual line belongs to so we can clip intelligently.
        $taggedLines = [];

        foreach ($visible as $key => $row) {
            $isHighlighted = !$isSearching && !$strikethrough && $key === $highlightedKey;

            // Split each cell by newlines
            $cellLines = [];
            $maxSubRows = 1;

            foreach ($widths as $i => $w) {
                $text = isset($row[$i]) ? $row[$i] : '';
                $subLines = explode(PHP_EOL, $text);
                $cellLines[$i] = $subLines;
                $maxSubRows = max($maxSubRows, count($subLines));
            }

            // Render each sub-row
            for ($subRow = 0; $subRow < $maxSubRows; $subRow++) {
                $cells = [];

                foreach ($widths as $i => $w) {
                    $text = isset($cellLines[$i][$subRow]) ? $cellLines[$i][$subRow] : '';
                    $content = ' ' . $this->pad($this->truncate($text, $w), $w) . ' ';

                    if ($strikethrough) {
                        $content = ' ' . $this->pad($this->dim($this->strikethrough($this->truncate($text, $w))), $w) . ' ';
                    } elseif ($isHighlighted) {
                        $content = $this->inverse($content);
                    } elseif ($isSearching) {
                        $content = $this->dim($content);
                    }

                    $cells[] = $content;
                }

                $separator = $isHighlighted ? $this->inverse('│') : $this->gray('│');
                $taggedLines[] = [
                    'line' => implode($separator, $cells) . '  ',
                    'highlighted' => $isHighlighted,
                ];
            }
        }

        // Fixed visual height: always exactly `scroll` lines.
        // The highlighted row must be fully visible. If multiline rows cause
        // overflow, clip partial rows at the top or bottom edge.
        $totalVisual = count($taggedLines);

        if ($totalVisual <= $fixedHeight) {
            $dataLines = array_column($taggedLines, 'line');
        } else {
            // Find the highlighted row's visual line range
            $hlStart = null;
            $hlEnd = null;

            foreach ($taggedLines as $i => $tagged) {
                if ($tagged['highlighted']) {
                    if ($hlStart === null) {
                        $hlStart = $i;
                    }
                    $hlEnd = $i;
                }
            }

            // Pick a window of fixedHeight lines that includes the full highlighted row.
            // Prefer keeping the highlighted row near the bottom (natural scroll feel).
            if ($hlStart !== null) {
                $startLine = max(0, $hlEnd - $fixedHeight + 1);
                $startLine = min($startLine, $hlStart);
            } else {
                $startLine = 0;
            }

            $startLine = min($startLine, $totalVisual - $fixedHeight);
            $startLine = max(0, $startLine);

            $dataLines = array_column(array_slice($taggedLines, $startLine, $fixedHeight), 'line');
        }

        while (count($dataLines) < $fixedHeight) {
            $dataLines[] = $emptyRow;
        }

        // Apply scrollbar to data lines.
        // We can't use the trait's scrollbar() directly because it compares visual
        // line count against logical row count — multiline rows inflate visual lines
        // beyond $total, causing the scrollbar to disappear. Instead, determine
        // scrollability from logical counts and map the indicator to visual space.
        $shouldScroll = $total > $prompt->scroll;

        if ($shouldScroll) {
            $numVisual = count($dataLines);
            $maxFirst = $total - $prompt->scroll;

            if ($prompt->firstVisible === 0) {
                $visualPos = 0;
            } elseif ($prompt->firstVisible >= $maxFirst) {
                $visualPos = $numVisual - 1;
            } elseif ($numVisual <= 2) {
                $visualPos = -1;
            } else {
                $percent = $prompt->firstVisible / $maxFirst;
                $visualPos = (int) round($percent * ($numVisual - 3)) + 1;
            }

            $dataLines = array_map(function ($line, $index) use ($visualPos, $innerWidth) {
                if ($index === $visualPos) {
                    $replaced = preg_replace('/.$/', $this->cyan('┃'), $this->pad($line, $innerWidth));
                } else {
                    $replaced = preg_replace('/.$/', $this->gray('│'), $this->pad($line, $innerWidth));
                }

                return $replaced === null ? '' : $replaced;
            }, array_values($dataLines), range(0, $numVisual - 1));
        }

        return $dataLines;
    }

    /**
     * Compute column widths that fit within maxWidth.
     *
     * Columns get their natural (P85) width. Only shrink proportionally
     * if the total exceeds available terminal space.
     *
     * @param array<int, array<int, string>|string> $headers
     * @param array<int|string, array<int, string>> $allRows
     * @param int                                     $numCols
     * @param int                                     $maxWidth
     *
     * @return array<int, int>
     */
    protected function computeColumnWidths(array $headers, array $allRows, $numCols, $maxWidth) {
        // Header widths serve as the floor for each column
        $headerWidths = array_fill(0, $numCols, 0);

        foreach ($headers as $i => $header) {
            $headerText = is_array($header) ? implode(' ', $header) : $header;
            $headerWidths[$i] = mb_strwidth($headerText);
        }

        // Collect all cell widths per column (excluding blank cells)
        $columnWidths = array_fill(0, $numCols, []);

        foreach ($allRows as $row) {
            foreach ($row as $i => $cell) {
                $cellMax = 0;
                foreach (explode(PHP_EOL, $cell) as $line) {
                    $cellMax = max($cellMax, mb_strwidth($line));
                }
                if ($cellMax > 0) {
                    $columnWidths[$i][] = $cellMax;
                }
            }
        }

        // Per-column width strategy:
        // - Uniform columns (max <= P90 * 2): use max — all values are reasonable
        // - Outlier columns (max > P90 * 2): use P90 — ignore extreme values
        $natural = array_fill(0, $numCols, 0);

        foreach ($columnWidths as $i => $widths) {
            if (empty($widths)) {
                $natural[$i] = $headerWidths[$i];

                continue;
            }

            sort($widths);
            $p90Index = (int) ceil(count($widths) * 0.90) - 1;
            $p90 = $widths[max(0, $p90Index)];
            $colMax = end($widths);

            $natural[$i] = max($headerWidths[$i], $colMax <= $p90 * 2 ? $colMax : $p90);
        }

        // Available width for cell content:
        // Each column has 1 space padding on each side = 2 per column
        // Columns separated by │ = numCols - 1 separators
        // Scrollbar area = 2 chars on the right
        // Outer frame = 4 chars (` │` left + ` │` right)
        $overhead = ($numCols * 2) + ($numCols - 1) + 2 + 4;
        $available = $maxWidth - $overhead;

        if ($available <= 0) {
            return array_fill(0, $numCols, 1);
        }

        $totalNatural = array_sum($natural);

        // If natural widths fit, use them directly (comfortable width)
        if ($totalNatural <= $available) {
            return $natural;
        }

        // Otherwise, shrink proportionally
        $widths = array_fill(0, $numCols, 0);

        foreach ($natural as $i => $w) {
            $widths[$i] = max($headerWidths[$i], (int) floor($available * $w / $totalNatural));
        }

        // Distribute any remaining pixels from rounding
        $remainder = $available - array_sum($widths);

        if ($remainder > 0) {
            $order = range(0, $numCols - 1);
            usort($order, function ($a, $b) use ($natural) {
                return $natural[$b] - $natural[$a];
            });

            foreach ($order as $i) {
                if ($remainder <= 0) {
                    break;
                }
                $widths[$i]++;
                $remainder--;
            }
        }

        return $widths;
    }

    /**
     * The number of lines to reserve outside of the scrollable area.
     *
     * @return int
     */
    public function reservedLines() {
        return 10;
    }
}

<?php

/**
 * Description of ExceptionCommand.
 */
class CDevSuite_Command_DevCloud_Collector_ExceptionCommand extends CDevSuite_CommandAbstract {
    /**
     * Get the signature arguments string for the command.
     *
     * @return string
     */
    public function getSignatureArguments() {
        return '{id} {--frames=25 : How many stacktrace frames to print} {--full : Print every frame and the raw detail}';
    }

    /**
     * Show one collected DevCloud exception - message, location, stacktrace
     * and the latest detail row - without opening the browser.
     *
     * @param CConsole_Command $cfCommand
     *
     * @return int|void
     */
    public function run(CConsole_Command $cfCommand) {
        $id = $cfCommand->argument('id');
        $full = (bool) $cfCommand->option('full');
        $frameLimit = $full ? 0 : (int) $cfCommand->option('frames');

        try {
            $data = CDevSuite::devCloudApi()->request('collector/getException', ['id' => $id]);
        } catch (Exception $e) {
            CDevSuite::error($e->getMessage());

            return CConsole::FAILURE_EXIT;
        }

        if (empty($data)) {
            CDevSuite::error('Exception not found: ' . $id);

            return CConsole::FAILURE_EXIT;
        }

        $cfCommand->line('<info>Exception</info>');
        $cfCommand->table(['Field', 'Value'], [
            ['ID', carr::get($data, 'id')],
            ['App', carr::get($data, 'appCode')],
            ['Environment', carr::get($data, 'environment')],
            ['Error', carr::get($data, 'error')],
            ['Message', carr::get($data, 'message')],
            ['File', carr::get($data, 'file') . ':' . carr::get($data, 'line')],
            ['Occurred', carr::get($data, 'occurred')],
            ['Trigger At', carr::get($data, 'triggerAt')],
            ['Controller', carr::get($data, 'controller') . '::' . carr::get($data, 'method')],
            ['Full URL', carr::get($data, 'fullUrl')],
            ['Referer', carr::get($data, 'httpReferer')],
            ['User', carr::get($data, 'user')],
            ['CF Version', carr::get($data, 'cfVersion')],
        ]);

        $this->printFrames($cfCommand, carr::get($data, 'frames', []), $frameLimit);
        $this->printDetail($cfCommand, carr::get($data, 'detail'), $full);
    }

    /**
     * @param CConsole_Command $cfCommand
     * @param array            $frames
     * @param int              $limit    0 prints every frame
     *
     * @return void
     */
    protected function printFrames(CConsole_Command $cfCommand, $frames, $limit) {
        if (empty($frames)) {
            return;
        }

        $total = count($frames);
        $shown = $limit > 0 ? array_slice($frames, 0, $limit) : $frames;

        $cfCommand->line('');
        $cfCommand->line('<info>Stacktrace</info>');

        foreach ($shown as $i => $frame) {
            //the collector stores its own frame shape - file/class/method/
            //line_number - not debug_backtrace()'s file/class/type/function
            $class = (string) carr::get($frame, 'class');
            $method = (string) carr::get($frame, 'method');
            $call = $class === '' ? $method : $class . '::' . $method;

            $cfCommand->line(
                '#' . $i . ' ' . carr::get($frame, 'file') . ':' . carr::get($frame, 'line_number')
                . ($call === '' ? '' : '  ' . $call . '()')
            );
        }

        //never let a truncated stacktrace read as a complete one
        if (count($shown) < $total) {
            $cfCommand->line('... ' . ($total - count($shown)) . ' more frame(s), use --full or --frames=N');
        }
    }

    /**
     * @param CConsole_Command $cfCommand
     * @param null|array       $detail
     * @param bool             $full
     *
     * @return void
     */
    protected function printDetail(CConsole_Command $cfCommand, $detail, $full) {
        if (empty($detail)) {
            return;
        }

        $cfCommand->line('');
        $cfCommand->line('<info>Detail</info>');
        $cfCommand->table(['Field', 'Value'], [
            ['Detail ID', carr::get($detail, 'id')],
            ['Trigger At', carr::get($detail, 'triggerAt')],
            ['User Agent', carr::get($detail, 'userAgent')],
        ]);

        $request = carr::get($detail, 'request');
        if (!empty($request)) {
            $cfCommand->line('Request: ' . $this->encode($request, $full));
        }

        $postData = carr::get($detail, 'postData');
        if (!empty($postData)) {
            $cfCommand->line('Post   : ' . $this->truncate((string) $postData, $full));
        }

        $debug = carr::get($detail, 'debug', []);
        if (!empty($debug)) {
            $cfCommand->line('Debug  : ' . $this->encode($debug, $full));
        }
    }

    /**
     * @param mixed $value
     * @param bool  $full
     *
     * @return string
     */
    protected function encode($value, $full) {
        return $this->truncate((string) json_encode($value), $full);
    }

    /**
     * @param string $value
     * @param bool   $full
     *
     * @return string
     */
    protected function truncate($value, $full) {
        if ($full || strlen($value) <= 2000) {
            return $value;
        }

        return substr($value, 0, 2000) . ' ... (truncated, use --full)';
    }
}

<?php
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class CRunner_Teserract {
    /**
     * @var \Symfony\Component\HttpFoundation\File\File;
     */
    protected $image;

    public function scan($imagePath, $lang = null) {
        $executable = CF::config('runner.tesseract.executable', 'tesseract');
        $command = [];
        $command[] = $executable;
        if ($lang !== null) {
            $command[] = '-l';
            $command[] = $lang;
        }
        $command[] = $imagePath;
        $command[] = 'stdout';
        $command[] = 'quiet';
        $process = new Process($command);
        $process->run();

        // Cek apakah proses berjalan sukses
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Ambil output stdout
        $output = $process->getOutput();

        return $output;

        return shell_exec($command);
    }
}

<?php

/**
 * `CComparator_Differ_OutputBuilder` tidak pernah ada di repo ini — nama itu
 * sisa dari sebutan hulu (`SebastianBergmann\Diff\Output\DiffOutputBuilder`)
 * yang tidak ikut disulih saat kelasnya diporting. Akibatnya kelas ini fatal
 * begitu dimuat, dan bersamanya `CComparator_Differ_Output_UnifiedDiffOutput`
 * — satu-satunya keluaran diff terpadu yang tersedia — tidak pernah dapat
 * dipakai sama sekali.
 *
 * Antarmuka yang benar `CComparator_Differ_OutputInterface`, yang memang sudah
 * dipakai dua kelas keluaran lainnya.
 */
abstract class CComparator_Differ_AbstractOutput implements CComparator_Differ_OutputInterface {
    /**
     * Takes input of the diff array and returns the common parts.
     * Iterates through diff line by line.
     *
     * @param array $diff
     * @param int   $lineThreshold
     *
     * @return array
     */
    protected function getCommonChunks(array $diff, $lineThreshold = 5) {
        $diffSize = \count($diff);
        $capturing = false;
        $chunkStart = 0;
        $chunkSize = 0;
        $commonChunks = [];
        for ($i = 0; $i < $diffSize; ++$i) {
            if ($diff[$i][1] === 0 /* OLD */) {
                if ($capturing === false) {
                    $capturing = true;
                    $chunkStart = $i;
                    $chunkSize = 0;
                } else {
                    ++$chunkSize;
                }
            } elseif ($capturing !== false) {
                if ($chunkSize >= $lineThreshold) {
                    $commonChunks[$chunkStart] = $chunkStart + $chunkSize;
                }
                $capturing = false;
            }
        }
        if ($capturing !== false && $chunkSize >= $lineThreshold) {
            $commonChunks[$chunkStart] = $chunkStart + $chunkSize;
        }
        return $commonChunks;
    }
}

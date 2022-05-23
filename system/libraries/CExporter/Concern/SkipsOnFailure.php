<?php

interface CExporter_Concern_SkipsOnFailure {
    /**
     * @param CExporter_Validator_Failure[] $failures
     */
    public function onFailure(CExporter_Validator_Failure ...$failures);
}

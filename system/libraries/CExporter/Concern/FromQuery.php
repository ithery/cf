<?php

interface CExporter_Concern_FromQuery {
    /**
     * @return CDatabase_Query_Builder
     */
    public function query();
}

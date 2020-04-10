<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CExporter_ExportableDetector {
    
    
    public static function toExportable($data) {
        if(is_array($data)) {
            return new CExporter_Exportable_Array($data);
        } 
        if($data instanceof CCollection) {
            return new CExporter_Exportable_Collection($data);
        } 
        if($data instanceof Iterator) {
            return new CExporter_Exportable_Iterator($data);
        } 
        
        
    }
}

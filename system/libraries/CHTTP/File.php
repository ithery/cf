<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

class CHTTP_File extends SymfonyFile {

    use CHTTP_Trait_FileHelpersTrait;
}

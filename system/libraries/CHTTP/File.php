<?php


use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

class CHTTP_File extends SymfonyFile {
    use CHTTP_Trait_FileHelpersTrait;
}

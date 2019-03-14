<?php

class CModel_LogActivity_Exception_CouldNotLogActivityException extends CException
{
    public static function couldNotDetermineUser($id)
    {
        return new static("Could not determine a user with identifier `{$id}`.");
    }
}

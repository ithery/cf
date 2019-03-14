<?php

class CModel_LogActivity_Exception_CouldNotLogChangesException extends CException
{
    public static function invalidAttribute($attribute)
    {
        return new static("Cannot log attribute `{$attribute}`. Can only log attributes of a model or a directly related model.");
    }
}

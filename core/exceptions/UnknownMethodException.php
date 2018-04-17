<?php

namespace core\exceptions;

class UnknownMethodException extends  \BadMethodCallException
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Unknown Method';
    }
}
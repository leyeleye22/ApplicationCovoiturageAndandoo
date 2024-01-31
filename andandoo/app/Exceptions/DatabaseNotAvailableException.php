<?php

namespace App\Exceptions;

use Exception;

class DatabaseNotAvailableException extends Exception
{
    /**
     * Crée une nouvelle instance de l'exception.
     *
     * @param  string  $message
     * @param  int  $code
     * @param  Exception|null  $previous
     * @return void
     */
    public function __construct($message = 'La base de données est indisponible.', $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

<?php

namespace App\Exceptions;

use Exception;

class RegistrationException extends Exception
{
    public function __construct($message = 'Échec de l\'inscription de l\'utilisateur.',
    $code = 500, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

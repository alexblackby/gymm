<?php

namespace AppBundle\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ObjectNotFoundException extends NotFoundHttpException
{
    public function __construct($message = 'Object not found')
    {
        parent::__construct($message);
    }
}
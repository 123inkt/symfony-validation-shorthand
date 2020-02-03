<?php

namespace PrinsFrank\SymfonyRequestValidation\Response;

use Symfony\Component\HttpKernel\Exception\HttpException;

class UnauthorizedRequestResponse
{
    public function __construct()
    {
        throw new HttpException(401, 'unauthorized');
    }
}
<?php

declare(strict_types=1);

namespace PrinsFrank\SymfonyRequestValidation\Response;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\ConstraintViolationList;

class InvalidRequestResponse
{
    public function __construct(ConstraintViolationList $constraintViolationList)
    {
        $message = [];

        foreach ($constraintViolationList->getIterator() as $violation) {
            $message[] = $violation->getPropertyPath() . $violation->getMessage();
        }

        throw new UnprocessableEntityHttpException(implode(',', $message));
    }
}

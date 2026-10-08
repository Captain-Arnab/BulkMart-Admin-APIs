<?php

/**
 * Customer order edit refused by the edit rule (Order::editState); carries the API error code.
 */
class OrderEditDeniedException extends DomainException
{
    public function __construct(public readonly string $errorCode, string $message)
    {
        parent::__construct($message);
    }
}

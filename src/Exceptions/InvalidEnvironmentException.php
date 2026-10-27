<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel\Exceptions;

use InvalidArgumentException;
use Routegroup\IngPay\Types\Environment;

class InvalidEnvironmentException extends InvalidArgumentException
{
    public function __construct(public readonly string $given)
    {
        parent::__construct(sprintf(
            'Invalid [services.ing_pay.env] value [%s]. Allowed values: %s.',
            $given,
            implode(', ', array_column(Environment::cases(), 'value')),
        ));
    }

    /** @return array{given: string, allowed: list<string>} */
    public function context(): array
    {
        return [
            'given' => $this->given,
            'allowed' => array_column(Environment::cases(), 'value'),
        ];
    }
}

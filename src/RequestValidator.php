<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel;

use Illuminate\Http\Request;
use Routegroup\IngPay\Exceptions\InvalidSignatureException;
use Routegroup\IngPay\Exceptions\SchemaValidationException;
use Routegroup\IngPay\Lib\Validator;

/**
 * Pulls the two things the core needs out of a framework request: the body
 * exactly as it arrived, and the signature header. Nothing else about Laravel
 * reaches the verification.
 */
class RequestValidator
{
    public const SIGNATURE_HEADER = 'x-imoje-signature';

    public function __construct(private readonly Validator $validator) {}

    /** @throws InvalidSignatureException */
    public function verifySignature(Request $request): void
    {
        $this->validator->verifySignature(
            $request->getContent(),
            (string) $request->headers->get(self::SIGNATURE_HEADER, ''),
        );
    }

    /**
     * @throws SchemaValidationException
     */
    public function fromNotification(Request $request): bool
    {
        return $this->validator->fromNotification($request->json()->all());
    }
}

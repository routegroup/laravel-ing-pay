<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Laravel;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Routegroup\IngPay\Lib\Config;
use Routegroup\IngPay\Lib\Utils;
use Routegroup\IngPay\Paywall\CreateTransactionRequest;
use Routegroup\IngPay\Types\HashMethod;
use Routegroup\IngPay\Types\Lang;
use Routegroup\IngPay\Value\Cart;
use Routegroup\IngPay\Value\Invoice;

/**
 * The paywall is a signed form the payer's browser submits, not an endpoint, so
 * this builds a url rather than sending anything.
 */
class Paywall
{
    public function __construct(
        private readonly Config $config,
        private readonly Utils $utils,
    ) {}

    public function createTransaction(
        CreateTransactionRequest $request,
        ?Lang $lang = null,
        HashMethod $hashMethod = HashMethod::SHA256,
    ): RedirectResponse {
        return redirect($this->url($request, $lang, $hashMethod));
    }

    /**
     * Separate from the redirect so an integration can render the url itself —
     * in a form, a QR code, an e-mail — and so this is testable without one.
     */
    public function url(
        CreateTransactionRequest $request,
        ?Lang $lang = null,
        HashMethod $hashMethod = HashMethod::SHA256,
    ): string {
        $fields = $request->withIdentity($this->config)->toArray();

        // The paywall takes an invoice and a cart encoded, not expanded: that is
        // a property of this channel, so the request keeps holding real values.
        if ($request->invoice instanceof Invoice) {
            $fields['invoice'] = $request->invoice->encoded();
        }

        if ($request->cart instanceof Cart) {
            $fields['cart'] = $request->cart->encoded();
        }

        unset($fields['signature']);

        $fields['signature'] = $this->utils->createSignature($fields, $hashMethod);

        return $this->config->env->paywallUrl($lang).'?'.Arr::query($fields);
    }
}

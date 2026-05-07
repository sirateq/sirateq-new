<?php

namespace App\Http\Controllers\Shop;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SignedShopOrderController
{
    /**
     * Validates a temporary signed URL, grants storefront session access, then shows the order.
     */
    public function grantAndRedirectToOrder(Request $request, Order $order): RedirectResponse
    {
        if (! $request->hasValidSignature()) {
            abort(403);
        }

        Order::grantCustomerSessionAccess($order);

        return redirect()->route('shop.orders.show', $order);
    }

    /**
     * Validates a temporary signed URL, grants storefront session access, then downloads the invoice PDF.
     */
    public function grantAndRedirectToInvoice(Request $request, Order $order): RedirectResponse
    {
        if (! $request->hasValidSignature()) {
            abort(403);
        }

        Order::grantCustomerSessionAccess($order);

        return redirect()->route('shop.orders.invoice', $order);
    }
}

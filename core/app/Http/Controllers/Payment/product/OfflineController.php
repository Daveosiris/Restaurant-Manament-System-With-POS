<?php

namespace App\Http\Controllers\Payment\product;

use App\Events\OrderPlaced;
use Illuminate\Http\Request;
use App\Http\Controllers\Payment\product\PaymentController;
use App\Models\BasicSetting;
use App\Models\PostalCode;
use App\Models\ShippingCharge;
use Session;
use Str;


class OfflineController extends PaymentController
{

    public function store(Request $request)
    {
        if($this->orderValidation($request)) {
            return $this->orderValidation($request);
        }

        $bs = BasicSetting::select('postal_code')->firstOrFail();

        if ($request->serving_method == 'home_delivery') {
            if ($bs->postal_code == 0) {
                if ($request->has('shipping_charge')) {
                    $shipping = ShippingCharge::findOrFail($request->shipping_charge);
                    $shippig_charge = $shipping->charge;
                } else {
                    $shipping = NULL;
                    $shippig_charge = 0;
                }
            } else {
                $shipping = PostalCode::findOrFail($request->postal_code);
                $shippig_charge = $shipping->charge;
            }
            if (!empty($shipping) && !empty($shipping->free_delivery_amount) && cartTotal() >= $shipping->free_delivery_amount) {
                $shippig_charge = 0;
            } else {
                $shippig_charge = $shippig_charge;
            }
        } else {
            $shipping = NULL;
            $shippig_charge = 0;
        }
        $total = $this->orderTotal($shippig_charge);

        // save order
        $txnId = 'txn_' . Str::random(8) . time();
        $chargeId = 'ch_' . Str::random(9) . time();
        $order = $this->saveOrder($request, $shipping, $total, $txnId, $chargeId, 'offline');
        $order_id = $order->id;

        // save ordered items
        $this->saveOrderItem($order_id);

        $this->sendNotifications($order);

        Session::forget('coupon');
        Session::forget('cart');

        if ($request->ordered_from == 'website') {
            $success_url = route('product.payment.return', $order->order_number);
        } elseif ($request->ordered_from == 'qr') {
            $success_url = route('qr.payment.return', $order->order_number);
        }
        return redirect($success_url);

    }
}

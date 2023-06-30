<?php

namespace App\Notifications;

use App\Channels\Messages\WhatsAppMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Channels\WhatsAppChannel;
use App\Models\BasicExtended;
use App\Models\ProductOrder;

class WAStatusNotification extends Notification
{
    use Queueable;

    public $order;

    public function __construct(ProductOrder $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return [WhatsAppChannel::class];
    }

    public function toWhatsApp($notifiable)
    {
        $order = $this->order;
        $orderNum = $order->order_number;
        $status = $order->order_status;
        $servingMethod = $order->serving_method;

        $message = "";

        if ($status == 'received') {
            $message .= "Your order has been *received*.\n";
            $message .= "*Order Number:* #" . $orderNum . "\n\n";
            if (!empty($order->user_id)) {
                $message .= "Please click on the below link to see your order details.\n";
                $message .= route('user-orders-details', $order->id);
            }
        } elseif ($status == 'preparing') {
            $message .= "Chef has started *preparing* your ordered foods.\n";
            $message .= "*Order Number:* #" . $orderNum . "\n\n";
            if (!empty($order->user_id)) {
                $message .= "Please click on the below link to see your order details.\n";
                $message .= route('user-orders-details', $order->id);
            }
        } elseif ($status == 'ready_to_pick_up') {
            $message .= "Your order is ready to pickup.\n";
            $message .= "*Order Number:* #" . $orderNum . "\n\n";
            if (!empty($order->user_id)) {
                $message .= "Please click on the below link to see your order details.\n";
                $message .= route('user-orders-details', $order->id);
            }
        } elseif ($status == 'picked_up' && $servingMethod == 'home_delivery') {
            $message .= "Your order is picked up for delivery.\n";
            $message .= "*Order Number:* #" . $orderNum . "\n\n";
            if (!empty($order->user_id)) {
                $message .= "Please click on the below link to see your order details.\n";
                $message .= route('user-orders-details', $order->id);
            }
        } elseif ($status == 'picked_up' && $servingMethod == 'pick_up') {
            $message .= "Your have picked up your ordered Food.\n";
            $message .= "*Order Number:* #" . $orderNum . "\n\n";
            if (!empty($order->user_id)) {
                $message .= "Please click on the below link to see your order details.\n";
                $message .= route('user-orders-details', $order->id);
            }
        } elseif ($status == 'delivered') {
            $message .= "Your order has been delivered.\n";
            $message .= "*Order Number:* #" . $orderNum . "\n\n";
            if (!empty($order->user_id)) {
                $message .= "Please click on the below link to see your order details.\n";
                $message .= route('user-orders-details', $order->id);
            }
        } elseif ($status == 'cancelled') {
            $message .= "Your order has been cancelled.\n";
            $message .= "*Order Number:* #" . $orderNum . "\n\n";
            if (!empty($order->user_id)) {
                $message .= "Please click on the below link to see your order details.\n";
                $message .= route('user-orders-details', $order->id);
            }
        } elseif ($status == 'served') {
            $message .= "Your order is served on your table. \n";
            $message .= "*Order Number:* #" . $orderNum . "\n\n";
            if (!empty($order->user_id)) {
                $message .= "Please click on the below link to see your order details.\n";
                $message .= route('user-orders-details', $order->id);
            }
        } elseif ($status == 'ready_to_serve') {
            $message .= "Your order is ready to serve on table. \n";
            $message .= "*Order Number:* #" . $orderNum . "\n\n";
            if (!empty($order->user_id)) {
                $message .= "Please click on the below link to see your order details.\n";
                $message .= route('user-orders-details', $order->id);
            }
        }

        return (new WhatsAppMessage)->content($message);
    }

    public function setCurrPos($amount, $be)
    {
        return ($be->base_currency_symbol_position == 'left' ? $be->base_currency_symbol : '') . $amount . ($be->base_currency_symbol_position == 'right' ? $be->base_currency_symbol : '');
    }
}

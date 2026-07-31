<?php

namespace App\Services;

use App\Mail\StoreEventMail;
use App\Models\Inquiry;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class StoreMailService
{
    public function inquiryCreated(Inquiry $inquiry): void
    {
        $this->sendToAdmin(
            'New Contact Query from '.$inquiry->name,
            'emails.admin.inquiry-created',
            ['inquiry' => $inquiry]
        );
    }

    public function userLoggedIn(User $user, string $loginType, ?Request $request = null): void
    {
        $this->sendToAdmin(
            'User login: '.$user->name,
            'emails.admin.user-login',
            [
                'user' => $user,
                'loginType' => $loginType,
                'ip' => $request?->ip(),
                'userAgent' => $request?->userAgent(),
                'loggedInAt' => now(),
            ]
        );
    }

    public function orderSuccess(Order $order): void
    {
        $order->loadMissing('items');

        $this->sendToCustomer(
            $order,
            'Order Confirmed: '.$order->order_number,
            'emails.customer.order-success',
            ['order' => $order]
        );

        $this->sendToAdmin(
            'New Order: '.$order->order_number,
            'emails.admin.order-success',
            ['order' => $order]
        );
    }

    public function orderPaymentFailed(Order $order, ?string $reason = null): void
    {
        $order->loadMissing('items');

        $this->sendToCustomer(
            $order,
            'Payment Failed: '.$order->order_number,
            'emails.customer.payment-failed',
            ['order' => $order, 'reason' => $reason]
        );

        $this->sendToAdmin(
            'Payment Failed: '.$order->order_number,
            'emails.admin.order-failed',
            ['order' => $order, 'reason' => $reason]
        );
    }

    private function sendToCustomer(Order $order, string $subject, string $view, array $data): void
    {
        $email = $order->customer_email ?: $order->user?->email;

        if (! $email) {
            return;
        }

        $this->send([$email], $subject, $view, $data);
    }

    private function sendToAdmin(string $subject, string $view, array $data): void
    {
        $this->send($this->adminRecipients(), $subject, $view, $data);
    }

    private function send(array $recipients, string $subject, string $view, array $data): void
    {
        $recipients = array_values(array_filter(array_unique($recipients)));

        if ($recipients === []) {
            return;
        }

        try {
            Mail::to($recipients)->send(new StoreEventMail($subject, $view, $data));
        } catch (\Throwable $e) {
            Log::error('Store mail failed', [
                'subject' => $subject,
                'view' => $view,
                'recipients' => $recipients,
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function adminRecipients(): array
    {
        $configured = env('ADMIN_MAIL_TO');

        if ($configured) {
            return collect(explode(',', $configured))
                ->map(fn (string $email) => trim($email))
                ->filter()
                ->all();
        }

        return array_filter([
            config('mail.from.address'),
        ]);
    }

    public static function addressLine(Order $order): string
    {
        $parts = array_filter([
            $order->shipping_address_line_1,
            $order->shipping_address_line_2,
            $order->shipping_city,
            $order->shipping_state,
            $order->shipping_country,
        ]);

        return implode(', ', $parts).' - '.$order->shipping_postal_code;
    }

    public static function itemOptions(array $meta = []): string
    {
        $rows = $meta['product_attribute_values'] ?? [];

        if (! is_array($rows) || $rows === []) {
            return '-';
        }

        return collect($rows)
            ->map(function (array $row) {
                $label = $row['attribute_name'] ?? 'Option';
                $value = $row['attribute_value_label'] ?? $row['value'] ?? '-';

                return Str::headline($label).': '.$value;
            })
            ->implode(', ');
    }
}

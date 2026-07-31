<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query()
            ->with('user')
            ->withCount('items')
            ->latest();

        $this->applyOrderFilters($query, $request);

        $orders = $query->paginate(15)->withQueryString();

        $stats = [
            'orders' => Order::count(),
            'revenue' => (float) Order::where('payment_status', 'paid')->sum('grand_total'),
            'pending' => Order::whereIn('status', ['pending', 'processing'])->count(),
            'failed' => Order::where('payment_status', 'failed')->count(),
        ];

        return view('admin.pages.sales.index', compact('orders', 'stats'));
    }

    public function show(Order $order)
    {
        $order->load(['items', 'user.addresses', 'coupon']);

        return view('admin.pages.sales.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,processing,shipped,delivered,cancelled,payment_failed'],
            'payment_status' => ['required', 'string', 'in:pending,paid,failed,refunded'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $order->update($validated);

        return back()->with('success', 'Order updated successfully.');
    }

    public function customers(Request $request)
    {
        $query = User::query()
            ->where('role', 'user')
            ->withCount('orders')
            ->withSum('orders as total_order_value', 'grand_total')
            ->latest();

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate(15)->withQueryString();

        return view('admin.pages.sales.customers', compact('customers'));
    }

    public function customer(User $user)
    {
        $user->load([
            'addresses',
            'orders' => fn ($query) => $query->withCount('items')->latest(),
            'wishlists.product',
            'carts.items',
        ]);

        $summary = [
            'orders' => $user->orders->count(),
            'paid' => $user->orders->where('payment_status', 'paid')->count(),
            'spent' => (float) $user->orders->where('payment_status', 'paid')->sum('grand_total'),
            'wishlist' => $user->wishlists->count(),
            'cart_items' => $user->activeCart?->items?->sum('quantity') ?? 0,
        ];

        return view('admin.pages.sales.customer-show', compact('user', 'summary'));
    }

    public function items(Request $request)
    {
        $items = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->select([
                'order_items.*',
                'orders.order_number',
                'orders.customer_name',
                'orders.payment_status',
                'orders.status',
                'orders.created_at as ordered_at',
            ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('order_items.product_name', 'like', "%{$search}%")
                        ->orWhere('order_items.product_sku', 'like', "%{$search}%")
                        ->orWhere('orders.order_number', 'like', "%{$search}%")
                        ->orWhere('orders.customer_name', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('orders.created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.pages.sales.items', compact('items'));
    }

    private function applyOrderFilters($query, Request $request): void
    {
        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->string('payment_status')->toString());
        }
    }
}

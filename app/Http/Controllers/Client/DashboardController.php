<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $user->load([
            'activeCart.items.product',
            'wishlists.product',
            'reviews.product',
            'addresses' => fn ($query) => $query->latest('is_default')->latest(),
            'orders.items' => fn ($query) => $query->latest(),
        ]);

        return view('client.dashboard.home', [
            'user' => $user,
            'cart' => $user->activeCart,
            'wishlistItems' => $user->wishlists()->with('product')->latest()->take(4)->get(),
            'recentReviews' => $user->reviews()->with('product')->latest()->take(3)->get(),
            'recentOrders' => $user->orders()->with('items')->latest()->take(5)->get(),
        ]);
    }

    public function profile()
    {
        return view('client.dashboard.profile', [
            'user' => auth()->user()->load(['addresses' => fn ($query) => $query->latest('is_default')->latest()]),
        ]);
    }

    public function editProfile()
    {
        return view('client.dashboard.edit-profile', [
            'user' => auth()->user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if (! empty($validated['phone'])) {
            if (User::where('id', '!=', $user->id)->whereIn('phone', $this->phoneLookupValues($validated['phone']))->exists()) {
                return back()->withInput()->withErrors(['phone' => 'This mobile number is already linked to another account.']);
            }

            $validated['phone'] = $this->formatMobile($validated['phone']);

            if ($validated['phone'] !== $user->phone) {
                $validated['phone_verified_at'] = null;
            }
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                unlink(public_path($user->avatar));
            }

            $avatar = $request->file('avatar');
            $avatarName = time() . '_avatar_' . $avatar->getClientOriginalName();
            $avatar->move(public_path('users/avatars'), $avatarName);
            $validated['avatar'] = 'users/avatars/' . $avatarName;
        }

        if ($validated['email'] !== $user->email) {
            $validated['email_verified_at'] = null;
        }

        $user->update($validated);

        return redirect()
            ->route('dashboard.profile')
            ->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Current password is incorrect.'], 'updatePassword')
                ->withInput();
        }

        $user->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Password updated successfully.');
    }

    public function storeAddress(Request $request)
    {
        $validated = $this->validateAddress($request);
        $validated['is_default'] = ! empty($validated['is_default']) || ! $request->user()->addresses()->exists();

        if ($validated['is_default']) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $request->user()->addresses()->create($validated);

        return redirect()
            ->route('dashboard.profile')
            ->with('success', 'Address added successfully.');
    }

    public function updateAddress(Request $request, UserAddress $address)
    {
        abort_unless($address->user_id === $request->user()->id, 403);

        $validated = $this->validateAddress($request);

        if (! empty($validated['is_default'])) {
            $request->user()->addresses()
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update($validated);

        return redirect()
            ->route('dashboard.profile')
            ->with('success', 'Address updated successfully.');
    }

    private function validateAddress(Request $request): array
    {
        return $request->validate([
            'label' => ['required', 'string', 'max:80'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'landmark' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'state' => ['required', 'string', 'max:120'],
            'postal_code' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:120'],
            'is_default' => ['nullable', 'boolean'],
        ]);
    }

    private function formatMobile(string $phone): string
    {
        $phone = preg_replace('/\D+/', '', $phone);

        return str_starts_with($phone, '91') ? $phone : '91'.$phone;
    }

    private function phoneLookupValues(string $phone): array
    {
        $digits = preg_replace('/\D+/', '', $phone);
        $mobile = $this->formatMobile($digits);
        $national = str_starts_with($mobile, '91') ? substr($mobile, 2) : $mobile;

        return array_values(array_unique(array_filter([
            $phone,
            $digits,
            $mobile,
            '+'.$mobile,
            $national,
            '0'.$national,
        ])));
    }
}

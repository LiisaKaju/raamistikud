<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class ShopController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('shop/Index', [
            'products' => Product::latest()->take(50)->get(),
            'cartCount' => $this->cartCount(),
        ]);
    }

    public function cart(): Response
    {
        return Inertia::render('shop/Cart', [
            'items' => $this->cartItems(),
            'total' => $this->cartTotal(),
            'cartCount' => $this->cartCount(),
        ]);
    }

    public function addToCart(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $cart = session()->get('cart', []);
        $id = (string) $product->id;

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $validated['quantity'];
        } else {
            $cart[$id] = [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => (float) $product->price,
                'quantity' => $validated['quantity'],
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Toode lisatud ostukorvi.');
    }

    public function updateCart(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $cart = session()->get('cart', []);
        $id = (string) $product->id;

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $validated['quantity'];
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Kogus uuendatud.');
    }

    public function removeFromCart(Product $product): RedirectResponse
    {
        $cart = session()->get('cart', []);
        unset($cart[(string) $product->id]);
        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Toode eemaldatud ostukorvist.');
    }

    public function checkout(): Response|RedirectResponse
    {
        if ($this->cartCount() === 0) {
            return redirect()->route('shop.index')->with('error', 'Ostukorv on tühi.');
        }

        return Inertia::render('shop/Checkout', [
            'items' => $this->cartItems(),
            'total' => $this->cartTotal(),
        ]);
    }

    public function startPayment(Request $request): RedirectResponse|HttpResponse
    {
        if ($this->cartCount() === 0) {
            return redirect()->route('shop.index')->with('error', 'Ostukorv on tühi.');
        }

        if (! config('services.stripe.secret')) {
            return redirect()->route('shop.checkout')->with('error', 'Stripe seadistus puudub (.env: STRIPE_SECRET).');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'payment_method' => 'required|in:stripe',
        ]);

        session()->put('checkout_customer', $validated);

        Stripe::setApiKey((string) config('services.stripe.secret'));

        $lineItems = collect($this->cartItems())->map(function (array $item): array {
            $productData = [
                'name' => $item['name'],
            ];

            if (! empty($item['description'])) {
                $productData['description'] = $item['description'];
            }

            return [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => $productData,
                    'unit_amount' => (int) round($item['price'] * 100),
                ],
                'quantity' => $item['quantity'],
            ];
        })->values()->all();

        try {
            $session = Session::create([
                'mode' => 'payment',
                'line_items' => $lineItems,
                'success_url' => route('shop.result', [], true).'?status=success&session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('shop.result', [], true).'?status=failed',
                'customer_email' => $validated['email'],
                'metadata' => [
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'phone' => $validated['phone'],
                ],
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()->route('shop.checkout')->withErrors([
                'payment' => 'Stripe makse algatamine ebaõnnestus. Kontrolli võtmeid ja proovi uuesti.',
            ]);
        }

        // Inertia POST requests use XHR; external redirects must use Inertia::location
        // so the browser performs a full-page navigation to Stripe Checkout.
        return Inertia::location($session->url);
    }

    public function paymentResult(Request $request): Response
    {
        $status = $request->query('status');
        $sessionId = $request->query('session_id');

        if ($status !== 'success' || ! $sessionId) {
            return Inertia::render('shop/Result', [
                'status' => 'failed',
                'message' => 'Makse ebaõnnestus või katkestati. Tooted jäid ostukorvi.',
            ]);
        }

        Stripe::setApiKey((string) config('services.stripe.secret'));
        $checkoutSession = Session::retrieve((string) $sessionId);

        if ($checkoutSession->payment_status === 'paid') {
            $this->saveOrderAndClearCart((string) $sessionId, 'paid');

            return Inertia::render('shop/Result', [
                'status' => 'success',
                'message' => 'Makse õnnestus. Tellimus on salvestatud.',
            ]);
        }

        if ($checkoutSession->payment_status === 'unpaid') {
            return Inertia::render('shop/Result', [
                'status' => 'pending',
                'message' => 'Makse on ootel. Kontrolli hiljem uuesti.',
            ]);
        }

        return Inertia::render('shop/Result', [
            'status' => 'failed',
            'message' => 'Makse staatus teadmata. Tooted jäid ostukorvi.',
        ]);
    }

    private function saveOrderAndClearCart(string $paymentReference, string $paymentStatus): void
    {
        if (Order::where('payment_reference', $paymentReference)->exists()) {
            session()->forget(['cart', 'checkout_customer']);

            return;
        }

        $items = $this->cartItems();
        $customer = session()->get('checkout_customer', []);

        $order = Order::create([
            'first_name' => $customer['first_name'] ?? '',
            'last_name' => $customer['last_name'] ?? '',
            'email' => $customer['email'] ?? '',
            'phone' => $customer['phone'] ?? '',
            'total' => $this->cartTotal(),
            'status' => $paymentStatus,
            'payment_provider' => 'stripe',
            'payment_reference' => $paymentReference,
        ]);

        foreach ($items as $item) {
            $order->items()->create([
                'product_id' => $item['id'],
                'product_name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
            ]);
        }

        session()->forget(['cart', 'checkout_customer']);
    }

    private function cartItems(): array
    {
        return array_values(session()->get('cart', []));
    }

    private function cartTotal(): float
    {
        return collect($this->cartItems())->sum(fn (array $item) => $item['price'] * $item['quantity']);
    }

    private function cartCount(): int
    {
        return (int) collect($this->cartItems())->sum('quantity');
    }
}

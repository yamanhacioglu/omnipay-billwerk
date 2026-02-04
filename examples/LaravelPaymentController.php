<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Omnipay\Omnipay;

/**
 * Laravel Payment Controller Example
 *
 * This controller demonstrates how to use the Omnipay Billwerk Gateway in Laravel
 */
class PaymentController extends Controller
{
    /**
     * @var \Omnipay\Billwerk\Gateway
     */
    protected $gateway;

    /**
     * Initialize the gateway
     */
    public function __construct()
    {
        $this->gateway = Omnipay::create('Billwerk');
        $this->gateway->initialize([
            'apiKey' => config('billwerk.apiKey'),
            'testMode' => config('billwerk.testMode'),
        ]);
    }

    /**
     * Show checkout page
     */
    public function checkout()
    {
        return view('checkout');
    }

    /**
     * Process payment
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'email' => 'required|email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
        ]);

        try {
            $response = $this->gateway->purchase([
                'amount' => $request->amount,
                'currency' => 'EUR',
                'description' => 'Order #' . time(),
                'transactionId' => 'ORDER-' . uniqid(),
                'returnUrl' => route('payment.return'),
                'cancelUrl' => route('payment.cancel'),
                'card' => [
                    'email' => $request->email,
                    'firstName' => $request->first_name,
                    'lastName' => $request->last_name,
                    'phone' => $request->phone,
                    'address1' => $request->address,
                    'city' => $request->city,
                    'postcode' => $request->postcode,
                    'country' => $request->country,
                ]
            ])->send();

            if ($response->isRedirect()) {
                // Redirect to Billwerk checkout
                return redirect($response->getRedirectUrl());
            }

            return back()->with('error', 'Payment initialization failed');

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Handle return from payment
     */
    public function returnFromPayment(Request $request)
    {
        try {
            $response = $this->gateway->completePurchase([
                'transactionReference' => $request->invoice,
            ])->send();

            if ($response->isSuccessful()) {
                // Payment successful - save order, send confirmation email, etc.

                return view('payment.success', [
                    'transactionId' => $response->getTransactionReference(),
                    'amount' => $response->getAmount(),
                    'currency' => $response->getCurrency(),
                ]);
            }

            return view('payment.failed', [
                'message' => $response->getMessage()
            ]);

        } catch (\Exception $e) {
            return view('payment.failed', [
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle payment cancellation
     */
    public function cancelPayment()
    {
        return view('payment.cancelled');
    }

    /**
     * Create a customer
     */
    public function createCustomer(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
        ]);

        try {
            $response = $this->gateway->createCustomer([
                'customerReference' => 'CUST-' . $request->user()->id,
                'card' => [
                    'email' => $request->email,
                    'firstName' => $request->first_name,
                    'lastName' => $request->last_name,
                    'company' => $request->company,
                    'phone' => $request->phone,
                    'address1' => $request->address,
                    'city' => $request->city,
                    'postcode' => $request->postcode,
                    'country' => $request->country,
                ]
            ])->send();

            if ($response->isSuccessful()) {
                // Save customer reference to database
                $request->user()->update([
                    'billwerk_customer_id' => $response->getCustomerReference()
                ]);

                return redirect()->back()->with('success', 'Customer created successfully');
            }

            return back()->with('error', $response->getMessage());

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Add payment method
     */
    public function addPaymentMethod(Request $request)
    {
        $customerRef = $request->user()->billwerk_customer_id;

        if (!$customerRef) {
            return back()->with('error', 'Please create a customer profile first');
        }

        try {
            $response = $this->gateway->createCard([
                'customerReference' => $customerRef,
                'returnUrl' => route('card.return'),
                'cancelUrl' => route('card.cancel'),
            ])->send();

            if ($response->isRedirect()) {
                return redirect($response->getRedirectUrl());
            }

            return back()->with('error', 'Failed to initialize card creation');

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Create subscription
     */
    public function createSubscription(Request $request)
    {
        $request->validate([
            'plan' => 'required|string',
        ]);

        $customerRef = $request->user()->billwerk_customer_id;

        if (!$customerRef) {
            return back()->with('error', 'Please create a customer profile first');
        }

        try {
            $response = $this->gateway->createSubscription([
                'customerReference' => $customerRef,
                'plan' => $request->plan,
                'subscriptionReference' => 'SUB-' . $request->user()->id . '-' . time(),
                'cardReference' => $request->card_reference, // Optional
            ])->send();

            if ($response->isSuccessful()) {
                // Save subscription reference
                $request->user()->subscriptions()->create([
                    'billwerk_subscription_id' => $response->getSubscriptionReference(),
                    'plan' => $request->plan,
                    'status' => $response->getState(),
                ]);

                return redirect()->route('subscription.index')
                    ->with('success', 'Subscription created successfully');
            }

            return back()->with('error', $response->getMessage());

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription(Request $request, $subscriptionId)
    {
        $subscription = $request->user()
            ->subscriptions()
            ->findOrFail($subscriptionId);

        try {
            $response = $this->gateway->cancelSubscription([
                'subscriptionReference' => $subscription->billwerk_subscription_id,
            ])->send();

            if ($response->isSuccessful()) {
                $subscription->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                ]);

                return back()->with('success', 'Subscription cancelled successfully');
            }

            return back()->with('error', $response->getMessage());

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Process refund
     */
    public function refund(Request $request)
    {
        $request->validate([
            'transaction_reference' => 'required|string',
            'amount' => 'nullable|numeric|min:0.01',
            'reason' => 'nullable|string',
        ]);

        try {
            $response = $this->gateway->refund([
                'transactionReference' => $request->transaction_reference,
                'amount' => $request->amount, // Optional: partial refund
                'description' => $request->reason,
            ])->send();

            if ($response->isSuccessful()) {
                return back()->with('success', 'Refund processed successfully');
            }

            return back()->with('error', $response->getMessage());

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Handle webhooks from Billwerk
     */
    public function webhook(Request $request)
    {
        // Get webhook payload
        $payload = $request->getContent();
        $signature = $request->header('Reepay-Signature');

        // Verify webhook signature (implement your verification logic)
        // if (!$this->verifyWebhookSignature($payload, $signature)) {
        //     return response()->json(['error' => 'Invalid signature'], 401);
        // }

        $event = json_decode($payload, true);

        try {
            switch ($event['event_type']) {
                case 'invoice_settled':
                    $this->handleInvoiceSettled($event);
                    break;

                case 'invoice_authorized':
                    $this->handleInvoiceAuthorized($event);
                    break;

                case 'invoice_failed':
                    $this->handleInvoiceFailed($event);
                    break;

                case 'subscription_created':
                    $this->handleSubscriptionCreated($event);
                    break;

                case 'subscription_cancelled':
                    $this->handleSubscriptionCancelled($event);
                    break;

                case 'customer_created':
                    $this->handleCustomerCreated($event);
                    break;

                // Add more event types as needed
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            \Log::error('Billwerk webhook error: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Handle invoice settled event
     */
    protected function handleInvoiceSettled($event)
    {
        $invoice = $event['invoice'];

        // Update order status, send confirmation email, etc.
        \Log::info('Invoice settled: ' . $invoice);
    }

    /**
     * Handle invoice authorized event
     */
    protected function handleInvoiceAuthorized($event)
    {
        $invoice = $event['invoice'];

        // Handle authorization
        \Log::info('Invoice authorized: ' . $invoice);
    }

    /**
     * Handle invoice failed event
     */
    protected function handleInvoiceFailed($event)
    {
        $invoice = $event['invoice'];

        // Handle failed payment
        \Log::warning('Invoice failed: ' . $invoice);
    }

    /**
     * Handle subscription created event
     */
    protected function handleSubscriptionCreated($event)
    {
        $subscription = $event['subscription'];

        // Handle subscription creation
        \Log::info('Subscription created: ' . $subscription);
    }

    /**
     * Handle subscription cancelled event
     */
    protected function handleSubscriptionCancelled($event)
    {
        $subscription = $event['subscription'];

        // Handle subscription cancellation
        \Log::info('Subscription cancelled: ' . $subscription);
    }

    /**
     * Handle customer created event
     */
    protected function handleCustomerCreated($event)
    {
        $customer = $event['customer'];

        // Handle customer creation
        \Log::info('Customer created: ' . $customer);
    }
}

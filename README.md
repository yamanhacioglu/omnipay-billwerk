# Omnipay: Billwerk

**Billwerk (Reepay) driver for the Omnipay PHP payment processing library**

[![Latest Stable Version](https://poser.pugx.org/omnipay/billwerk/version.png)](https://packagist.org/packages/omnipay/billwerk)
[![Total Downloads](https://poser.pugx.org/omnipay/billwerk/d/total.png)](https://packagist.org/packages/omnipay/billwerk)

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP. This package implements Billwerk (Reepay) support for Omnipay.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "omnipay/billwerk": "^1.0"
    }
}
```

And run composer to update your dependencies:

    composer update

Or you can simply run:

    composer require omnipay/billwerk

## Requirements

- PHP 8.2 or higher
- Omnipay ^3.0
- Billwerk PHP SDK

## Basic Usage

The following gateways are provided by this package:

* Billwerk (Reepay)

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay)
repository.

## Code Examples

### Initialize Gateway

```php
use Omnipay\Omnipay;

$gateway = Omnipay::create('Billwerk');

$gateway->initialize([
    'apiKey' => 'your-private-api-key',
    'testMode' => true, // Set to false for production
]);
```

### Create a Purchase (One-time Charge)

```php
$response = $gateway->purchase([
    'amount' => '10.00',
    'currency' => 'EUR',
    'description' => 'Order #1234',
    'transactionId' => 'order_12345',
    'returnUrl' => 'https://example.com/return',
    'cancelUrl' => 'https://example.com/cancel',
    'card' => [
        'email' => 'customer@example.com',
        'firstName' => 'John',
        'lastName' => 'Doe',
    ]
])->send();

if ($response->isRedirect()) {
    // Redirect to Billwerk checkout page
    $response->redirect();
}
```

### Complete a Purchase

```php
$response = $gateway->completePurchase([
    'transactionReference' => $_GET['invoice']
])->send();

if ($response->isSuccessful()) {
    echo "Payment successful! Transaction: " . $response->getTransactionReference();
} else {
    echo "Payment failed: " . $response->getMessage();
}
```

### Authorize a Payment

```php
$response = $gateway->authorize([
    'amount' => '10.00',
    'currency' => 'EUR',
    'customerReference' => 'customer_123',
    'cardReference' => 'ca_xxxxx', // Saved card token
])->send();

if ($response->isSuccessful()) {
    $transactionRef = $response->getTransactionReference();
    echo "Payment authorized: " . $transactionRef;
}
```

### Capture an Authorized Payment

```php
$response = $gateway->capture([
    'transactionReference' => 'charge_handle',
    'amount' => '10.00', // Optional: partial capture
])->send();

if ($response->isSuccessful()) {
    echo "Payment captured successfully!";
}
```

### Refund a Payment

```php
$response = $gateway->refund([
    'transactionReference' => 'invoice_id',
    'amount' => '5.00', // Optional: partial refund
    'description' => 'Refund reason',
])->send();

if ($response->isSuccessful()) {
    echo "Refund processed successfully!";
}
```

### Void/Cancel a Payment

```php
$response = $gateway->void([
    'transactionReference' => 'charge_handle',
])->send();

if ($response->isSuccessful()) {
    echo "Payment cancelled successfully!";
}
```

### Create a Customer

```php
$response = $gateway->createCustomer([
    'customerReference' => 'customer_123',
    'card' => [
        'email' => 'customer@example.com',
        'firstName' => 'John',
        'lastName' => 'Doe',
        'phone' => '+1234567890',
        'address1' => '123 Main St',
        'city' => 'Copenhagen',
        'postcode' => '1234',
        'country' => 'DK',
    ]
])->send();

if ($response->isSuccessful()) {
    $customerRef = $response->getCustomerReference();
    echo "Customer created: " . $customerRef;
}
```

### Create a Payment Method (Card)

```php
$response = $gateway->createCard([
    'customerReference' => 'customer_123',
    'returnUrl' => 'https://example.com/card/return',
    'cancelUrl' => 'https://example.com/card/cancel',
])->send();

if ($response->isRedirect()) {
    // Redirect to Billwerk to add payment method
    $response->redirect();
}
```

### Create a Subscription

```php
$response = $gateway->createSubscription([
    'customerReference' => 'customer_123',
    'plan' => 'plan_basic',
    'subscriptionReference' => 'sub_123',
    'cardReference' => 'ca_xxxxx', // Optional: use saved card
])->send();

if ($response->isSuccessful()) {
    $subscriptionRef = $response->getSubscriptionReference();
    echo "Subscription created: " . $subscriptionRef;
}
```

### Cancel a Subscription

```php
$response = $gateway->cancelSubscription([
    'subscriptionReference' => 'sub_123',
])->send();

if ($response->isSuccessful()) {
    echo "Subscription cancelled successfully!";
}
```

## Laravel Integration

### Installation

The package includes a Laravel service provider. If you're using Laravel 5.5+, the service provider will be automatically discovered.

For older versions of Laravel, add the service provider to your `config/app.php`:

```php
'providers' => [
    // ...
    Omnipay\Billwerk\BillwerkServiceProvider::class,
],
```

### Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Omnipay\Billwerk\BillwerkServiceProvider"
```

Then edit `config/billwerk.php` or add to your `.env` file:

```env
BILLWERK_API_KEY=your-private-api-key
BILLWERK_TEST_MODE=true
```

### Usage in Laravel

```php
use Omnipay\Omnipay;

class PaymentController extends Controller
{
    public function checkout()
    {
        $gateway = Omnipay::create('Billwerk');
        $gateway->initialize(config('billwerk'));
        
        $response = $gateway->purchase([
            'amount' => '10.00',
            'currency' => 'EUR',
            'returnUrl' => route('payment.return'),
            'cancelUrl' => route('payment.cancel'),
        ])->send();
        
        if ($response->isRedirect()) {
            return $response->redirect();
        }
        
        return back()->with('error', $response->getMessage());
    }
}
```

Or inject via service container:

```php
public function checkout()
{
    $gateway = app('omnipay.billwerk');
    
    // Use gateway...
}
```

## Webhook Handling

To handle webhooks from Billwerk, you need to implement a webhook endpoint that verifies and processes incoming events:

```php
use Illuminate\Http\Request;

public function webhook(Request $request)
{
    $payload = $request->getContent();
    $signature = $request->header('Reepay-Signature');
    
    // Verify signature (implement your verification logic)
    
    $event = json_decode($payload, true);
    
    switch ($event['event_type']) {
        case 'invoice_settled':
            // Handle successful payment
            break;
        case 'invoice_authorized':
            // Handle authorization
            break;
        case 'subscription_created':
            // Handle subscription creation
            break;
        // Add more event types as needed
    }
    
    return response()->json(['success' => true]);
}
```

## Supported Operations

- **purchase** - Create a one-time charge session
- **completePurchase** - Verify and complete a purchase
- **authorize** - Authorize a payment
- **completeAuthorize** - Complete an authorization
- **capture** - Capture an authorized payment
- **refund** - Refund a payment
- **void** - Cancel/void an authorized payment
- **createCard** - Create a payment method session
- **deleteCard** - Delete/inactivate a payment method
- **fetchTransaction** - Fetch transaction details
- **createCustomer** - Create a customer
- **updateCustomer** - Update customer details
- **fetchCustomer** - Fetch customer details
- **createSubscription** - Create a subscription
- **cancelSubscription** - Cancel a subscription

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/thephpleague/omnipay-billwerk/issues),
or better yet, fork the library and submit a pull request.

## Testing

```bash
composer test
```

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Links

- [Billwerk Documentation](https://reference.reepay.com/api/)
- [Omnipay Documentation](https://github.com/thephpleague/omnipay)

# Omnipay Billwerk Package Summary

## 📦 Package Structure

```
omnipay-billwerk/
├── config/
│   └── billwerk.php                    # Laravel configuration file
├── examples/
│   ├── LaravelPaymentController.php    # Complete Laravel controller example
│   └── routes.php                      # Laravel routes example
├── src/
│   ├── Gateway.php                     # Main gateway class
│   ├── BillwerkServiceProvider.php     # Laravel service provider
│   └── Message/
│       ├── AbstractRequest.php         # Base request class
│       ├── AbstractResponse.php        # Base response class
│       ├── PurchaseRequest.php         # One-time charge request
│       ├── PurchaseResponse.php        # One-time charge response
│       ├── CompletePurchaseRequest.php
│       ├── CompletePurchaseResponse.php
│       ├── AuthorizeRequest.php        # Authorization request
│       ├── AuthorizeResponse.php
│       ├── CompleteAuthorizeRequest.php
│       ├── CompleteAuthorizeResponse.php
│       ├── CaptureRequest.php          # Capture authorized payment
│       ├── CaptureResponse.php
│       ├── RefundRequest.php           # Refund request
│       ├── RefundResponse.php
│       ├── VoidRequest.php             # Cancel/void request
│       ├── VoidResponse.php
│       ├── CreateCustomerRequest.php   # Customer management
│       ├── CreateCustomerResponse.php
│       ├── UpdateCustomerRequest.php
│       ├── UpdateCustomerResponse.php
│       ├── FetchCustomerRequest.php
│       ├── FetchCustomerResponse.php
│       ├── CreateCardRequest.php       # Payment method management
│       ├── CreateCardResponse.php
│       ├── DeleteCardRequest.php
│       ├── DeleteCardResponse.php
│       ├── FetchTransactionRequest.php # Transaction details
│       ├── FetchTransactionResponse.php
│       ├── CreateSubscriptionRequest.php # Subscription management
│       ├── CreateSubscriptionResponse.php
│       ├── CancelSubscriptionRequest.php
│       └── CancelSubscriptionResponse.php
├── tests/
│   └── GatewayTest.php                 # Unit tests
├── composer.json                       # Composer configuration
├── phpunit.xml.dist                    # PHPUnit configuration
├── README.md                           # Documentation
├── CHANGELOG.md                        # Version history
├── CONTRIBUTING.md                     # Contribution guidelines
├── LICENSE                             # MIT License
├── .gitignore
└── examples.php                        # Standalone PHP examples
```

## ✨ Features

### Core Payment Operations
- ✅ **Purchase** - One-time charges with hosted checkout
- ✅ **Complete Purchase** - Verify payment after customer returns
- ✅ **Authorize** - Hold funds without capturing
- ✅ **Complete Authorize** - Verify authorization
- ✅ **Capture** - Capture previously authorized payment
- ✅ **Refund** - Full or partial refunds
- ✅ **Void** - Cancel authorized payment

### Customer Management
- ✅ **Create Customer** - Create customer profiles
- ✅ **Update Customer** - Update customer information
- ✅ **Fetch Customer** - Retrieve customer details

### Payment Methods
- ✅ **Create Card** - Add payment method with hosted page
- ✅ **Delete Card** - Inactivate payment method

### Subscriptions
- ✅ **Create Subscription** - Create recurring billing
- ✅ **Cancel Subscription** - Cancel active subscription

### Additional Features
- ✅ **Fetch Transaction** - Get transaction details
- ✅ **Test Mode** - Sandbox environment support
- ✅ **Webhook Support** - Handle Billwerk events
- ✅ **Laravel Integration** - Service provider and configuration

## 🔧 Installation

```bash
composer require omnipay/billwerk
```

## 📚 Quick Start

### Standalone PHP

```php
use Omnipay\Omnipay;

$gateway = Omnipay::create('Billwerk');
$gateway->initialize([
    'apiKey' => 'your-private-api-key',
    'testMode' => true,
]);

$response = $gateway->purchase([
    'amount' => '10.00',
    'currency' => 'EUR',
    'returnUrl' => 'https://example.com/return',
])->send();

if ($response->isRedirect()) {
    $response->redirect();
}
```

### Laravel

1. **Publish configuration:**
```bash
php artisan vendor:publish --provider="Omnipay\Billwerk\BillwerkServiceProvider"
```

2. **Configure .env:**
```env
BILLWERK_API_KEY=your-private-api-key
BILLWERK_TEST_MODE=true
```

3. **Use in controller:**
```php
$gateway = Omnipay::create('Billwerk');
$gateway->initialize(config('billwerk'));

$response = $gateway->purchase([...])->send();
```

## 🎯 Supported Methods

| Method | Description | Status |
|--------|-------------|--------|
| `purchase()` | Create one-time charge | ✅ |
| `completePurchase()` | Verify purchase | ✅ |
| `authorize()` | Authorize payment | ✅ |
| `completeAuthorize()` | Verify authorization | ✅ |
| `capture()` | Capture authorized payment | ✅ |
| `refund()` | Refund payment | ✅ |
| `void()` | Cancel authorized payment | ✅ |
| `createCustomer()` | Create customer | ✅ |
| `updateCustomer()` | Update customer | ✅ |
| `fetchCustomer()` | Get customer details | ✅ |
| `createCard()` | Add payment method | ✅ |
| `deleteCard()` | Remove payment method | ✅ |
| `fetchTransaction()` | Get transaction | ✅ |
| `createSubscription()` | Create subscription | ✅ |
| `cancelSubscription()` | Cancel subscription | ✅ |

## 📝 Configuration Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `apiKey` | string | ✅ | Billwerk private API key |
| `testMode` | boolean | ❌ | Use test environment (default: false) |

## 🔗 Common Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `amount` | string | Amount (e.g., '10.00') |
| `currency` | string | ISO currency code (e.g., 'EUR') |
| `description` | string | Payment description |
| `transactionId` | string | Your order/transaction ID |
| `returnUrl` | string | URL to return after payment |
| `cancelUrl` | string | URL for cancelled payments |
| `customerReference` | string | Customer handle/ID |
| `cardReference` | string | Saved card token |
| `subscriptionReference` | string | Subscription handle/ID |

## 🌐 Webhook Events

Billwerk sends webhook events for various payment events:

- `invoice_settled` - Payment completed
- `invoice_authorized` - Payment authorized
- `invoice_failed` - Payment failed
- `subscription_created` - Subscription created
- `subscription_cancelled` - Subscription cancelled
- `customer_created` - Customer created

Example webhook handler included in `examples/LaravelPaymentController.php`

## 🧪 Testing

```bash
composer test
```

## 📖 Documentation

- Full README: `README.md`
- Laravel Examples: `examples/LaravelPaymentController.php`
- Standalone Examples: `examples.php`
- Laravel Routes: `examples/routes.php`

## 🔐 Security

- Never expose your private API key in client-side code
- Always validate webhook signatures
- Use HTTPS for production
- Implement proper CSRF protection for Laravel routes
- Exclude webhook routes from CSRF middleware

## 🚀 Requirements

- PHP 8.2 or higher
- Omnipay ^3.0
- Billwerk PHP SDK ^1.0

## 📄 License

MIT License - see LICENSE file for details

## 🤝 Contributing

Contributions are welcome! Please see CONTRIBUTING.md for details.

## 📞 Support

- GitHub Issues: For bug reports and feature requests
- Documentation: Full API reference at https://reference.reepay.com/api/
- Stack Overflow: Tag questions with `omnipay` and `billwerk`

## ✅ Checklist for Implementation

- [ ] Install package via Composer
- [ ] Configure API credentials
- [ ] Set up routes (Laravel)
- [ ] Implement payment flow
- [ ] Set up webhook handler
- [ ] Test in sandbox mode
- [ ] Implement error handling
- [ ] Add logging
- [ ] Test all payment scenarios
- [ ] Deploy to production
- [ ] Monitor webhook events

---

**Built with ❤️ for the Omnipay community**

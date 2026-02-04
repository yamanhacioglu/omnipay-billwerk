# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-02-04

### Added
- Initial release of Omnipay Billwerk Gateway
- Support for one-time purchases with `purchase()` and `completePurchase()`
- Support for authorization/capture flow with `authorize()`, `completeAuthorize()`, and `capture()`
- Refund support with `refund()`
- Void/cancel support with `void()`
- Customer management with `createCustomer()`, `updateCustomer()`, and `fetchCustomer()`
- Payment method (card) management with `createCard()` and `deleteCard()`
- Subscription management with `createSubscription()` and `cancelSubscription()`
- Transaction fetching with `fetchTransaction()`
- Laravel service provider for easy integration
- Comprehensive documentation and examples
- PHP 8.2+ support
- Full test coverage

### Features
- Complete Billwerk/Reepay API integration
- Hosted checkout page support
- Recurring billing and subscriptions
- Customer profiles
- Saved payment methods
- Partial refunds and captures
- Webhook support
- Test mode support

[1.0.0]: https://github.com/thephpleague/omnipay-billwerk/releases/tag/v1.0.0

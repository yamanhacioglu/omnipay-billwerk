<?php

/**
 * Laravel Routes Example for Billwerk Gateway
 *
 * Add these routes to your routes/web.php file
 */

use App\Http\Controllers\PaymentController;

Route::group(['prefix' => 'payment', 'middleware' => ['web']], function () {

    // Checkout
    Route::get('/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::post('/process', [PaymentController::class, 'processPayment'])->name('payment.process');

    // Return URLs
    Route::get('/return', [PaymentController::class, 'returnFromPayment'])->name('payment.return');
    Route::get('/cancel', [PaymentController::class, 'cancelPayment'])->name('payment.cancel');

    // Customer Management
    Route::post('/customer/create', [PaymentController::class, 'createCustomer'])->name('customer.create');

    // Card Management
    Route::post('/card/add', [PaymentController::class, 'addPaymentMethod'])->name('card.add');
    Route::get('/card/return', [PaymentController::class, 'cardReturn'])->name('card.return');
    Route::get('/card/cancel', [PaymentController::class, 'cardCancel'])->name('card.cancel');

    // Subscription Management
    Route::post('/subscription/create', [PaymentController::class, 'createSubscription'])->name('subscription.create');
    Route::delete('/subscription/{id}/cancel', [PaymentController::class, 'cancelSubscription'])->name('subscription.cancel');

    // Refunds
    Route::post('/refund', [PaymentController::class, 'refund'])->name('payment.refund');
});

// Webhook (should be excluded from CSRF protection)
Route::post('/webhook/billwerk', [PaymentController::class, 'webhook'])->name('webhook.billwerk');

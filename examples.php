<?php

/**
 * Omnipay Billwerk Gateway - Usage Examples
 *
 * This file demonstrates how to use the Omnipay Billwerk Gateway
 */

require_once __DIR__ . '/vendor/autoload.php';

use Omnipay\Omnipay;

// Create gateway instance
$gateway = Omnipay::create('Billwerk');

// Initialize with your credentials
$gateway->initialize([
    'apiKey' => 'priv_xxxxxxxxxxxxxxxxxxxxx', // Your private API key
    'testMode' => true, // Set to false for production
]);

// ============================================================================
// Example 1: Create a one-time purchase
// ============================================================================

echo "Example 1: Create a Purchase\n";
echo "============================\n\n";

try {
    $response = $gateway->purchase([
        'amount' => '29.99',
        'currency' => 'EUR',
        'description' => 'Premium Package',
        'transactionId' => 'ORDER-' . uniqid(),
        'returnUrl' => 'https://example.com/payment/success',
        'cancelUrl' => 'https://example.com/payment/cancel',
        'card' => [
            'email' => 'customer@example.com',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'phone' => '+45 12345678',
            'address1' => 'Nørregade 10',
            'city' => 'Copenhagen',
            'postcode' => '1165',
            'country' => 'DK',
        ]
    ])->send();

    if ($response->isRedirect()) {
        echo "Redirecting to: " . $response->getRedirectUrl() . "\n\n";
        // In a web application, you would redirect:
        // $response->redirect();
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 2: Complete a purchase (after customer returns)
// ============================================================================

echo "Example 2: Complete Purchase\n";
echo "============================\n\n";

try {
    // Typically you would get this from $_GET['invoice']
    $invoiceId = 'inv_xxxxxxxxxxxxxxxx';

    $response = $gateway->completePurchase([
        'transactionReference' => $invoiceId,
    ])->send();

    if ($response->isSuccessful()) {
        echo "Payment successful!\n";
        echo "Transaction ID: " . $response->getTransactionReference() . "\n";
        echo "Amount: " . $response->getAmount() . " " . $response->getCurrency() . "\n";
        echo "Status: " . $response->getState() . "\n\n";
    } else {
        echo "Payment failed: " . $response->getMessage() . "\n\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 3: Create a customer
// ============================================================================

echo "Example 3: Create Customer\n";
echo "============================\n\n";

try {
    $response = $gateway->createCustomer([
        'customerReference' => 'CUST-' . uniqid(),
        'card' => [
            'email' => 'john.doe@example.com',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'company' => 'Acme Corp',
            'phone' => '+45 12345678',
            'address1' => 'Nørregade 10',
            'city' => 'Copenhagen',
            'postcode' => '1165',
            'country' => 'DK',
        ]
    ])->send();

    if ($response->isSuccessful()) {
        echo "Customer created successfully!\n";
        echo "Customer Reference: " . $response->getCustomerReference() . "\n\n";
    } else {
        echo "Failed to create customer: " . $response->getMessage() . "\n\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 4: Create a payment method (card)
// ============================================================================

echo "Example 4: Create Payment Method\n";
echo "============================\n\n";

try {
    $customerRef = 'CUST-xxxxx'; // Existing customer

    $response = $gateway->createCard([
        'customerReference' => $customerRef,
        'returnUrl' => 'https://example.com/card/success',
        'cancelUrl' => 'https://example.com/card/cancel',
    ])->send();

    if ($response->isRedirect()) {
        echo "Redirecting to add payment method: " . $response->getRedirectUrl() . "\n\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 5: Authorize and capture
// ============================================================================

echo "Example 5: Authorize and Capture\n";
echo "============================\n\n";

try {
    // Step 1: Authorize
    $authorizeResponse = $gateway->authorize([
        'amount' => '49.99',
        'currency' => 'EUR',
        'customerReference' => 'CUST-xxxxx',
        'cardReference' => 'ca_xxxxx', // Saved card
    ])->send();

    if ($authorizeResponse->isSuccessful()) {
        $chargeHandle = $authorizeResponse->getTransactionReference();
        echo "Payment authorized: " . $chargeHandle . "\n";

        // Step 2: Capture (settle) the authorization
        $captureResponse = $gateway->capture([
            'transactionReference' => $chargeHandle,
        ])->send();

        if ($captureResponse->isSuccessful()) {
            echo "Payment captured successfully!\n\n";
        } else {
            echo "Capture failed: " . $captureResponse->getMessage() . "\n\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 6: Refund a payment
// ============================================================================

echo "Example 6: Refund Payment\n";
echo "============================\n\n";

try {
    $response = $gateway->refund([
        'transactionReference' => 'inv_xxxxx',
        'amount' => '10.00', // Partial refund
        'description' => 'Customer requested refund',
    ])->send();

    if ($response->isSuccessful()) {
        echo "Refund processed successfully!\n";
        echo "Refund ID: " . $response->getTransactionReference() . "\n\n";
    } else {
        echo "Refund failed: " . $response->getMessage() . "\n\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 7: Create a subscription
// ============================================================================

echo "Example 7: Create Subscription\n";
echo "============================\n\n";

try {
    $response = $gateway->createSubscription([
        'customerReference' => 'CUST-xxxxx',
        'plan' => 'plan_monthly_basic',
        'subscriptionReference' => 'SUB-' . uniqid(),
        'cardReference' => 'ca_xxxxx', // Optional: use saved card
    ])->send();

    if ($response->isSuccessful()) {
        echo "Subscription created successfully!\n";
        echo "Subscription Reference: " . $response->getSubscriptionReference() . "\n";
        echo "Status: " . $response->getState() . "\n\n";
    } else {
        echo "Failed to create subscription: " . $response->getMessage() . "\n\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 8: Cancel a subscription
// ============================================================================

echo "Example 8: Cancel Subscription\n";
echo "============================\n\n";

try {
    $response = $gateway->cancelSubscription([
        'subscriptionReference' => 'SUB-xxxxx',
    ])->send();

    if ($response->isSuccessful()) {
        echo "Subscription cancelled successfully!\n\n";
    } else {
        echo "Failed to cancel subscription: " . $response->getMessage() . "\n\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

// ============================================================================
// Example 9: Fetch transaction details
// ============================================================================

echo "Example 9: Fetch Transaction\n";
echo "============================\n\n";

try {
    $response = $gateway->fetchTransaction([
        'transactionReference' => 'transaction_id',
    ])->send();

    if ($response->isSuccessful()) {
        echo "Transaction Details:\n";
        echo "ID: " . $response->getTransactionReference() . "\n";
        echo "State: " . $response->getState() . "\n";
        echo "Type: " . $response->getType() . "\n\n";
    } else {
        echo "Failed to fetch transaction: " . $response->getMessage() . "\n\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

echo "Examples completed!\n";

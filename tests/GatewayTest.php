<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Tests;

use Omnipay\Billwerk\Gateway;
use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    /**
     * @var Gateway
     */
    protected $gateway;

    public function setUp(): void
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setApiKey('test_key');
        $this->gateway->setTestMode(true);
    }

    public function testGetName(): void
    {
        $this->assertEquals('Billwerk', $this->gateway->getName());
    }

    public function testGetShortName(): void
    {
        $this->assertEquals('Billwerk', $this->gateway->getShortName());
    }

    public function testDefaultParameters(): void
    {
        $this->assertEquals('test_key', $this->gateway->getApiKey());
        $this->assertTrue($this->gateway->getTestMode());
    }

    public function testPurchase(): void
    {
        $request = $this->gateway->purchase([
            'amount' => '10.00',
            'currency' => 'EUR',
        ]);

        $this->assertInstanceOf('Omnipay\Billwerk\Message\PurchaseRequest', $request);
        $this->assertEquals('10.00', $request->getAmount());
        $this->assertEquals('EUR', $request->getCurrency());
    }

    public function testAuthorize(): void
    {
        $request = $this->gateway->authorize([
            'amount' => '10.00',
            'currency' => 'EUR',
            'customerReference' => 'customer_123',
        ]);

        $this->assertInstanceOf('Omnipay\Billwerk\Message\AuthorizeRequest', $request);
    }

    public function testCapture(): void
    {
        $request = $this->gateway->capture([
            'transactionReference' => 'charge_123',
        ]);

        $this->assertInstanceOf('Omnipay\Billwerk\Message\CaptureRequest', $request);
    }

    public function testRefund(): void
    {
        $request = $this->gateway->refund([
            'transactionReference' => 'invoice_123',
        ]);

        $this->assertInstanceOf('Omnipay\Billwerk\Message\RefundRequest', $request);
    }

    public function testVoid(): void
    {
        $request = $this->gateway->void([
            'transactionReference' => 'charge_123',
        ]);

        $this->assertInstanceOf('Omnipay\Billwerk\Message\VoidRequest', $request);
    }

    public function testCreateCustomer(): void
    {
        $request = $this->gateway->createCustomer([
            'customerReference' => 'customer_123',
        ]);

        $this->assertInstanceOf('Omnipay\Billwerk\Message\CreateCustomerRequest', $request);
    }

    public function testCreateSubscription(): void
    {
        $request = $this->gateway->createSubscription([
            'customerReference' => 'customer_123',
            'plan' => 'plan_basic',
        ]);

        $this->assertInstanceOf('Omnipay\Billwerk\Message\CreateSubscriptionRequest', $request);
    }
}

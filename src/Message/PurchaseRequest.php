<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Message;

use Billwerk\Sdk\Model\Checkout\Session\SessionChargeModel;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Purchase Request
 *
 * Creates a checkout session for a one-time charge
 */
class PurchaseRequest extends AbstractRequest
{
    /**
     * Get the data for this request
     *
     * @return array<string, mixed>
     * @throws InvalidRequestException
     */
    public function getData(): array
    {
        $this->validate('amount', 'currency');

        $data = [
            'order' => [
                'handle' => $this->getTransactionId() ?? uniqid('order_', true),
                'amount' => $this->getAmountInteger(),
                'currency' => $this->getCurrency(),
                'customer' => [
                    'handle' => $this->getCustomerReference() ?? uniqid('customer_', true),
                ],
            ],
            'settle' => true,
            'accept_url' => $this->getReturnUrl(),
            'cancel_url' => $this->getCancelUrl(),
        ];

        // Add optional description
        if ($this->getDescription()) {
            $data['order']['order_lines'] = [
                [
                    'ordertext' => $this->getDescription(),
                    'amount' => $this->getAmountInteger(),
                    'quantity' => 1,
                ]
            ];
        }

        // Add customer email if provided
        if ($this->getCard() && $this->getCard()->getEmail()) {
            $data['order']['customer']['email'] = $this->getCard()->getEmail();

            if ($this->getCard()->getFirstName()) {
                $data['order']['customer']['first_name'] = $this->getCard()->getFirstName();
            }

            if ($this->getCard()->getLastName()) {
                $data['order']['customer']['last_name'] = $this->getCard()->getLastName();
            }

            if ($this->getCard()->getPhone()) {
                $data['order']['customer']['phone'] = $this->getCard()->getPhone();
            }

            if ($this->getCard()->getAddress1()) {
                $data['order']['customer']['address'] = $this->getCard()->getAddress1();

                if ($this->getCard()->getAddress2()) {
                    $data['order']['customer']['address2'] = $this->getCard()->getAddress2();
                }
            }

            if ($this->getCard()->getCity()) {
                $data['order']['customer']['city'] = $this->getCard()->getCity();
            }

            if ($this->getCard()->getPostcode()) {
                $data['order']['customer']['postal_code'] = $this->getCard()->getPostcode();
            }

            if ($this->getCard()->getCountry()) {
                $data['order']['customer']['country'] = $this->getCard()->getCountry();
            }
        }

        return $data;
    }

    /**
     * Send the request
     *
     * @param array<string, mixed> $data
     * @return PurchaseResponse
     */
    public function sendData($data): PurchaseResponse
    {
        try {
            $sessionModel = SessionChargeModel::fromArray($data);
            $session = $this->getSdk()->session()->charge($sessionModel);

            $responseData = [
                'id' => $session->getId(),
                'url' => $session->getUrl(),
            ];

            return new PurchaseResponse($this, $responseData);
        } catch (\Exception $e) {
            return new PurchaseResponse($this, [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
        }
    }
}

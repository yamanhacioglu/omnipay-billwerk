<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Message;

use Billwerk\Sdk\Model\Checkout\Session\SessionRecurringModel;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Create Card Request
 *
 * Creates a payment method (card) for a customer
 */
class CreateCardRequest extends AbstractRequest
{
    /**
     * Get the data for this request
     *
     * @return array<string, mixed>
     * @throws InvalidRequestException
     */
    public function getData(): array
    {
        $this->validate('customerReference');

        $data = [
            'customer' => $this->getCustomerReference(),
            'accept_url' => $this->getReturnUrl(),
            'cancel_url' => $this->getCancelUrl(),
        ];

        return $data;
    }

    /**
     * Send the request
     *
     * @param array<string, mixed> $data
     * @return CreateCardResponse
     */
    public function sendData($data): CreateCardResponse
    {
        try {
            $sessionModel = SessionRecurringModel::fromArray($data);
            $session = $this->getSdk()->session()->recurring($sessionModel);

            $responseData = [
                'id' => $session->getId(),
                'url' => $session->getUrl(),
            ];

            return new CreateCardResponse($this, $responseData);
        } catch (\Exception $e) {
            return new CreateCardResponse($this, [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
        }
    }
}

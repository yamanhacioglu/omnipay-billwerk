<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Message;

use Billwerk\Sdk\Model\PaymentMethod\PaymentMethodInactivateModel;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Delete Card Request
 *
 * Inactivates a payment method
 */
class DeleteCardRequest extends AbstractRequest
{
    /**
     * Get the data for this request
     *
     * @return array<string, mixed>
     * @throws InvalidRequestException
     */
    public function getData(): array
    {
        $this->validate('cardReference');

        return [
            'id' => $this->getCardReference(),
        ];
    }

    /**
     * Send the request
     *
     * @param array<string, mixed> $data
     * @return DeleteCardResponse
     */
    public function sendData($data): DeleteCardResponse
    {
        try {
            $paymentMethodModel = new PaymentMethodInactivateModel();
            $paymentMethodModel->setId($data['id']);

            $paymentMethod = $this->getSdk()->paymentMethod()->inactivate($paymentMethodModel);

            $responseData = [
                'id' => $paymentMethod->getId(),
                'state' => $paymentMethod->getState(),
                'deleted' => true,
            ];

            return new DeleteCardResponse($this, $responseData);
        } catch (\Exception $e) {
            return new DeleteCardResponse($this, [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
        }
    }

    /**
     * Get card reference
     *
     * @return string|null
     */
    public function getCardReference(): ?string
    {
        return $this->getParameter('cardReference');
    }

    /**
     * Set card reference
     *
     * @param string $value
     * @return $this
     */
    public function setCardReference(string $value): self
    {
        return $this->setParameter('cardReference', $value);
    }
}

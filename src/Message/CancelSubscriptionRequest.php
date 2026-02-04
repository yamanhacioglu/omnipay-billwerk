<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Message;

use Billwerk\Sdk\Model\Agreement\AgreementCancelModel;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Cancel Subscription Request
 *
 * Cancels an active subscription
 */
class CancelSubscriptionRequest extends AbstractRequest
{
    /**
     * Get the data for this request
     *
     * @return array<string, mixed>
     * @throws InvalidRequestException
     */
    public function getData(): array
    {
        $this->validate('subscriptionReference');

        $data = [
            'handle' => $this->getSubscriptionReference(),
        ];

        return $data;
    }

    /**
     * Send the request
     *
     * @param array<string, mixed> $data
     * @return CancelSubscriptionResponse
     */
    public function sendData($data): CancelSubscriptionResponse
    {
        try {
            $cancelModel = new AgreementCancelModel();
            $cancelModel->setHandle($data['handle']);

            $agreement = $this->getSdk()->agreement()->cancel($cancelModel);

            $responseData = [
                'handle' => $agreement->getHandle(),
                'state' => $agreement->getState(),
                'cancelled' => true,
            ];

            return new CancelSubscriptionResponse($this, $responseData);
        } catch (\Exception $e) {
            return new CancelSubscriptionResponse($this, [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
        }
    }

    /**
     * Get subscription reference
     *
     * @return string|null
     */
    public function getSubscriptionReference(): ?string
    {
        return $this->getParameter('subscriptionReference');
    }

    /**
     * Set subscription reference
     *
     * @param string $value
     * @return $this
     */
    public function setSubscriptionReference(string $value): self
    {
        return $this->setParameter('subscriptionReference', $value);
    }
}

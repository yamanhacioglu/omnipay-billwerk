<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Message;

use Billwerk\Sdk\Model\Agreement\AgreementCreateModel;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Create Subscription Request
 *
 * Creates a new subscription for a customer
 */
class CreateSubscriptionRequest extends AbstractRequest
{
    /**
     * Get the data for this request
     *
     * @return array<string, mixed>
     * @throws InvalidRequestException
     */
    public function getData(): array
    {
        $this->validate('customerReference', 'plan');

        $data = [
            'handle' => $this->getSubscriptionReference() ?? uniqid('sub_', true),
            'customer' => $this->getCustomerReference(),
            'plan' => $this->getPlan(),
        ];

        if ($this->getStartDate()) {
            $data['start_date'] = $this->getStartDate();
        }

        if ($this->getDescription()) {
            $data['description'] = $this->getDescription();
        }

        // If card reference is provided
        if ($this->getCardReference()) {
            $data['source'] = $this->getCardReference();
        }

        return $data;
    }

    /**
     * Send the request
     *
     * @param array<string, mixed> $data
     * @return CreateSubscriptionResponse
     */
    public function sendData($data): CreateSubscriptionResponse
    {
        try {
            $agreementModel = AgreementCreateModel::fromArray($data);
            $agreement = $this->getSdk()->agreement()->create($agreementModel);

            $responseData = [
                'handle' => $agreement->getHandle(),
                'state' => $agreement->getState(),
                'plan' => $agreement->getPlan(),
                'customer' => $agreement->getCustomer(),
                'created' => $agreement->getCreated(),
            ];

            return new CreateSubscriptionResponse($this, $responseData);
        } catch (\Exception $e) {
            return new CreateSubscriptionResponse($this, [
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

    /**
     * Get plan
     *
     * @return string|null
     */
    public function getPlan(): ?string
    {
        return $this->getParameter('plan');
    }

    /**
     * Set plan
     *
     * @param string $value
     * @return $this
     */
    public function setPlan(string $value): self
    {
        return $this->setParameter('plan', $value);
    }

    /**
     * Get start date
     *
     * @return string|null
     */
    public function getStartDate(): ?string
    {
        return $this->getParameter('startDate');
    }

    /**
     * Set start date
     *
     * @param string $value
     * @return $this
     */
    public function setStartDate(string $value): self
    {
        return $this->setParameter('startDate', $value);
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

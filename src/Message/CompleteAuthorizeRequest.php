<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Complete Authorize Request
 *
 * Completes an authorization after customer returns from checkout
 */
class CompleteAuthorizeRequest extends AbstractRequest
{
    /**
     * Get the data for this request
     *
     * @return array<string, mixed>
     * @throws InvalidRequestException
     */
    public function getData(): array
    {
        $chargeId = $this->httpRequest->query->get('charge')
                 ?? $this->httpRequest->query->get('id')
                 ?? $this->getTransactionReference();

        if (!$chargeId) {
            throw new InvalidRequestException('Missing charge reference');
        }

        return [
            'charge' => $chargeId,
        ];
    }

    /**
     * Send the request
     *
     * @param array<string, mixed> $data
     * @return CompleteAuthorizeResponse
     */
    public function sendData($data): CompleteAuthorizeResponse
    {
        try {
            $chargeModel = new \Billwerk\Sdk\Model\Charge\ChargeGetModel();
            $chargeModel->setHandle($data['charge']);

            $charge = $this->getSdk()->charge()->get($chargeModel);

            $responseData = [
                'handle' => $charge->getHandle(),
                'state' => $charge->getState(),
                'amount' => $charge->getAmount(),
                'currency' => $charge->getCurrency(),
                'authorized' => $charge->getAuthorized(),
            ];

            return new CompleteAuthorizeResponse($this, $responseData);
        } catch (\Exception $e) {
            return new CompleteAuthorizeResponse($this, [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
        }
    }
}

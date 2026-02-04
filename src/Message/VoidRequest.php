<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Message;

use Billwerk\Sdk\Model\Charge\ChargeCancelModel;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Void Request
 *
 * Cancels an authorized charge
 */
class VoidRequest extends AbstractRequest
{
    /**
     * Get the data for this request
     *
     * @return array<string, mixed>
     * @throws InvalidRequestException
     */
    public function getData(): array
    {
        $this->validate('transactionReference');

        return [
            'handle' => $this->getTransactionReference(),
        ];
    }

    /**
     * Send the request
     *
     * @param array<string, mixed> $data
     * @return VoidResponse
     */
    public function sendData($data): VoidResponse
    {
        try {
            $cancelModel = new ChargeCancelModel();
            $cancelModel->setHandle($data['handle']);

            $charge = $this->getSdk()->charge()->cancel($cancelModel);

            $responseData = [
                'handle' => $charge->getHandle(),
                'state' => $charge->getState(),
                'cancelled' => true,
            ];

            return new VoidResponse($this, $responseData);
        } catch (\Exception $e) {
            return new VoidResponse($this, [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
        }
    }
}

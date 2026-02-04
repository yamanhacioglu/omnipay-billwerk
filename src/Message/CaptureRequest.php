<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Message;

use Billwerk\Sdk\Model\Charge\ChargeSettleModel;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Capture Request
 *
 * Settles (captures) a previously authorized charge
 */
class CaptureRequest extends AbstractRequest
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

        $data = [
            'handle' => $this->getTransactionReference(),
        ];

        // Optional partial capture amount
        if ($this->getAmount()) {
            $data['amount'] = $this->getAmountInteger();
        }

        return $data;
    }

    /**
     * Send the request
     *
     * @param array<string, mixed> $data
     * @return CaptureResponse
     */
    public function sendData($data): CaptureResponse
    {
        try {
            $settleModel = new ChargeSettleModel();
            $settleModel->setHandle($data['handle']);

            if (isset($data['amount'])) {
                $settleModel->setAmount($data['amount']);
            }

            $charge = $this->getSdk()->charge()->settle($settleModel);

            $responseData = [
                'handle' => $charge->getHandle(),
                'state' => $charge->getState(),
                'amount' => $charge->getAmount(),
                'currency' => $charge->getCurrency(),
                'settled' => $charge->getSettled(),
            ];

            return new CaptureResponse($this, $responseData);
        } catch (\Exception $e) {
            return new CaptureResponse($this, [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
        }
    }
}

<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Message;

use Billwerk\Sdk\Model\Refund\RefundCreateModel;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Refund Request
 *
 * Creates a refund for a settled charge or invoice
 */
class RefundRequest extends AbstractRequest
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
            'invoice' => $this->getTransactionReference(),
        ];

        // Optional partial refund amount
        if ($this->getAmount()) {
            $data['amount'] = $this->getAmountInteger();
        }

        if ($this->getDescription()) {
            $data['text'] = $this->getDescription();
        }

        return $data;
    }

    /**
     * Send the request
     *
     * @param array<string, mixed> $data
     * @return RefundResponse
     */
    public function sendData($data): RefundResponse
    {
        try {
            $refundModel = RefundCreateModel::fromArray($data);
            $refund = $this->getSdk()->refund()->create($refundModel);

            $responseData = [
                'id' => $refund->getId(),
                'state' => $refund->getState(),
                'amount' => $refund->getAmount(),
                'currency' => $refund->getCurrency(),
                'created' => $refund->getCreated(),
            ];

            return new RefundResponse($this, $responseData);
        } catch (\Exception $e) {
            return new RefundResponse($this, [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
        }
    }
}

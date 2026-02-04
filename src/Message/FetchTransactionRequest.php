<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Message;

use Billwerk\Sdk\Model\Transaction\TransactionGetModel;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Fetch Transaction Request
 *
 * Retrieves transaction details from Billwerk
 */
class FetchTransactionRequest extends AbstractRequest
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
            'id' => $this->getTransactionReference(),
        ];
    }

    /**
     * Send the request
     *
     * @param array<string, mixed> $data
     * @return FetchTransactionResponse
     */
    public function sendData($data): FetchTransactionResponse
    {
        try {
            $transactionModel = new TransactionGetModel();
            $transactionModel->setId($data['id']);

            $transaction = $this->getSdk()->transaction()->get($transactionModel);

            $responseData = [
                'id' => $transaction->getId(),
                'state' => $transaction->getState(),
                'amount' => $transaction->getAmount(),
                'currency' => $transaction->getCurrency(),
                'type' => $transaction->getType(),
                'created' => $transaction->getCreated(),
            ];

            return new FetchTransactionResponse($this, $responseData);
        } catch (\Exception $e) {
            return new FetchTransactionResponse($this, [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
        }
    }
}

<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Message;

use Billwerk\Sdk\Model\Invoice\InvoiceGetModel;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Complete Purchase Request
 *
 * Verifies and completes a purchase after customer returns from checkout
 */
class CompletePurchaseRequest extends AbstractRequest
{
    /**
     * Get the data for this request
     *
     * @return array<string, mixed>
     * @throws InvalidRequestException
     */
    public function getData(): array
    {
        $invoiceId = $this->httpRequest->query->get('invoice')
                  ?? $this->httpRequest->query->get('id')
                  ?? $this->getTransactionReference();

        if (!$invoiceId) {
            throw new InvalidRequestException('Missing invoice reference');
        }

        return [
            'invoice' => $invoiceId,
        ];
    }

    /**
     * Send the request
     *
     * @param array<string, mixed> $data
     * @return CompletePurchaseResponse
     */
    public function sendData($data): CompletePurchaseResponse
    {
        try {
            $invoiceModel = new InvoiceGetModel();
            $invoiceModel->setId($data['invoice']);

            $invoice = $this->getSdk()->invoice()->get($invoiceModel);

            $responseData = [
                'id' => $invoice->getId(),
                'state' => $invoice->getState(),
                'amount' => $invoice->getAmount(),
                'currency' => $invoice->getCurrency(),
                'settled' => $invoice->getSettled(),
                'authorized' => $invoice->getAuthorized(),
            ];

            return new CompletePurchaseResponse($this, $responseData);
        } catch (\Exception $e) {
            return new CompletePurchaseResponse($this, [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
        }
    }
}

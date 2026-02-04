<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Message;

use Billwerk\Sdk\Model\Customer\CustomerGetModel;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Fetch Customer Request
 *
 * Retrieves customer details from Billwerk
 */
class FetchCustomerRequest extends AbstractRequest
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

        return [
            'handle' => $this->getCustomerReference(),
        ];
    }

    /**
     * Send the request
     *
     * @param array<string, mixed> $data
     * @return FetchCustomerResponse
     */
    public function sendData($data): FetchCustomerResponse
    {
        try {
            $customerModel = new CustomerGetModel();
            $customerModel->setHandle($data['handle']);

            $customer = $this->getSdk()->customer()->get($customerModel);

            $responseData = [
                'handle' => $customer->getHandle(),
                'email' => $customer->getEmail(),
                'first_name' => $customer->getFirstName(),
                'last_name' => $customer->getLastName(),
                'created' => $customer->getCreated(),
            ];

            return new FetchCustomerResponse($this, $responseData);
        } catch (\Exception $e) {
            return new FetchCustomerResponse($this, [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
        }
    }
}

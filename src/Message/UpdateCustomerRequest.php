<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Message;

use Billwerk\Sdk\Model\Customer\CustomerUpdateModel;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Update Customer Request
 *
 * Updates an existing customer in Billwerk
 */
class UpdateCustomerRequest extends AbstractRequest
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

        $data = [
            'handle' => $this->getCustomerReference(),
        ];

        if ($this->getCard()) {
            $card = $this->getCard();

            if ($card->getEmail()) {
                $data['email'] = $card->getEmail();
            }

            if ($card->getFirstName()) {
                $data['first_name'] = $card->getFirstName();
            }

            if ($card->getLastName()) {
                $data['last_name'] = $card->getLastName();
            }

            if ($card->getPhone()) {
                $data['phone'] = $card->getPhone();
            }

            if ($card->getCompany()) {
                $data['company'] = $card->getCompany();
            }

            if ($card->getAddress1()) {
                $data['address'] = $card->getAddress1();

                if ($card->getAddress2()) {
                    $data['address2'] = $card->getAddress2();
                }
            }

            if ($card->getCity()) {
                $data['city'] = $card->getCity();
            }

            if ($card->getPostcode()) {
                $data['postal_code'] = $card->getPostcode();
            }

            if ($card->getCountry()) {
                $data['country'] = $card->getCountry();
            }
        }

        return $data;
    }

    /**
     * Send the request
     *
     * @param array<string, mixed> $data
     * @return UpdateCustomerResponse
     */
    public function sendData($data): UpdateCustomerResponse
    {
        try {
            $customerModel = CustomerUpdateModel::fromArray($data);
            $customer = $this->getSdk()->customer()->update($customerModel);

            $responseData = [
                'handle' => $customer->getHandle(),
                'email' => $customer->getEmail(),
            ];

            return new UpdateCustomerResponse($this, $responseData);
        } catch (\Exception $e) {
            return new UpdateCustomerResponse($this, [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
        }
    }
}

<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Message;

use Billwerk\Sdk\Model\Customer\CustomerCreateModel;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Create Customer Request
 *
 * Creates a new customer in Billwerk
 */
class CreateCustomerRequest extends AbstractRequest
{
    /**
     * Get the data for this request
     *
     * @return array<string, mixed>
     * @throws InvalidRequestException
     */
    public function getData(): array
    {
        $data = [
            'handle' => $this->getCustomerReference() ?? uniqid('customer_', true),
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
     * @return CreateCustomerResponse
     */
    public function sendData($data): CreateCustomerResponse
    {
        try {
            $customerModel = CustomerCreateModel::fromArray($data);
            $customer = $this->getSdk()->customer()->create($customerModel);

            $responseData = [
                'handle' => $customer->getHandle(),
                'email' => $customer->getEmail(),
                'created' => $customer->getCreated(),
            ];

            return new CreateCustomerResponse($this, $responseData);
        } catch (\Exception $e) {
            return new CreateCustomerResponse($this, [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
        }
    }
}

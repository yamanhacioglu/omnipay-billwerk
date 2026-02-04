<?php

declare(strict_types=1);

namespace Omnipay\Billwerk\Message;

use Omnipay\Common\Message\AbstractResponse as BaseAbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;

/**
 * Abstract Response
 *
 * Base class for all Billwerk responses
 */
abstract class AbstractResponse extends BaseAbstractResponse implements RedirectResponseInterface
{
    /**
     * @var array<string, mixed>
     */
    protected $data;

    /**
     * Constructor
     *
     * @param RequestInterface $request
     * @param array<string, mixed> $data
     */
    public function __construct(RequestInterface $request, array $data)
    {
        $this->request = $request;
        $this->data = $data;
    }

    /**
     * Is the response successful?
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return !$this->isRedirect() && !isset($this->data['error']);
    }

    /**
     * Get response data
     *
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get transaction reference
     *
     * @return string|null
     */
    public function getTransactionReference(): ?string
    {
        return $this->data['id'] ?? $this->data['handle'] ?? null;
    }

    /**
     * Get error message
     *
     * @return string|null
     */
    public function getMessage(): ?string
    {
        if (isset($this->data['error'])) {
            return $this->data['error'];
        }

        if (isset($this->data['message'])) {
            return $this->data['message'];
        }

        return null;
    }

    /**
     * Get error code
     *
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->data['code'] ?? null;
    }

    /**
     * Is the response a redirect?
     *
     * @return bool
     */
    public function isRedirect(): bool
    {
        return false;
    }

    /**
     * Get redirect URL
     *
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return null;
    }

    /**
     * Get redirect method
     *
     * @return string
     */
    public function getRedirectMethod(): string
    {
        return 'GET';
    }

    /**
     * Get redirect data
     *
     * @return array<string, mixed>
     */
    public function getRedirectData(): array
    {
        return [];
    }
}

<?php

namespace Codilar\ProductEnquiryREST\Model;

use Codilar\ProductEnquiryREST\Api\Data\ApiResponseInterface;

class ApiResponse implements ApiResponseInterface
{
    private bool $status = false;
    private string $message = '';
    private array $data = [];

    /**
     * @return bool
     */
    public function getStatus(): bool
    {
        return $this->status;
    }

    /**
     * @param bool $status
     * @return mixed
     */
    public function setStatus(bool $status): mixed
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function setMessage(string $message): mixed
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function setData(array $data): mixed
    {
        $this->data = $data;

        return $this;
    }
}

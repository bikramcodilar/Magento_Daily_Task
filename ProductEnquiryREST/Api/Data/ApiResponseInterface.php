<?php

namespace Codilar\ProductEnquiryREST\Api\Data;

interface ApiResponseInterface
{
    /**
     * @return bool
     */
    public function getStatus(): bool;

    /**
     * @param bool $status
     * @return mixed
     */
    public function setStatus(bool $status): mixed;

    /**
     * @return string
     */
    public function getMessage(): string;

    /**
     * @param string $message
     * @return mixed
     */
    public function setMessage(string $message): mixed;

    /**
     * @return array
     */
    public function getData(): array;

    /**
     * @param array $data
     * @return mixed
     */
    public function setData(array $data): mixed;
}

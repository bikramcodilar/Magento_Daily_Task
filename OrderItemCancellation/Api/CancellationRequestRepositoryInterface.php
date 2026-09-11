<?php
declare(strict_types=1);
namespace Codilar\OrderItemCancellation\Api;
use Codilar\OrderItemCancellation\Model\CancellationRequest;

interface CancellationRequestRepositoryInterface
{
    /**
     * @param CancellationRequest $request
     * @return CancellationRequest
     */
    public function save(CancellationRequest $request): CancellationRequest;

    /**
     * @param int $requestId
     * @return CancellationRequest
     */
    public function getById(int $requestId): CancellationRequest;

    /**
     * @param string $requestToken
     * @return CancellationRequest
     */
    public function getByToken(string $requestToken): CancellationRequest;
}

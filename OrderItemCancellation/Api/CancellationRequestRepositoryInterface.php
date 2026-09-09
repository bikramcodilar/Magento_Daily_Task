<?php
declare(strict_types=1);
namespace Codilar\OrderItemCancellation\Api;
use Codilar\OrderItemCancellation\Model\CancellationRequest;

interface CancellationRequestRepositoryInterface
{
    public function save(CancellationRequest $request): CancellationRequest;
    public function getById(int $requestId): CancellationRequest;
    public function getByToken(string $requestToken): CancellationRequest;
}

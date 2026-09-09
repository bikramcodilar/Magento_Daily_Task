<?php
declare(strict_types=1);
namespace Codilar\OrderItemCancellation\Api;
use Codilar\OrderItemCancellation\Model\ItemCancellation;

interface ItemCancellationRepositoryInterface
{
    public function save(ItemCancellation $itemCancellation): ItemCancellation;
}

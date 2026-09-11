<?php
declare(strict_types=1);
namespace Codilar\OrderItemCancellation\Api;
use Codilar\OrderItemCancellation\Model\ItemCancellation;

interface ItemCancellationRepositoryInterface
{
    /**
     * @param ItemCancellation $itemCancellation
     * @return ItemCancellation
     */
    public function save(ItemCancellation $itemCancellation): ItemCancellation;
}

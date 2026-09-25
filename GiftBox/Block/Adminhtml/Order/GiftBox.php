<?php

declare(strict_types=1);

namespace Codilar\GiftBox\Block\Adminhtml\Order;

use Codilar\GiftBox\Model\Order\GiftBoxContentProvider;
use Magento\Backend\Block\Template;
use Magento\Sales\Model\Order\Item;

class GiftBox extends Template
{
    private ?Item $item = null;

    public function __construct(
        Template\Context $context,
        private readonly GiftBoxContentProvider $giftBoxContentProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->setTemplate(
            'Codilar_GiftBox::order/giftbox.phtml'
        );
    }

    public function setItem(Item $item): self
    {
        $this->item = $item;

        return $this;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function getGiftBoxData(): array
    {
        if (!$this->item) {
            return [];
        }

        return $this->giftBoxContentProvider->getData(
            $this->item
        );
    }

    public function getCoffeeNames(): array
    {
        if (!$this->item) {
            return [];
        }

        return $this->giftBoxContentProvider->getCoffeeNames(
            $this->item
        );
    }

    public function getEquipmentName(): ?string
    {
        if (!$this->item) {
            return null;
        }

        return $this->giftBoxContentProvider->getEquipmentName(
            $this->item
        );
    }

    public function getGiftMessage(): ?string
    {
        if (!$this->item) {
            return null;
        }

        return $this->giftBoxContentProvider->getGiftMessage(
            $this->item
        );
    }
}

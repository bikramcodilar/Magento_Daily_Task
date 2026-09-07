<?php

namespace Codilar\OrderItemCancellation\Controller\Cancel;

use Codilar\OrderItemCancellation\Service\PartialCancellationService;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

class Submit extends Action implements HttpPostActionInterface
{
    public function __construct(
        Context $context,
        private readonly CustomerSession $customerSession,
        private readonly PartialCancellationService $partialCancellationService
    ) {
        parent::__construct($context);
    }

    public function execute(): Redirect
    {
        /** @var Redirect $redirect */
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if (!$this->customerSession->isLoggedIn()) {
            return $redirect->setPath('customer/account/login');
        }

        $orderId = (int) $this->getRequest()->getParam('order_id');
        $items = (array) $this->getRequest()->getParam('items', []);
        $requestToken = (string) $this->getRequest()->getParam('request_token');

        try {
            $this->partialCancellationService->cancel(
                $orderId,
                (int) $this->customerSession->getCustomerId(),
                $items,
                $requestToken
            );

            $this->messageManager->addSuccessMessage(
                __('The selected item quantities were cancelled successfully.')
            );
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (\Throwable $exception) {
            $this->messageManager->addErrorMessage(
                __('Unable to cancel the selected quantities. Please try again.')
            );
        }

        return $redirect->setPath('sales/order/view', ['order_id' => $orderId]);
    }
}

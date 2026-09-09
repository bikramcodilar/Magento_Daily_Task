<?php

namespace Codilar\OrderItemCancellation\Controller\Cancel;

use Codilar\OrderItemCancellation\Service\PartialCancellationService;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;

class Submit implements ActionInterface, HttpPostActionInterface
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly RedirectFactory $redirectFactory,
        private readonly CustomerSession $customerSession,
        private readonly PartialCancellationService $partialCancellationService,
        private readonly ManagerInterface $messageManager
    ) {
    }

    public function execute(): Redirect
    {
        $orderId = (int) $this->request->getParam('order_id');
        $redirect = $this->redirectFactory->create();
        if (!$this->customerSession->isLoggedIn()) {
            return $redirect->setPath(
                'customer/account/login'
            );
        }
        $items = (array) $this->request->getParam(
            'items',
            []
        );
        $requestToken = (string) $this->request->getParam(
            'request_token'
        );
        try {
            $this->partialCancellationService->cancel(
                $orderId,
                (int) $this->customerSession->getCustomerId(),
                $items,
                $requestToken
            );
            $this->messageManager->addSuccessMessage(
                __(
                    'The selected item quantities were cancelled successfully.'
                )
            );
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage(
                $exception->getMessage()
            );
        } catch (\Throwable $exception) {
            $this->messageManager->addErrorMessage(
                __(
                    'Unable to cancel the selected quantities. Please try again.'
                )
            );
        }
        return $redirect->setPath(
            'sales/order/view',
            ['order_id' => $orderId]
        );
    }
}

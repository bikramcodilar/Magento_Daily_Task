<?php

namespace Codilar\ProductEnquiry\Ui\DataProvider\Enquiry\Form;

use Codilar\ProductEnquiry\Model\ResourceModel\ProductEnquiry\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
        $this->collection = $collectionFactory->create();
        $enquiryId = $request->getParam($requestFieldName);
        if ($enquiryId) {
            $this->collection->addFieldToFilter(
                'enquiry_id',
                $enquiryId
            );
        }
    }

    public function getData(): array
    {
        $data = [];
        foreach ($this->collection->getItems() as $enquiry) {
            $data[$enquiry->getId()] = $enquiry->getData();
        }
        return $data;
    }
}

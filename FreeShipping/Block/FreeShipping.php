<?php
namespace Codilar\FreeShipping\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;

class FreeShipping extends Template
{
    private const string XML_PATH_THRESHOLD = 'codilar_freeshipping/general/threshold';

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return float
     */
    public function getThreshold(): float
    {
        return (float) $this->scopeConfig->getValue(
            self::XML_PATH_THRESHOLD
        );
    }
}

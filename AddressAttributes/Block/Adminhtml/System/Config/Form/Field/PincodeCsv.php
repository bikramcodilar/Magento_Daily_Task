<?php

namespace Codilar\AddressAttributes\Block\Adminhtml\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Escaper;
use Magento\Framework\Filesystem;
use Magento\Store\Model\ScopeInterface;

class PincodeCsv extends Field
{
    private const string XML_PATH_FILE = 'addressattributes/pincode/csv_file';
    private const string PINCODE_DIRECTORY = 'addressattributes/pincodes/';

    public function __construct(
        Context $context,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly Filesystem $filesystem,
        private readonly Escaper $escaper,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(AbstractElement $element): string
    {
        $configuredFile = (string)$this->scopeConfig->getValue(
            self::XML_PATH_FILE,
            ScopeInterface::SCOPE_STORE
        );
        if ($configuredFile === '') {
            return '<span>No pincode CSV file is configured.</span>';
        }
        $configuredFile = ltrim(str_replace('\\', '/', $configuredFile), '/');
        if (str_starts_with($configuredFile, self::PINCODE_DIRECTORY)) {
            $relativePath = $configuredFile;
        } else {
            $relativePath = self::PINCODE_DIRECTORY . basename($configuredFile);
        }
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        if (!$mediaDirectory->isFile($relativePath)) {
            return '<span>The configured pincode CSV file could not be found.</span>';
        }
        $handle = fopen($mediaDirectory->getAbsolutePath($relativePath), 'r');
        if ($handle === false) {
            return '<span>The configured pincode CSV file could not be read.</span>';
        }
        $pincodes = [];
        $firstRow = true;
        try {
            while (($row = fgetcsv($handle, 0, ',', '"', '')) !== false) {
                if (!isset($row[0])) {
                    continue;
                }
                $pincode = trim((string)$row[0]);
                if (
                    $firstRow
                    && in_array(strtolower($pincode), ['pincode', 'postcode', 'postal_code'], true)
                ) {
                    $firstRow = false;
                    continue;
                }
                $firstRow = false;
                if ($pincode !== '') {
                    $pincodes[] = $pincode;
                }
            }
        } finally {
            fclose($handle);
        }
        if ($pincodes === []) {
            return '<span>The CSV file contains no pincodes.</span>';
        }
        $escapedPincodes = array_map(
            fn (string $pincode): string => $this->escaper->escapeHtml($pincode),
            $pincodes
        );
        return '<div style="max-height: 250px; overflow-y: auto; padding: 8px; border: 1px solid #ccc;">'
            . implode('<br>', $escapedPincodes)
            . '</div>';
    }
}

<?php

namespace Codilar\AddressAttributes\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Store\Model\ScopeInterface;

class PincodeValidator
{
    private const string XML_PATH_ENABLED = 'addressattributes/pincode/enabled';
    private const string XML_PATH_FILE = 'addressattributes/pincode/csv_file';
    private const string PINCODE_DIRECTORY = 'addressattributes/pincodes/';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly Filesystem $filesystem
    ) {
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param string $postcode
     * @param int|null $storeId
     * @return bool
     * @throws LocalizedException
     */
    public function isAllowed(string $postcode, ?int $storeId = null): bool
    {
        $postcode = trim($postcode);
        if ($postcode === '') {
            return false;
        }
        $configuredFile = (string)$this->scopeConfig->getValue(
            self::XML_PATH_FILE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        if ($configuredFile === '') {
            return true;
        }
        $configuredFile = ltrim(str_replace('\\', '/', $configuredFile), '/');
        if (str_starts_with($configuredFile, self::PINCODE_DIRECTORY)) {
            $relativePath = $configuredFile;
        } else {
            $relativePath = self::PINCODE_DIRECTORY . basename($configuredFile);
        }
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        if (!$mediaDirectory->isFile($relativePath)) {
            throw new LocalizedException(
                __('The configured pincode CSV file could not be found.')
            );
        }
        $absolutePath = $mediaDirectory->getAbsolutePath($relativePath);
        $handle = fopen($absolutePath, 'r');
        if ($handle === false) {
            throw new LocalizedException(
                __('The configured pincode CSV file could not be read.')
            );
        }
        try {
            $firstRow = true;
            while (($row = fgetcsv($handle, 0, ',', '"', '')) !== false) {
                if (!isset($row[0])) {
                    continue;
                }
                $value = trim((string)$row[0]);
                if ($firstRow && in_array(strtolower($value), ['pincode', 'postcode', 'postal_code'], true)) {
                    $firstRow = false;
                    continue;
                }
                $firstRow = false;
                if ($value !== '' && hash_equals($value, $postcode)) {
                    return true;
                }
            }
        } finally {
            fclose($handle);
        }
        return false;
    }
}

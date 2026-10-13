<?php

namespace Codilar\AddressAttributes\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\File;
use Magento\Framework\Exception\FileSystemException;
class PincodeCsv extends File
{
    /**
     * @return string[]
     */
    protected function _getAllowedExtensions(): array
    {
        return ['csv'];
    }

    /**
     * @return string
     * @throws FileSystemException
     */
    protected function _getUploadDir(): string
    {
        return $this->_filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('addressattributes/pincodes');
    }

    protected function _addWhetherScopeInfo(): bool
    {
        return true;
    }
}

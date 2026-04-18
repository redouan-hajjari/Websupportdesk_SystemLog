<?php

declare(strict_types=1);

namespace WebSupportDesk\SystemLogs\Controller\Adminhtml\Logs;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use WebSupportDesk\SystemLogs\Model\LogDirectory;
use WebSupportDesk\SystemLogs\Model\LogReader;

class Content extends Action
{
    public const ADMIN_RESOURCE = 'WebSupportDesk_SystemLogs::systemlogs';

    public function __construct(
        Context $context,
        private readonly JsonFactory $jsonFactory,
        private readonly LogDirectory $logDirectory,
        private readonly LogReader $logReader
    ) {
        parent::__construct($context);
    }

    public function execute(): Json
    {
        $file = (string)$this->getRequest()->getParam('file', '');

        if ($this->logDirectory->resolveValidatedPath($file) === null) {
            return $this->jsonFactory->create()->setData([
                'success' => false,
                'message' => (string)__('Choose a valid log file from var/log.'),
                'content' => '',
                'size' => 0,
            ]);
        }

        $content = $this->logReader->readFull($file);
        $size = $this->logReader->getFileSize($file);

        return $this->jsonFactory->create()->setData([
            'success' => true,
            'content' => $content,
            'size' => $size,
            'full_file' => true,
        ]);
    }
}

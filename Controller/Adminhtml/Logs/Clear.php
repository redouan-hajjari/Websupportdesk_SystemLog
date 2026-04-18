<?php

declare(strict_types=1);

namespace WebSupportDesk\SystemLogs\Controller\Adminhtml\Logs;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use WebSupportDesk\SystemLogs\Model\LogDirectory;
use WebSupportDesk\SystemLogs\Model\LogFileRegistry;

class Clear extends Action
{
    public const ADMIN_RESOURCE = 'WebSupportDesk_SystemLogs::systemlogs';

    public function __construct(
        Context $context,
        private readonly JsonFactory $jsonFactory,
        private readonly FormKeyValidator $formKeyValidator,
        private readonly LogDirectory $logDirectory,
        private readonly LogFileRegistry $logFileRegistry
    ) {
        parent::__construct($context);
    }

    public function execute(): Json
    {
        if (!$this->getRequest()->isPost()) {
            return $this->jsonFactory->create()->setData([
                'success' => false,
                'message' => (string)__('Use POST.'),
            ]);
        }

        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->jsonFactory->create()->setData([
                'success' => false,
                'message' => (string)__('Invalid form key.'),
            ]);
        }

        $file = (string)$this->getRequest()->getParam('file', '');

        try {
            $this->logDirectory->clear($file);
            $this->logFileRegistry->markCleared($file);
            $this->logFileRegistry->syncAndDescribe();
        } catch (\InvalidArgumentException $e) {
            return $this->jsonFactory->create()->setData([
                'success' => false,
                'message' => (string)__('Could not clear this log file.'),
            ]);
        } catch (\Throwable $e) {
            return $this->jsonFactory->create()->setData([
                'success' => false,
                'message' => (string)__('Error: %1', $e->getMessage()),
            ]);
        }

        return $this->jsonFactory->create()->setData([
            'success' => true,
            'message' => (string)__('Log file emptied.'),
        ]);
    }
}

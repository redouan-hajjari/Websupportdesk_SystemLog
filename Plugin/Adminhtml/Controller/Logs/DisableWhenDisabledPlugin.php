<?php

declare(strict_types=1);

namespace WebSupportDesk\SystemLogs\Plugin\Adminhtml\Controller\Logs;

use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use WebSupportDesk\SystemLogs\Controller\Adminhtml\Logs\Clear;
use WebSupportDesk\SystemLogs\Controller\Adminhtml\Logs\Content;
use WebSupportDesk\SystemLogs\Controller\Adminhtml\Logs\Index;
use WebSupportDesk\SystemLogs\Model\Config;

class DisableWhenDisabledPlugin
{
    public function __construct(
        private readonly Config $config,
        private readonly RedirectFactory $redirectFactory,
        private readonly JsonFactory $jsonFactory,
        private readonly MessageManagerInterface $messageManager
    ) {
    }

    /**
     * @param Index|Content|Clear $subject
     */
    public function aroundExecute($subject, callable $proceed)
    {
        if ($this->config->isEnabled()) {
            return $proceed();
        }

        if ($subject instanceof Index) {
            $this->messageManager->addErrorMessage(
                (string)__('System Logs is disabled in configuration (Stores → Configuration → WebSupportDesk → System Logs).')
            );

            return $this->redirectFactory->create()->setPath(
                'adminhtml/system_config/edit',
                ['section' => 'websupportdesk_systemlogs']
            );
        }

        return $this->jsonFactory->create()->setData([
            'success' => false,
            'message' => (string)__('System Logs is disabled.'),
        ]);
    }
}

<?php

declare(strict_types=1);

namespace WebSupportDesk\SystemLogs\Controller\Adminhtml\Logs;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    public const ADMIN_RESOURCE = 'WebSupportDesk_SystemLogs::systemlogs';

    public function __construct(
        Context $context,
        private readonly PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
    }

    public function execute(): Page
    {
        $page = $this->resultPageFactory->create();
        $page->setActiveMenu('WebSupportDesk_SystemLogs::logs');
        $page->getConfig()->getTitle()->prepend(__('WebSupportDesk'));
        $page->getConfig()->getTitle()->prepend(__('System Logs'));

        return $page;
    }
}

<?php

declare(strict_types=1);

namespace WebSupportDesk\SystemLogs\Block\Adminhtml\Logs;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use WebSupportDesk\SystemLogs\Model\LogFileRegistry;

class Index extends Template
{
    /** @var list<array{name: string, first_seen: string, last_seen: string, last_cleared_at: ?string}>|null */
    private ?array $filesCache = null;

    public function __construct(
        Context $context,
        private readonly LogFileRegistry $logFileRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return list<array{name: string, first_seen: string, last_seen: string, last_cleared_at: ?string}>
     */
    public function getFiles(): array
    {
        if ($this->filesCache === null) {
            $this->filesCache = $this->logFileRegistry->syncAndDescribe();
        }

        return $this->filesCache;
    }

    public function getFormKeyValue(): string
    {
        return $this->formKey->getFormKey();
    }

    public function getContentUrl(): string
    {
        return $this->getUrl('wsd_systemlogs/logs/content');
    }

    public function getClearUrl(): string
    {
        return $this->getUrl('wsd_systemlogs/logs/clear');
    }
}

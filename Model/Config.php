<?php

declare(strict_types=1);

namespace WebSupportDesk\SystemLogs\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    public const XML_PATH_ENABLED = 'websupportdesk_systemlogs/general/enabled';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * Global switch (default scope only — see system.xml).
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            null
        );
    }
}

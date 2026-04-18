<?php

declare(strict_types=1);

namespace WebSupportDesk\SystemLogs\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Grants WebSupportDesk_SystemLogs::config (Stores → Configuration) to the same roles
 * that already have WebSupportDesk_SystemLogs::systemlogs or peer WebSupportDesk resources.
 */
class GrantSystemLogsConfigAcl implements DataPatchInterface
{
    private const RESOURCE_ID = 'WebSupportDesk_SystemLogs::config';

    private const PEER_RESOURCES = [
        'WebSupportDesk_SystemLogs::systemlogs',
        'WebSupportDesk_MailTracing::mailtracing',
        'WebSupportDesk_MailTracing::mailtracing_log',
        'WebSupportDesk_OrderWhatsapp::config',
        'WebSupportDesk_OneStepCheckout::config',
        'WebSupportDesk_PopUpSeatch::config',
        'WebSupportDesk_PostCodeApi::config',
    ];

    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup
    ) {
    }

    public function apply(): self
    {
        $setup = $this->moduleDataSetup;
        $setup->startSetup();

        $connection = $setup->getConnection();
        $ruleTable = $setup->getTable('authorization_rule');
        $roleTable = $setup->getTable('authorization_role');

        $roleIds = $this->collectRoleIdsToGrant($connection, $ruleTable, $roleTable);

        foreach ($roleIds as $roleId) {
            if ($this->ruleExists($connection, $ruleTable, (int)$roleId)) {
                continue;
            }
            $connection->insert($ruleTable, [
                'role_id' => (int)$roleId,
                'resource_id' => self::RESOURCE_ID,
                'privileges' => null,
                'permission' => 'allow',
            ]);
        }

        $setup->endSetup();

        return $this;
    }

    /**
     * @return list<int>
     */
    private function collectRoleIdsToGrant(
        \Magento\Framework\DB\Adapter\AdapterInterface $connection,
        string $ruleTable,
        string $roleTable
    ): array {
        $ids = [];

        $adminIds = $connection->fetchCol(
            $connection->select()
                ->from($roleTable, 'role_id')
                ->where('role_name = ?', 'Administrators')
        );
        foreach ($adminIds as $id) {
            $ids[] = (int)$id;
        }

        foreach (self::PEER_RESOURCES as $resourceId) {
            $peerRoleIds = $connection->fetchCol(
                $connection->select()
                    ->from($ruleTable, 'role_id')
                    ->where('resource_id = ?', $resourceId)
                    ->where('permission = ?', 'allow')
            );
            foreach ($peerRoleIds as $id) {
                $ids[] = (int)$id;
            }
        }

        $ids = array_values(array_unique(array_filter($ids)));

        return $ids;
    }

    private function ruleExists(
        \Magento\Framework\DB\Adapter\AdapterInterface $connection,
        string $ruleTable,
        int $roleId
    ): bool {
        $select = $connection->select()
            ->from($ruleTable, 'rule_id')
            ->where('role_id = ?', $roleId)
            ->where('resource_id = ?', self::RESOURCE_ID)
            ->limit(1);

        return (bool)$connection->fetchOne($select);
    }

    public static function getDependencies(): array
    {
        return [
            GrantSystemLogsAcl::class,
        ];
    }

    public function getAliases(): array
    {
        return [];
    }
}

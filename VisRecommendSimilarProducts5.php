<?php

namespace VisRecommendSimilarProducts5;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;

class VisRecommendSimilarProducts5 extends Plugin
{
    public function install(InstallContext $context)
    {
        $this->createRole();
        $this->createResourcesAndPrivileges();

    }


    private function createRole()
    {
        $role = $this->getRole();

        if (empty($role)) {
            $db = Shopware()->Db();

            $db->insert(
                's_core_auth_roles',
                [
                    'name' => 'VisualSearch',
                    'description' => 'Berechtigung Produkte zu bearbeiten / Permission to edit products',
                    'source' => 'custom',
                    'enabled' => true,
                    'admin' => false
                ]
            );
        }

    }

    private function getRole()
    {
        $db = Shopware()->Db();

        $sql = '
                SELECT * FROM s_core_auth_roles
                WHERE `name` = ?
                ';

        $role = $db->fetchRow(
            $sql,
            [
                'VisualSearch'
            ]
        );

        return $role;
    }

    private function createResourcesAndPrivileges()
    {
        $roleId = $this->getRole();
        $roleId = $roleId['id'];

        $resourceId = $this->getResourceByName('article');
//        $privileges = $this->getPrivilegesById($resourceId);

        $db = Shopware()->Db();

        $db->insert(
            's_core_acl_roles',
            [
                'roleID' => $roleId,
                'resourceID' => $resourceId,
            ]
        );

    }

    private function getResourceByName(string  $name)
    {
        $db = Shopware()->Db();

        $sql = '
                SELECT * FROM s_core_acl_resources
                WHERE `name` = ?
                ';

        $resources = $db->fetchRow(
            $sql,
            [
                $name
            ]
        );

        return $resources['id'];
    }

    private function getPrivilegesById(int  $resourceId)
    {
        $db = Shopware()->Db();

        $sql = '
                SELECT * FROM s_core_acl_privileges
                WHERE `resourceID` = ?
                ';

        $privileges = $db->fetchAll(
            $sql,
            [
                $resourceId
            ]
        );

        return $privileges;
    }
}

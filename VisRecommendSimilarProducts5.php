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
        $keys = $this->createUser();

        $hosts = $this->getHosts();
        $this->notification($hosts, $keys, 'shopware5;install');
    }

    public function uninstall(InstallContext $context)
    {
        $this->deleteUser();
        $this->deleteRole();

        $hosts = $this->getHosts();
        $this->notification($hosts, '', 'shopware5;uninstall');
    }

    private function createResourcesAndPrivileges()
    {
        $roleId = $this->getRole();
        $roleId = $roleId['id'];

        $resourceId = $this->getResourceByName('article');

        $db = Shopware()->Db();

        $db->insert(
            's_core_acl_roles',
            [
                'roleID' => $roleId,
                'resourceID' => $resourceId,
            ]
        );
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

    private function createUser()
    {
        $user = $this->getUser();

        $roleId = $this->getRole();
        $roleId = $roleId['id'];

        $password = bin2hex(openssl_random_pseudo_bytes(20));
        $key = bin2hex(openssl_random_pseudo_bytes(20));

        if (empty($user)) {
            $db = Shopware()->Db();

            $db->insert(
                's_core_auth',
                [
                    'roleID' => $roleId,
                    'username' => 'VisualSearch',
                    'password' => $password,
                    'active' => true,
                    'apiKey' => $key,
                    'name' => 'VisualSearch',
                    'email' => 'office@visualsearch.at'
                ]
            );
        }

        return $password.';'.$key;
    }

    private function deleteRole()
    {
        $db = Shopware()->Db();

        $roleId = $this->getRole();
        $roleId = $roleId['id'];

        $sql = '
                DELETE FROM s_core_auth_roles
                WHERE `id` = ?
                ';

        $db->fetchRow(
            $sql,
            [
                $roleId
            ]
        );

        $sql = '
                DELETE FROM s_core_acl_roles
                WHERE `roleID` = ?
                ';

        $db->fetchRow(
            $sql,
            [
                $roleId
            ]
        );
    }

    private function deleteUser()
    {
        $db = Shopware()->Db();

        $roleId = $this->getRole();
        $roleId = $roleId['id'];

        $sql = '
                DELETE FROM s_core_auth
                WHERE `username` = "VisualSearch" and `roleID` = ?
                ';

        $db->fetchRow(
            $sql,
            [
                $roleId
            ]
        );
    }

    private function getHosts()
    {
        /** @var Connection $connection */
        $connection = $this->container->get('dbal_connection');

        $shops = $connection->createQueryBuilder()
            ->select('*')
            ->from('s_core_shops', 'shops')
            ->execute()
            ->fetchAll();

        $systemHosts = [];

        foreach($shops as $shop) {
            $secure = 'https://';
            if($shop['secure'] == 0) {
                $secure = 'http://';
            }
            array_push($systemHosts, $secure . $shop['host'] . $shop['base_path']);
        }
        return implode(";",$systemHosts);
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

    private function getUser()
    {
        $db = Shopware()->Db();

        $sql = '
                SELECT * FROM s_core_auth
                WHERE `name` = ?
                ';

        $user = $db->fetchRow(
            $sql,
            [
                'VisualSearch'
            ]
        );

        return $user;
    }

    private function notification($hosts, $keys, $type)
    {
        $url = 'https://api.visualsearch.wien/installation_notify';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Vis-API-KEY: marketing',
            'Vis-SYSTEM-HOSTS:' . $hosts,
            'Vis-SYSTEM-KEY:' . $keys,
            'Vis-SYSTEM-TYPE: VisRecommendSimilarProducts;' . $type,
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }

}

<?php

use Doctrine\DBAL\Connection;
use Shopware\Components\Api\Resource\Article;

class Shopware_Controllers_Api_SimilarDeleteCross extends \Shopware_Controllers_Api_Rest
{
    /**
     * @throws Exception
     */
    public function indexAction()
    {
        $db = Shopware()->Db();

        $sql = 'DELETE FROM s_articles_similar';

        $db->fetchRow($sql);

        $this->View()->assign(['code' => 200, 'message' => 'success']);
        return $this->View();
    }
}

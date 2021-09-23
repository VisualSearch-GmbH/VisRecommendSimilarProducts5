<?php

use Doctrine\DBAL\Connection;
use Shopware\Bundle\MediaBundle\MediaService;
use Shopware\Components\Api\Resource\Article;

class Shopware_Controllers_Api_SimilarStatus extends \Shopware_Controllers_Api_Rest
{
    /**
     * @throws Exception
     */
    public function indexAction()
    {
        $productsIds = $this->getProductsIds();
        $sp = sizeof($productsIds);

        /** @var Article $productApiService */
        $productApiService = $this->container->get('shopware.api.article');

        /** @var MediaService $mediaService */
        $mediaService = $this->container->get('shopware_media.media_service');

        $productsWithoutCrossSelling = [];

        foreach($productsIds as $productId) {
            $isSimilarProduct = $this->isSimilar($productId['id']);
            if(count($isSimilarProduct) == 0) {
                $productTemp = $productApiService->getOne($productId['id']);

                $categories = [];
                foreach ($productTemp['categories'] as $category) {
                    array_push($categories, $category['id']);
                }
                $categories = implode("-", $categories);

                $images = [];
                foreach ($productTemp['images'] as $image) {
                    $mediaPath = $this->getPath($image['mediaId']);
                    array_push($images, $mediaService->getUrl($mediaPath[0]['path']));
                }

                array_push($productsWithoutCrossSelling, [$productTemp['id'], $productTemp['name'], $categories, '', array_values($images)[0]]);
            }
        }

        if (count($productsWithoutCrossSelling) == 0) {
            $this->View()->assign(['code' => 200, 'message' => 'Info VisRecommendSimilarProducts: size catalogue:'.$sp.';all products have cross-sellings']);
            return $this->View();
        }
        else {
            $this->View()->assign(['code' => 200, 'message' => 'Info VisRecommendSimilarProducts: size catalogue:'.$sp.';update of cross-sellings is needed']);
            return $this->View();
        }
    }

    private function getProductsIds()
    {
        /** @var Connection $connection */
        $connection = $this->container->get('dbal_connection');

        return $connection->createQueryBuilder()
            ->select('id')
            ->from('s_articles', 'articles')
            ->execute()
            ->fetchAll();
    }

    private function isSimilar($articleId)
    {
        /** @var Connection $connection */
        $connection = $this->container->get('dbal_connection');

        return $connection->createQueryBuilder()
            ->select('*')
            ->from('s_articles_similar', 'similarArticles')
            ->where('similarArticles.articleID = :articleID')
            ->setParameter('articleID',  $articleId)
            ->execute()
            ->fetchAll();
    }

    private function getPath($mediaId)
    {
        /** @var Connection $connection */
        $connection = $this->container->get('dbal_connection');

        return $connection->createQueryBuilder()
            ->select('path')
            ->from('s_media', 'media')
            ->where('media.id = :mediaId')
            ->setParameter('mediaId',  $mediaId)
            ->execute()
            ->fetchAll();

    }

}
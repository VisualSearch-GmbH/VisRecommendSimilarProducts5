<?php

use Doctrine\DBAL\Connection;
use Shopware\Bundle\MediaBundle\MediaService;
use Shopware\Components\Api\Resource\Article;

class Shopware_Controllers_Api_SimilarUpdateAuto extends \Shopware_Controllers_Api_Rest
{
    /**
     * @throws Exception
     */
    public function indexAction()
    {
        $config = $this->container->get('shopware.plugin.cached_config_reader')->getByPluginName('VisRecommendSimilarProducts5');
        $autoUpdate = $config['autoUpdate'];

        if(!$autoUpdate) {
            $this->View()->assign(['code' => 200, 'message' => 'Info VisRecommendSimilarProducts: automatic updates not enabled']);
            return $this->View();
        }

        //if(!$config['enabled']){
        //    $this->View()->assign(['code' => 200, 'message' => 'Info VisRecommendSimilarProducts: automatic updates not enabled']);
        //    return $this->View();
        //}

        $productsIds = $this->getProductsIds();

        if (count($productsIds) == 0) {
            $this->View()->assign(['code' => 200, 'message' => 'Info VisRecommendSimilarProducts: no products']);
            return $this->View();
        }

        /** @var Article $productApiService */
        $productApiService = $this->container->get('shopware.api.article');

        /** @var MediaService $mediaService */
        $mediaService = $this->container->get('shopware_media.media_service');

        /** find out if all products have cross sellings **/
        $firstCategory = '';
        foreach ($productsIds as $productId) {

            $productTemp = $productApiService->getOne($productId['id']);

            // skip not active products
            if ($productTemp['active'] != 1) {
                continue;
            }

            $isSimilarProduct = $this->isSimilar($productId['id']);
            if (count($isSimilarProduct) == 0) {

                $categories = [];
                foreach ($productTemp['categories'] as $category) {
                    array_push($categories, strval($category['id']));
                }
                $firstCategory = implode("-", $categories);
                break;
            }
        }

        /** all products have cross sellings **/
        if (empty($firstCategory)) {
            $this->View()->assign(['code' => 200, 'message' => 'Info VisRecommendSimilarProducts: all products have cross-sellings']);
            return $this->View();
        }

        $allProducts = [];

        /** update all products **/
        foreach ($productsIds as $productId) {
            $productTemp = $productApiService->getOne($productId['id']);

            // skip not active products
            if ($productTemp['active'] != 1) {
                continue;
            }

            $categories = [];
            foreach ($productTemp['categories'] as $category) {
                array_push($categories, strval($category['id']));
            }
            $catName = implode("-", $categories);

            $images = [];
            foreach ($productTemp['images'] as $image) {
                $mediaPath = $this->getPath($image['mediaId']);
                array_push($images, $mediaService->getUrl($mediaPath[0]['path']));
            }

            if (sizeof($productsIds) > 30000) {
                if (strcmp($catName,$firstCategory) == 0) {
                    array_push($allProducts, [$productTemp['id'], $productTemp['name'], $categories, '', array_values($images)[0]]);
                }
            } else {
                array_push($allProducts, [$productTemp['id'], $productTemp['name'], $categories, '', array_values($images)[0]]);
            }
        }

        $apiKey = $config['apiKey'];
        $systemHosts = $this->getHosts();

        $message = $this->updateProducts($apiKey, $allProducts, $systemHosts);

        $this->View()->assign(['code' => 200, 'message' => 'Info VisRecommendSimilarProducts: ' . $message]);
        return $this->View();
    }

    public function updateProducts($apiKey, $products, $systemHosts)
    {
        // Form data for the API request
        $data = ["products" => $products];

        // Create a connection
        $url = 'https://api.visualsearch.wien/similar_compute';
        $ch = curl_init($url);

        // Form data string
        $postString = json_encode($data);
        // $postString = http_build_query($data);

        // Setting our options
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json',
            'Vis-API-KEY:'. $apiKey,
            'Vis-SYSTEM-HOSTS:'. $systemHosts,
            'Vis-SYSTEM-TYPE:shopware5'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        try {
            // Get the response
            $response = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($response);

            return $response->{'message'};
        } catch (Exception $e) {
            return $e->getMessage();
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
            ->setParameter('articleID', $articleId)
            ->execute()
            ->fetchAll();
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

        foreach ($shops as $shop) {
            $secure = 'https://';
            if ($shop['secure'] == 0) {
                $secure = 'http://';
            }
            array_push($systemHosts, $secure . $shop['host'] . $shop['base_path']);
        }
        return implode(";", $systemHosts);
    }

    private function getPath($mediaId)
    {
        /** @var Connection $connection */
        $connection = $this->container->get('dbal_connection');

        return $connection->createQueryBuilder()
            ->select('path')
            ->from('s_media', 'media')
            ->where('media.id = :mediaId')
            ->setParameter('mediaId', $mediaId)
            ->execute()
            ->fetchAll();
    }
}

<?php

use Doctrine\DBAL\Connection;
use Shopware\Components\Api\Resource\Article;

class Shopware_Controllers_Api_SimilarApiKeyVerify extends \Shopware_Controllers_Api_Rest
{
    /**
     * @throws Exception
     */
    public function indexAction()
    {
        $config = $this->container->get('shopware.plugin.cached_config_reader')->getByPluginName('VisRecommendSimilarProducts5');
        $apiKey = $config['apiKey'];

        // Create a connection
        $url = 'https://api.visualsearch.wien/api_key_verify_similar';
        $ch = curl_init($url);

        // Setting our options
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json',
            'Vis-API-KEY:'.$apiKey));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        try{
            // Get the response
            $response = curl_exec($ch);
            curl_close($ch);
            $msg = json_decode($response);
            $this->View()->assign(['code' => $msg->{'code'}, 'message' => $msg->{'message'}]);
        }catch(Exception $e){
            $msg = $e->getMessage();
            $this->View()->assign(['code' => 500, 'message' => $msg]);
        }
        return $this->View();
    }
}
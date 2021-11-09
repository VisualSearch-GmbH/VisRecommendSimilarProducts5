<?php

namespace VisRecommendSimilarProducts5\Controller\Backend;

use Monolog\Logger;
use Shopware\Components\HttpClient\HttpClientInterface;
use Shopware\Components\HttpClient\RequestException;
use Symfony\Component\HttpFoundation\Response;

class RecommendationsController extends \Shopware_Controllers_Backend_ExtJs
{
    /**
     * @var HttpClientInterface
     */
    private $client;

    /**
     * @var Logger
     */
    private $logger;


    public function __construct(HttpClientInterface $client, Logger $logger)
    {
        $this->client = $client;
        $this->logger = $logger;

        parent::__construct();
    }

    public function apiKeyVerifyAction()
    {
        $config = $this->container->get('shopware.plugin.cached_config_reader')->getByPluginName('VisRecommendSimilarProducts5');
        $apiKey = $config['apiKey'];

        // Create a connection
        $url = 'https://api.visualsearch.wien/api_key_verify_similar';
        $ch = curl_init($url);

        // Setting our options
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json',
            'Vis-API-KEY:' . $apiKey]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        try {
            // Get the response
            $response = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($response, true);

            if ($response['message'] == "API key ok") {
                $this->View()->assign('response', 'Connection was successfully established.');
                $this->View()->assign('success', true);
                return;
            }

            $this->response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->View()->assign('response', 'Connection could not be established. Please check your API credentials.');
        } catch (RequestException $exception) {
            $this->response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->View()->assign('response', $exception->getMessage());
        }
    }
}

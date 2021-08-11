<?php

namespace VisRecommendSimilarProducts5\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ControllerPath implements SubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $pluginDirectory;

    /**
     * @param ContainerInterface $container
     * @param string             $pluginDirectory
     */
    public function __construct(ContainerInterface $container, $pluginDirectory)
    {
        $this->container = $container;
        $this->pluginDirectory = $pluginDirectory;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            'Enlight_Controller_Dispatcher_ControllerPath_Api_RecommendationsApiKeyVerify' => 'onGetControllerPathApiApiKeyVerify',
            'Enlight_Controller_Dispatcher_ControllerPath_Api_RecommendationsUpdateAuto' => 'onGetControllerPathApiUpdateAuto',
        );
    }

    public function onGetControllerPathApiApiKeyVerify(Enlight_Event_EventArgs $args)
    {
        return __DIR__ . '/../Controller/Api/RecommendationsApiKeyVerify.php';
    }

    public function onGetControllerPathApiUpdateAuto(Enlight_Event_EventArgs $args)
    {
        return __DIR__ . '/../Controller/Api/RecommendationsUpdateAuto.php';
    }
}
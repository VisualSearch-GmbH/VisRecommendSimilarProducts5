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
            'Enlight_Controller_Dispatcher_ControllerPath_Api_SimilarApiKeyVerify' => 'onGetControllerPathApiKeyVerify',
            'Enlight_Controller_Dispatcher_ControllerPath_Api_SimilarDeleteCross' => 'onGetControllerPathDeleteCross',
            'Enlight_Controller_Dispatcher_ControllerPath_Api_SimilarStatus' => 'onGetControllerPathStatus',
            'Enlight_Controller_Dispatcher_ControllerPath_Api_SimilarUpdateAuto' => 'onGetControllerPathUpdateAuto',
        );
    }

    public function onGetControllerPathApiKeyVerify(Enlight_Event_EventArgs $args)
    {
        return __DIR__ . '/../Controller/Api/SimilarApiKeyVerify.php';
    }

    public function onGetControllerPathDeleteCross(Enlight_Event_EventArgs $args)
    {
        return __DIR__ . '/../Controller/Api/SimilarDeleteCross.php';
    }

    public function onGetControllerPathStatus(Enlight_Event_EventArgs $args)
    {
        return __DIR__ . '/../Controller/Api/SimilarStatus.php';
    }

    public function onGetControllerPathUpdateAuto(Enlight_Event_EventArgs $args)
    {
        return __DIR__ . '/../Controller/Api/SimilarUpdateAuto.php';
    }
}

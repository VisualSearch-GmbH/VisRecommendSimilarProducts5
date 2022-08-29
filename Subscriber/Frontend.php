<?php

namespace VisRecommendSimilarProducts5\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Frontend implements SubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $pluginName;

    /**
     * @var string
     */
    private $viewDir;

    /**
     * @param ContainerInterface $container
     * @param string             $pluginName
     * @param string             $viewDir
     */
    public function __construct(ContainerInterface $container, $pluginName, $viewDir)
    {
        $this->container = $container;
        $this->pluginName = $pluginName;
        $this->viewDir = $viewDir;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            'Theme_Inheritance_Template_Directories_Collected' => ['onTemplateDirectoriesCollect', 0],
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Detail' => 'onPostDispatchFrontendDetail',
        ];
    }

    /**
     * @param Enlight_Event_EventArgs $args
     */
    public function onTemplateDirectoriesCollect(Enlight_Event_EventArgs $args)
    {
        $dirs = $args->getReturn();

        $dirs[] = $this->viewDir;

        $args->setReturn($dirs);
    }

    /**
     * @param Enlight_Event_EventArgs $args
     */
    public function onPostDispatchFrontendDetail(Enlight_Event_EventArgs $args)
    {
        $controller = $args->getSubject();

        $shop = $this->container->get('shop');
        $config = $this->container->get('shopware.plugin.config_reader')->getByPluginName($this->pluginName, $shop);
        $controller->View()->assign('visChangePosition', $config['sliderPosition']);
    }
}

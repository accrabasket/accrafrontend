<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

class Module
{
    public function onBootstrap(MvcEvent $e) {
        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->attach(MvcEvent::EVENT_DISPATCH, array(
            $this,
            'boforeDispatch'
                ), 100);
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }   
     
    function boforeDispatch(MvcEvent $event) {
        include 'config/constant.php';
        $session = new Container('User');            
        if ($session->offsetExists('user')) {
            $GLOBALS['user'] = $session->user;
        }    
        if ($session->offsetExists('city_list')) {
            $GLOBALS['city_list'] = $session->city_list;
        }        
        if ($session->offsetExists('category_list')) {
            $GLOBALS['category_list'] = $session->category_list;
        }        
        if ($session->offsetExists('marchant_list')) {
            $GLOBALS['marchant_list'] = $session->marchant_list;
        }               
        if ($session->offsetExists('banner')) {
            $GLOBALS['banner'] = $session->banner;
        }         
    }    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}

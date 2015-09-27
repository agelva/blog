<?php

namespace Kitsune\Plugins;

use Phalcon\Dispatcher;
use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;
use Phalcon\Mvc\Dispatcher\Exception as DispatcherException;

/**
 * NotFoundPlugin
 *
 * Handles not-found controller/actions
 */
class NotFoundPlugin extends Plugin
{

    /**
     * This action is executed before execute any action in the application
     *
     * @param Event         $event
     * @param MvcDispatcher $dispatcher
     * @param \Exception    $exception
     * @return bool
     */
    public function beforeException(Event $event, MvcDispatcher $dispatcher, \Exception $exception)
    {
        if ($exception instanceof DispatcherException) {
            switch ($exception->getCode()) {
                case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                    $dispatcher->forward(
                        [
                            'controller' => 'posts',
                            'action'     => 'pages',
                            'params'     => ['404'],
                        ]
                    );
                    return false;
            }
        }

        $dispatcher->forward(
            [
                'controller' => 'errors',
                'action'     => 'show500'
            ]
        );
        return false;
    }
}

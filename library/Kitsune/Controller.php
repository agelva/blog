<?php

namespace Kitsune;

use Phalcon\Mvc\Controller as PhController;

class Controller extends PhController
{
    public function initialize()
    {
        $this->view->setTemplateAfter('main');
        $this->view->setVar('cdnUrl', $this->config->cdnUrl);
        $this->view->setVar('menuList', $this->finder->getList());
        $this->view->setVar('tagCloud', $this->finder->getTagCloud());
    }
}

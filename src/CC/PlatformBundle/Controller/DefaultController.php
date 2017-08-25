<?php

namespace CC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CCPlatformBundle:Default:index.html.twig');
    }
}

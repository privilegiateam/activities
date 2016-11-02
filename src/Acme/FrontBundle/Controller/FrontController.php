<?php

namespace Acme\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontController extends Controller
{
    public function indexAction()
    {
        return $this->render('AcmeFrontBundle:Front:index.html.twig');
    }
}

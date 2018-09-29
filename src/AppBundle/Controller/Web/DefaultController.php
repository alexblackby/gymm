<?php

namespace AppBundle\Controller\Web;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('login');
        }

        $rootDir = $this->get('kernel')->getProjectDir();
        $spaIndexPage = file_get_contents($rootDir . '/web/spa/index.html');

        return new Response($spaIndexPage);
    }
}

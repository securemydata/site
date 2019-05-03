<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Security\User\WebserviceUserProvider;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Security;

class DefaultController extends Controller
{
    /**
 * @Route("/", name="homepage")
 */
    public function indexAction(Request $request, Security $security)
    {
        // replace this example code with whatever you need

        var_export($this->get("security.token_storage")->getToken());
        var_export($security->getUser());
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
        ;

    }

}

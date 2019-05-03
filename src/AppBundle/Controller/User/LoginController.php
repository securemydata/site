<?php
/**
 * Created by PhpStorm.
 * User: guillaume.regairaz
 * Date: 03/05/2019
 * Time: 18:42
 */

namespace AppBundle\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Security\User\WebserviceUserProvider;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Security;

class LoginController extends Controller
{


    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
        // replace this example code with whatever you need

        $repo = new WebserviceUserProvider();
        $user = $repo->loadUserByUsername('tg');
        if (!$user) {
            throw new UsernameNotFoundException("User not found");
        } else {
            $token = new UsernamePasswordToken($user, null, "main", $user->getRoles());
            $this->get("security.token_storage")->setToken($token);

            /*$request = $this->get("request");
            $event = new InteractiveLoginEvent($request, $token);
            $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);*/
        }
        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(Request $request, Security $security)
    {

        $this->get('security.token_storage')->setToken(NULL);
        var_export($security->getUser());
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
        ]);;
    }
}
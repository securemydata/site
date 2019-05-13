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
use AppBundle\Security\User\WebserviceUser;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Security;


class LoginController extends Controller
{

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request, Security $security)
    {

        $client = new \Google_Client();
        $client->setAuthConfigFile('../client_secrets.json');
        $client->setRedirectUri('https://' . $_SERVER['HTTP_HOST'] . '/checklogin');
        $client->setAccessType('offline');
        $client->setIncludeGrantedScopes(true);
        $client->addScope(\Google_Service_Drive::DRIVE_METADATA_READONLY);
        //$client->addScope(\Google_Service_Drive::USERINFO_EMAIL);
        //$client->addScope(\Google_Service_Plus::USERINFO_PROFILE);

        $auth_url = $client->createAuthUrl();
        header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
        return $this->redirectToRoute('homepage');

    }

    /**
     * @Route("/login2", name="login2")
     */
    public function login2Action(Request $request)
    {
        // replace this example code with whatever you need

        $repo = new WebserviceUserProvider();
        $user = $repo->loadUserByUsername('tg');
        if (!$user) {
            throw new UsernameNotFoundException("User not found");
        } else {
            $token = new UsernamePasswordToken($user, null, "main", $user->getRoles());
            $this->get("security.token_storage")->setToken($token);

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

    /**
     * @Route("/checklogin", name="checklogin")
     */
    public function checkloginAction(Request $request, Security $security)
    {

        $client = new \Google_Client();
        $client->setAuthConfigFile('../client_secrets.json');
        $client->setRedirectUri('https://' . $_SERVER['HTTP_HOST'] . '/checklogin');
        $client->setAccessType('offline');
        $client->setIncludeGrantedScopes(true);
        $client->addScope(\Google_Service_Drive::DRIVE_METADATA_READONLY);
        //$client->addScope(\Google_Service_Drive::USERINFO_EMAIL);
        //$client->addScope(\Google_Service_Plus::USERINFO_PROFILE);

        if (!isset($_GET['code'])) {
            $auth_url = $client->createAuthUrl();
            echo $auth_url;
            header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
        } else {
            //$client->authenticate($_GET['code']);
            if(empty($_SESSION['token_data'])) {
                $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
                //var_export($token);



                //var_export($token);
                $client->setAccessToken($token);
                $token_data = $client->verifyIdToken();
                $_SESSION['token_data'] = $token_data;
            }else{
                $token_data = $_SESSION['token_data'];

            }
            $redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . '/';
            echo $token_data['email'];
            echo $token_data['given_name'];
            echo $token_data['family_name'];
            var_export($token_data);


            $u = new WebserviceUser( $token_data['email'],$token_data['given_name'].' '.$token_data['family_name'] , 'fd12fq1fd1qs2', ['ROLE_ADMIN']);
            $sftoken = new UsernamePasswordToken($u, null, "main", $u->getRoles());
            $this->get('security.token_storage')->setToken(NULL);
            $this->get("security.token_storage")->setToken($sftoken);

            echo $redirect_uri;
            //header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
        }
        return $this->redirectToRoute('homepage');

    }

}
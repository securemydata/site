<?php
/**
 * Created by PhpStorm.
 * User: guillaume.regairaz
 * Date: 03/05/2019
 * Time: 18:42
 */

namespace AppBundle\Controller\User;

use function Sodium\crypto_pwhash_scryptsalsa208sha256;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Security\User\WebserviceUserProvider;
use AppBundle\Security\User\WebserviceUser;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Security;
use AppBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;


class LoginController extends Controller
{

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request, Security $security)
    {

        $msgerror  = null;
        $user = new User();
        $c  = openssl_encrypt ( "key devrait être généré précédement d'une manière cryptographique, tel que openssl_random_pseudo_bytes" , "AES-128-CBC" , 'test',0,'123fdqs123456789');
        echo $c." [".strlen($c)."]";
        //echo openssl_decrypt( $c , "AES-128-CBC" , 'test',0,'123fdqs123456789');
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('login'))
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('connect', SubmitType::class, ['label' => 'Connect'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            //$violationList = $this->get('validator')->validate($user);
            $dataform = $form->getData();

            //var_export($dataform);
            $entityManager = $this->getDoctrine()->getManager();
            $isuser = $entityManager
                ->getRepository(User::class)
                ->findOneByEmail($dataform['email']);
            //var_export($isuser);
            $salt               = $this->container->getParameter('secret');
            $encodedPassword    = md5($dataform['password'].$salt);
            //echo "|".$isuser->getPassword()."|\r\n|".$encodedPassword.'| '.is_object($isuser).' '.($encodedPassword==$isuser->getPassword());
            if(is_object($isuser) && $encodedPassword==$isuser->getPassword()){

                //echo "yeahhhhhhhhhhhhhhhhhhhhhh !!! ";

                $u = new WebserviceUser( $dataform['email'],$encodedPassword , $isuser->getPassphrase(), ['ROLE_ADMIN']);
                $sftoken = new UsernamePasswordToken($u, null, "main", $u->getRoles());
                $this->get('security.token_storage')->setToken(NULL);
                $this->get("security.token_storage")->setToken($sftoken);

                return $this->redirectToRoute('homepage');
            }else{
                return $this->render('login/base_login.html.twig', [
                    'form' => $form->createView(),
                    'msgerror'=>'Login failed',
                ]);
            }

        }
        return $this->render('login/base_login.html.twig', [
            'form' => $form->createView(),
        ]);

    }
    /**
     * @Route("/loginfds", name="loginfds")
     */
    public function loginfdsAction(Request $request, Security $security)
    {

        $client = new \Google_Client();
        $client->setAuthConfigFile('../client_secrets.json');
        $client->setRedirectUri('https://' . $_SERVER['HTTP_HOST'] . '/checklogin');
        $client->setAccessType('offline');
        $client->setIncludeGrantedScopes(true);
        //$client->addScope(\Google_Service_Drive::DRIVE_METADATA_READONLY);
        $client->setScopes('email');
        $client->setScopes('profile');
        $client->setScopes('openid');
        //$client->setScopes('profile');
        $auth_url = $client->createAuthUrl();

        return $this->render('login/base_login.html.twig', [
            'url_google_api' => $auth_url,
        ]);
        ;

    }

    /**
 * @Route("/register", name="register")
 */
    public function registerAction(Request $request)
    {
        $msgerror  = null;
        $user = new User();

        $form = $this->createFormBuilder($user)
            ->setAction($this->generateUrl('register'))
            ->add('email', EmailType::class)
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('password', PasswordType::class)
            ->add('passphrase', PasswordType::class)
            ->add('save', SubmitType::class, ['label' => 'Register'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $violationList = $this->get('validator')->validate($user);
            //var_export($violationList);
            $dataform = $form->getData();
            // but, the original `$task` variable has also been updated
            //$muser = $form->getData();
           // $form->add('email', TextType::class)
//var_export($dataform);
            if(($violationList->count())>0){
                for($a=0;$a<$violationList->count();$a++)
                    $msgerror   .= $violationList->get($a)->getPropertyPath(). ':'.$violationList->get($a)->getMessage()."\r\n";
                //$form->remove('email');
                //echo $violationList->

                return $this->render('login/register.html.twig', [
                    'form' => $form->createView(),
                    'msgerror'=>$msgerror,
                ]);
            }else{
                //var_export($user);
                $entityManager = $this->getDoctrine()->getManager();
                $isuser = $entityManager
                    ->getRepository(User::class)
                    ->findOneByEmail($dataform->getEmail());

                if(is_object($isuser) && $isuser->getEmail()==$dataform->getEmail()){
                    return $this->render('login/register.html.twig', [
                        'form' => $form->createView(),
                        'msgerror'=>'This email already exist',
                    ]);
                }else{
                    $salt               = $this->container->getParameter('secret');
                    $encodedPassword = md5($dataform->getPassword() . $salt);
                    $user->setPassword($encodedPassword);
                    $entityManager->persist($user);
                    $entityManager->flush();
                    echo $salt.' '.$dataform->getPassword();
                    //return $this->redirectToRoute('homepage');
                }

            }
            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!

            //var_export($user);

        }
        return $this->render('login/register.html.twig', [
            'form' => $form->createView(),
        ]);


    }

    /**
     * @Route("/checkregister", name="checkregister")
     */
    public function checkregisterAction(Request $request)
    {

        $user = new User();

        $form = $this->createFormBuilder($user)
            ->setAction($this->generateUrl('checkregister'))
            ->add('email', EmailType::class)
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('password', PasswordType::class)
            ->add('passphrase', PasswordType::class)
            ->add('save', SubmitType::class, ['label' => 'Register'])
            ->getForm();

        $form->handleRequest($request);

        return $this->render('login/register.html.twig', [
            'form' => $form->createView(),
        ]);


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
        //var_export($security->getUser());
        return $this->redirectToRoute('homepage');
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
        $client->setScopes('email');
        $client->setScopes('profile');
        $client->setScopes('openid');

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



                var_export($token);
                $client->setAccessToken($token);
                $token_data = $client->verifyIdToken();
                $_SESSION['token_data'] = $token_data;
            }else{
                $token_data = $_SESSION['token_data'];

            }
            $redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . '/';
            var_export($token_data);
            $email  = $token_data['email'];
            if(isset($token_data['given_name'])){
                $given_name = $token_data['given_name'];
            }else{
                $given_name = $token_data['email'];
            }
            if(isset($token_data['family_name'])){
                $family_name = $token_data['family_name'];
            }else{
                $family_name    = "";
            }

            //var_export($token_data);


            $u = new WebserviceUser( $email,$given_name.' '.$family_name , 'fd12fq1fd1qs2', ['ROLE_ADMIN']);
            $sftoken = new UsernamePasswordToken($u, null, "main", $u->getRoles());
            $this->get('security.token_storage')->setToken(NULL);
            $this->get("security.token_storage")->setToken($sftoken);

            echo $redirect_uri;
            //header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
        }
        return $this->redirectToRoute('homepage');

    }

}
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
use AppBundle\Entity\User;

class UserController extends Controller
{

    /**
     * @Route("/moncompte", name="moncompte")
     */
    public function moncompteAction(Request $request, Security $security)
    {
        $mail   = $security->getUser()->getUsername();
        $user = $this->container->get('doctrine')->getManager()
            ->getRepository(User::class)
            ->findOneByEmail($mail);
        //var_export($user);

        return $this->render('user/user.html.twig', [
            'email' => $user->getemail(),
            'firstName' => $user->getfirstName(),
            'lastName' => $user->getlastName(),
            //'passphrase' => $user->getpassphrase(),
        ]);
        ;

    }
    /**
     * @Route("/modifyuser", name="modifyuser")
     */
    public function modifyuserAction(Request $request, Security $security)
    {
        $mail   = $security->getUser()->getUsername();
        $user = $this->container->get('doctrine')->getManager()
            ->getRepository(User::class)
            ->findOneByEmail($mail);
        //echo $request->request->get('firstName').$request->request->get('lastName').$request->request->get('passphrase');
        $user->setFirstName($request->request->get('firstName'));
        $user->setLastName($request->request->get('lastName'));

        $salt               = $this->container->getParameter('secret');

        if($request->request->get('passphrase')<>'') {
            $encodedPassphrase = User::encryptPassword($request->request->get('passphrase'), $salt);
            $user->setPassphrase($encodedPassphrase);
        }

        if($request->request->get('password')<>'') {
            $encodedPassword = User::encryptPassword($request->request->get('password'),$request->request->get('password').$salt);
            $user->setPassword($encodedPassword);
        }
        $date = new \DateTime();
        $user->setDatePassphrase($date);

        $entityManager  = $this->container->get('doctrine')->getManager();

        $entityManager->persist($user);
        $entityManager->flush();
        //var_export($user);

        return $this->render('user/user.html.twig', [
            'email' => $user->getemail(),
            'firstName' => $user->getfirstName(),
            'lastName' => $user->getlastName(),
            'passphrase' => $user->getpassphrase(),
        ]);
        ;
        //return $this->redirectToRoute('moncompte');

    }

}
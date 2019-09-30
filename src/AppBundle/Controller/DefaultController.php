<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Password;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Security\User\WebserviceUserProvider;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Security;
use AppBundle\Entity\User;

class DefaultController extends Controller
{
    /**
 * @Route("/", name="homepage")
 */
    public function indexAction(Request $request, Security $security)
    {
        // replace this example code with whatever you need

       // var_export($this->get("security.token_storage")->getToken());
        $session    = $request->getSession();
        $user       = $session->get('User');
        $count = $this->getDoctrine()
            ->getRepository(Password::class)
            ->countAll($user->getId());

        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'nb' => $count[0]['nb'],
            'lastdate' => strftime('%d/%m/%Y %Hh%M',strtotime($count[0]['lastdate'])),
               // 'test' => $count[1]['lastdate']
        ]
            );
    }

    /**
     * @Route("/test", name="testpage")
     */
    public function testAction(Request $request, Security $security)
    {
        // replace this example code with whatever you need

        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->find(1);
        //var_export($users);

        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneByEmail("test@free.fr");
        //var_export($users);

        return $this->render('default/index.html.twig', [
            'secret'=> $this->container->getParameter('secret'),
            'warning'=> 'Votre passephrase n\'est pas enregistrer. Veuillez le renseigne avant de continuer.',
            'warningUrl'=> "moncompte",
        ]);
        ;

    }
}

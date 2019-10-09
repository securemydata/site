<?php

namespace AppBundle\Controller;

use AppBundle\Entity\LogPasswordView;
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

        $nb = $nbview   = 0;
        $lastdate = $lastdateview   = '';

        $count = $this->getDoctrine()
            ->getRepository(Password::class)
            ->countAll($user->getId());
        $logs   = $this->getDoctrine()
            ->getRepository(LogPasswordView::class)
            ->countAll($user->getId());

        if(is_array($count)){
            if(!empty($count[0]['lastdate']))
                $lastdate   = $count[0]['lastdate'];
            if(!empty($count[0]['nb']))
                $nb         = $count[0]['nb'];
        }

        if(is_array($logs)){
            if(!empty($logs[0]['lastdate']))
                $lastdateview   = $logs[0]['lastdate'];
            if(!empty($logs[0]['nb']))
                $nbview         = $logs[0]['nb'];
        }

        $graph   = $this->getDoctrine()
            ->getRepository(LogPasswordView::class)
            ->getLastmonths($user->getId());

        $graphglob   = $this->getDoctrine()
            ->getRepository(LogPasswordView::class)
            ->getLastmonths();
        //var_export($graph);
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'nb' => $nb,
            'lastdate' => strftime('%d/%m/%Y %Hh%M',strtotime($lastdate)),
            'nbview' => $nbview,
            'lastdateview' => strftime('%d/%m/%Y %Hh%M',strtotime($lastdateview)),
            'graphx'=>json_encode($graph[0]),
            'graphy'=>json_encode($graph[1]),
            'graphgx'=>json_encode($graphglob[0]),
            'graphgy'=>json_encode($graphglob[1]),
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

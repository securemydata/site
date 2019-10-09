<?php
/**
 * Created by PhpStorm.
 * User: guillaume.regairaz
 * Date: 03/05/2019
 * Time: 18:42
 */

namespace AppBundle\Controller\Password;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Security\Core\Security;
use AppBundle\Entity\User;
use AppBundle\Entity\Password;
use AppBundle\Service\LogPassword;
use AppBundle\Entity\LogPasswordView;
use AppBundle\Repository\PasswordRepository;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class PasswordController extends Controller
{

    /**
 * @Route("/passwordslist", name="passwordslist")
 */
    public function Passwordslist(Request $request, Security $security)
    {
        $q      = $request->query->get('q');
        $mail   = $security->getUser()->getUsername();
        $user = $this->container->get('doctrine')->getManager()
            ->getRepository(User::class)
            ->findOneByEmail($mail);
        $pass = $this->getDoctrine()
            ->getRepository(Password::class)
            ->findAlllist($user->getId());
        //var_export($pass);


        return $this->render('password/passwordlist.html.twig',
            ['q' => $q]
        );

        //return $this->redirectToRoute('moncompte');

    }


    /**
     * @Route("/history", name="history")
     */
    public function historyPassView(Request $request, Security $security)
    {
        $q      = $request->query->get('q');
        $session    = $request->getSession();
        $user       = $session->get('User');


        $history = $this->getDoctrine()
            ->getRepository(LogPasswordView::class)
            -> findBy(array('idUser'=>$user->getId()));
        //var_export($pass);

        return $this->render('password/logpasswordlist.html.twig',
            ['q' => $q]
        );

        //return $this->redirectToRoute('moncompte');

    }


    /**
     * @Route("/histodata", name="histodata")
     */
    public function histoData(Request $request, Security $security)
    {

        $session    = $request->getSession();
        $user       = $session->get('User');


        $content = $request->query->all();
        if(!empty($content)) {
            $draw = $content['draw'];
            $orderColumn = $content['order'][0]['column'];
            $orderDir = $content['order'][0]['dir'];
            $start = $content['start'];
            $length = $content['length'];
            $search = $content['search']['value'];
        }else{
            $draw=1;
            $orderColumn=$orderDir=$start=$length=$search='';
        }

        $history = $this->getDoctrine()
            ->getRepository(LogPasswordView::class)
            ->loadDatatableHistory($user->getId(),$orderColumn,$orderDir,$start,$length,$search);
        //var_export($history);
        $data   = array();
        foreach($history as $h){
            $data[] = ['label' => $h['label'],'ip' => $h['ip'],'DateInsert'=>date_format($h['dateInsert'],'d/m/Y H:i')];

        }
        $ar =   [  "draw"=> $draw, "recordsTotal"=> count($history), "recordsFiltered"=> count($history),
            "data"=>$data
        ];


        return $this->json($ar);
        //return $this->redirectToRoute('moncompte');

    }

    /**
     * @Route("/passworddata", name="passworddata")
     */
    public function Passworddata(Request $request, Security $security)
    {
        $mail   = $security->getUser()->getUsername();
        /*$user = $this->container->get('doctrine')->getManager()
            ->getRepository(User::class)
            ->findOneByEmail($mail);*/

        $session    = $request->getSession();
        $user       = $session->get('User');


        $content = $request->query->all();
        if(!empty($content)) {
            $draw = $content['draw'];
            $orderColumn = $content['order'][0]['column'];
            $orderDir = $content['order'][0]['dir'];
            $start = $content['start'];
            $length = $content['length'];
            $search = $content['search']['value'];
        }else{
            $draw=1;
            $orderColumn=$orderDir=$start=$length=$search='';
        }

        $pass = $this->getDoctrine()
            ->getRepository(Password::class)
            ->findAlllist($user->getId(),$orderColumn,$orderDir,$start,$length,$search);
        $data   = array();
        foreach($pass as $thepass){
            $data[] = ['label' => $thepass->getLabel(),'login' => $thepass->getLogin(),'url' => $thepass->getUrl(),'password' => $thepass->getId(),'description' => nl2br($thepass->getDescription()),'DateInsert'=>date_format($thepass->getDateInsert(),'d/m/Y H:i'),'id'=>$thepass->getId()];

        }
        $ar =   [  "draw"=> $draw, "recordsTotal"=> count($pass), "recordsFiltered"=> count($pass),
                    "data"=>$data
                ];


        return $this->json($ar);
        //return $this->redirectToRoute('moncompte');

    }


    /**
     * @Route("/testajax", name="testajax")
     */
    public function TestAjax(Request $request, Security $security)
    {
        $mail   = $security->getUser()->getUsername();
        $session    = $request->getSession();
        $user       = $session->get('User');
        $passphrase = $session->get('Passphrase');

        $content = $request->query->get('a');
        $pass = $this->getDoctrine()
            ->getRepository(Password::class)
            ->findOneById($content);
        $salt               = $this->container->getParameter('secret');

        $ip    = $request->getClientIp();
        $p  = (User::decryptPassword($pass->getPassword(),$passphrase));
        $l = $this->get('app.logpassword')->log($user->getId(),$content, $ip);

        return new Response($p, 200, array('Content-Type' => 'text/html'));

    }



    /**
     * @Route("/passwordedit/{idpwd}", name="passwordedit")
     */
    public function EditPassword(Request $request, Security $security, $idpwd)
    {

        $mail   = $security->getUser()->getUsername();
        $session    = $request->getSession();
        $user       = $session->get('User');
        $passphrase = $session->get('Passphrase');
        $success    = $request->query->get('success');
        //$id = $request->query->get('id');
        $id = $idpwd;
        $ip    = $request->getClientIp();
        $l = $this->get('app.logpassword')->log($user->getId(),$idpwd,$ip);
        if(!empty($id) && $id>0) {
            $pass = $this->getDoctrine()
                ->getRepository(Password::class)
                ->findOneById($id);
            //var_export($pass);
            if($pass->getIdUser()<>$user->getId()){
                return $this->redirectToRoute('passwordslist');
            }

            $salt               = $this->container->getParameter('secret');

            $pass->setPassword(User::decryptPassword($pass->getPassword(),$passphrase));


        }else{
            $pass   = new Password();
        }
        $form = $this->createFormBuilder($pass)
            ->setAction('/passwordedit/'.$id)
            ->add('label', TextType::class)
            ->add('login', TextType::class)
            ->add('password', TextType::class)
            ->add('url', UrlType::class)
            ->add('description', TextareaType::class)
            ->add('date_insert', DateType::class, [
                'widget' => 'single_text',
                'label'  => 'Date',
            ])
            ->add('save', SubmitType::class, ['label' => 'Submit'])
            ->getForm();

        //var_export($pass);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $salt               = $this->container->getParameter('secret');

            $pass->setPassword(User::encryptPassword($pass->getPassword(),$passphrase));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pass);
           // var_export($pass);
            $entityManager->flush();
            return $this->redirect('/passwordedit/'.$pass->getId().'?success=true');

        }
        return $this->render('password/passwordedit.html.twig', [
            'form' => $form->createView(),
            'id' => $pass->getId(),
            'success'=>$success
        ]);
        ;
        //return $this->redirectToRoute('moncompte');

    }

    /**
     * @Route("/deletepassword/{idpwd}", name="deletepassword")
     */
    public function DeletePassword(Request $request, Security $security, $idpwd)
    {

        $mail   = $security->getUser()->getUsername();
        $session    = $request->getSession();
        $user       = $session->get('User');

        //$id = $request->query->get('id');
        $id = $idpwd;
        if(!empty($id) && $id>0) {
            $pass = $this->getDoctrine()
                ->getRepository(Password::class)
                ->findOneById($id);
            //var_export($pass);
            if($pass->getIdUser()<>$user->getId()){
                return $this->redirectToRoute('passwordslist');
            }
        }else{
            $pass   = new Password();
        }

      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->remove($pass);
      $entityManager->flush();
      return $this->redirect('/passwordslist');


        //return $this->redirectToRoute('moncompte');

    }

    /**
     * @Route("/passwordedit", name="addpassword")
     */
    public function AddPassword(Request $request, Security $security)
    {

        $mail   = $security->getUser()->getUsername();
        $session    = $request->getSession();
        $user       = $session->get('User');
        $passphrase = $session->get('Passphrase');
        //echo $passphrase;

        $pass   = new Password();

        $pass->setPassword(Password::randomPassword());
        $pass->setDateInsert(new \DateTime());

        $form = $this->createFormBuilder($pass)
            ->setAction('/passwordedit')
            ->add('label', TextType::class)
            ->add('login', TextType::class)
            ->add('password', TextType::class)
            ->add('url', UrlType::class)
            ->add('description', TextareaType::class)
            ->add('date_insert', DateType::class, [
                'widget' => 'single_text',
                'label'  => 'Date',
            ])
            ->add('save', SubmitType::class, ['label' => 'Submit'])
            ->getForm();

        //var_export($pass);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $salt               = $this->container->getParameter('secret');

            $pass->setPassword(User::encryptPassword($pass->getPassword(),$passphrase));

            $pass->setDateInsert(new \DateTime());
            $pass->setIdUser($user->getId());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pass);
            //var_export($pass);
            $entityManager->flush();
            return $this->redirect('/passwordslist');

        }
        return $this->render('password/passwordedit.html.twig', [
            'form' => $form->createView(),
            'id' => $pass->getId()
        ]);
        ;
        //return $this->redirectToRoute('moncompte');

    }
}
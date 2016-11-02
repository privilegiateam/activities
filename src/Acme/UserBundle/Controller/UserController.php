<?php

namespace Acme\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Acme\OutchiBundle\Manager\FileManager as FileManager;
use Symfony\Component\HttpFoundation\Request;
#use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Acme\UserBundle\Entity\Setting;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{

    public function connectFacebookAction()
    {
        $fbService = $this->get('fos_facebook.user.login');
        $fbService->connectExistingAccount();
        return $this->redirect($this->generateUrl('my_profile'));
    }

    public function loginFbAction()
    {
        return $this->redirect($this->generateUrl("user_fb_account"));
    }

    public function settingProfileAction()
    {
        $status = 0;
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $user = $this->getUser();
        $entity = $em->getRepository('AcmeUserBundle:Setting')->mySetting($user->getId());
        if (empty($entity)) {
            $entity = new Setting();
            $entity->setUser($this->getUser());
        }
        $form = $this->createForm(new SettingType(), $entity, array(
            'action' => $this->generateUrl('my_setting'),
            'method' => 'POST',
        ));
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($entity);
                $em->flush();
            }
            $status = 1;
        }

        $formP = $this->container->get('fos_user.change_password.form');
        $formHandler = $this->container->get('fos_user.change_password.form.handler');

        $process = $formHandler->process($user);
        if ($process) {
//            $this->setFlash('fos_user_success', 'change_password.flash.success');
            $status = 2;

//            return new RedirectResponse($this->container->get('router')->generate('my_setting'));
            return $this->render('AcmeUserBundle:Profile:setting.html.twig', array(
                'entity' => $entity,
                'form' => $form->createView(),
                'formP' => $formP->createView(),
                'status' => $status,
            ));
        }

        return $this->render('AcmeUserBundle:Profile:setting.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'formP' => $formP->createView(),
            'status' => $status,
        ));
    }

    public function lockedMyAccountAction()
    {
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            if ($_POST['oui'] == 1) {
                $id = $this->getUser()->getId();
                $em = $this->getDoctrine()->getManager();
                $user = $em->getRepository('AcmeUserBundle:User')->find($id);
                $user->setLocked(1);
                $em->persist($user);
                $em->flush();
            }
        }
        return new RedirectResponse($this->container->get('router')->generate('fos_user_security_logout'));
    }

    public function profileAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repEtab = $em->getRepository('AcmeOutchiBundle:Etablissement');
        $repEvent = $em->getRepository('AcmeOutchiBundle:Event');
        $etabs = $repEtab->etabByUser($this->getUser()->getId());
        $nbEvents = $repEvent->nbEventsByUser($this->getUser()->getId());
        $request = $this->getRequest();
        $id = 'user' . $this->getUser()->getId();
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        if (null === $thread) {
            $thread = $this->container->get('fos_comment.manager.thread')->createThread();
            $thread->setId($id);

            $thread->setPermalink($request->getUri());
            $thread->setCommentable(0);

            $this->container->get('fos_comment.manager.thread')->saveThread($thread);
        }

        $form = $this->createFormBuilder($this->getUser())
            ->add('imageFile', VichFileType::class, array('label' => false, 'allow_delete' => false, 'allow_delete' => false, 'attr' => array('class' => 'form-control importFileIMG ', 'style' => 'margin-bottom:0;border:none;height:33px')))
            ->add('Importer une photo', ButtonType::class, array('attr' => array('class' => 'importer')))
            //->add('save', SubmitType::class, array('label' => 'Enregistrer'))
            ->getForm();

        $form->handleRequest($request);

        $comments = $this->container->get('fos_comment.manager.comment')->findCommentTreeByThread($thread);

        $paginator = $this->get('knp_paginator');
        $paginComments = $paginator->paginate(
            $comments, $this->get('request')->query->get('page', 1), 4);


        return $this->render('AcmeUserBundle:Profile:myProfile.html.twig', array(
            'etabs' => $etabs,
            'nbEvents' => $nbEvents,
            'comments' => $paginComments,
            'thread' => $thread,
            'form' => $form->createView(),
            'votes' => 1
        ));
    }

    public function userprofileAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repEtab = $em->getRepository('AcmeOutchiBundle:Etablissement');
        $repEvent = $em->getRepository('AcmeOutchiBundle:Event');
        $user = $em->getRepository('AcmeUserBundle:User')->find($id);
        $etabs = $repEtab->etabByUser($user->getId());
        $nbEvents = $repEvent->nbEventsByUser($user->getId());

        $idTread = 'user' . $id;
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($idTread);
        if (null === $thread) {
            $thread = $this->container->get('fos_comment.manager.thread')->createThread();
            $thread->setId($idTread);
            $request = $this->getRequest();
            $thread->setPermalink($request->getUri());
            $thread->setCommentable(0);

            $this->container->get('fos_comment.manager.thread')->saveThread($thread);
        }

        $comments = $this->container->get('fos_comment.manager.comment')->findCommentTreeByThread($thread);

        $paginator = $this->get('knp_paginator');
        $paginComments = $paginator->paginate(
            $comments, $this->get('request')->query->get('page', 1), 4);


        return $this->render('AcmeUserBundle:Profile:userProfile.html.twig', array(
            'user' => $user,
            'etabs' => $etabs,
            'nbEvents' => $nbEvents,
            'comments' => $paginComments,
            'thread' => $thread,
            'votes' => 1
        ));
    }

    public function editProfileAjaxAction($value, $champ)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if ($champ == "e") {
            $user->setEcole($value);
        } else if ($champ == "t") {
            $user->setTravail($value);
        } else if ($champ == "b") {
            $user->setBio($value);
        } else if ($champ == "em") {
            $user->setEmail($value);
        } else if ($champ == "p") {
            $user->setPhone($value);
        }
        $em->persist($user);
        $em->flush();
        die;
    }

    public function confirmedAction()
    {
        return $this->redirect($this->generateUrl("my_profile"));
    }

    // ******* Functions and methodes of Upload Pictures **********************
    public function fileEditAction()
    {
        //dump($_POST);die;
        $em = $this->getDoctrine()->getManager();
        $id = $_POST['user_id'];
        $path = '/uploads/images/user';
        $defaultImg = "user-icon.png";

        $fileManager = new FileManager();

        $entity = $this->getDoctrine()
            ->getRepository('AcmeUserBundle:User')
            ->find($id);


        $fileName = $fileManager->getNameOfFile($id, $_FILES);

        if ($fileManager->uploadFile($_FILES, $path, $fileName)) {

            $pathAbsoluteNameofFile = $fileManager->getAbsolutPathFile($path, $entity->getImage());

            if (file_exists($pathAbsoluteNameofFile) && $entity->getImage() != $defaultImg) {
                //unlink($pathAbsoluteNameofFile);
            }

            $entity->setImage($fileName);
            $em->persist($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('my_profile'));
    }

    public function editRenseignementsAction($name, $value)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if ($name === "nom") {
            $user->setLastname($value);
        } else if ($name === 'prenom') {
            $user->setFirstname($value);
        } else if ($name === 'date') {
            $user->setDateBirth($value);
        } else if ($name === "mail") {
            $user->setEmail($value);
            $user->setEmailVerified(0);
        } else if ($name === "tel") {
            $user->setPhone($value);
            $user->setPhoneVerified(0);
        } else if ($name === "language") {
            $user->setLanguage($value);
        }

        $em->persist($user);
        $em->flush();

        die;
    }

    public function verifierEmailAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AcmeUserBundle:User')->find($id);
        $user->setEmailVerified(1);
        $em->persist($user);
        $em->flush();
        return new RedirectResponse($this->container->get('router')->generate('my_setting'));
    }

    public function sendVerifierEmailAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AcmeUserBundle:User')->find($id);
        $message = \Swift_Message::newInstance()
            ->setSubject('Outchi: Vérification E-mail')
            ->setFrom('contact@smartup-dev.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView('emails/emailVerification.html.twig', array('user' => $user)
                ), 'text/html'
            );

        $this->get('mailer')->send($message);
    }

   

}

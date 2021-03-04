<?php

namespace App\Controller;


use App\Entity\User;

use App\Form\RegistrationFormType;
use App\Form\ResetPassWordType;
use App\Form\UserformType;
use App\Form\UserType;
use App\Repository\UserRepository;

use Doctrine\ORM\EntityRepository;
use App\Security\LoginFormAuthenticator;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Persistence\RegistryInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;



class RegistrationController extends AbstractController
{

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator, \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            /* $image = $form->get('image')->getData();

             $imageName = md5(uniqid()).'.'.$image->guessExtension();

             $user->setImage($imageName);

             $image->move(
                 $this->getParameter('image_directory'), $imageName);
             $user->setImage($imageName);*/
            /*
                        dump($image);*/
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();


            if ($request->isMethod('POST')) {

                $email =  $form->get('email')->getData();

                $entityManager = $this->getDoctrine()->getManager();
                $user = $entityManager->getRepository(User::class)->findOneByEmail($email);

                if ($user === null) {
                    $this->addFlash('danger', 'Email Inconnu, recommence !');
                    return $this->redirectToRoute('app_');
                }
                $token = $tokenGenerator->generateToken();

                try {
                    /* $user->setToken($token);*/
                    $entityManager->flush();
                } catch (\Exception $e) {
                    $this->addFlash('warning', $e->getMessage());
                    return $this->redirectToRoute('home');
                }

                $url = $this->generateUrl('app_login', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

                $message = (new \Swift_Message('Confirmation email'))
                    ->setFrom(array('jeandesir84@gmail.com' => 'Senior Services'))
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'security/emails/confirmation.html.twig',
                            [
                                'user' => $user,
                                'url' => $url
                            ]
                        ),
                        'text/html'
                    );
                $mailer->send($message);

                $this->addFlash('success', 'Bravo! Votre compte à bien été, un email de confirmation vous a été envoyer  allerz confimer votre adress email!');

                return $this->redirectToRoute('main');

            }
            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }



    /**
     * @Route("/registration-{id}-prestaRegister", name="registration.prestaRegister")
     */


    public function  new($id,UserRepository $repository, Request $request, ObjectManager $manager)
    {

        $user = $repository->findOneBy(['id' => $id]);
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $session = $request->getSession();







        $prestataire = new Prestataire();
        /* $appUser =  $this->getUser(); */
        dump($user );


        $prestataire->setUser($user);

        $form = $this->createForm(PrestataireFormtype::class,  $prestataire);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($prestataire);
            $manager->flush();
            $this->addFlash('success', "demande bien envoyer");
            return $this->redirectToRoute("home");



        }
        return $this->render('registration/prestaRegister.html.twig', [

            'PrestataireFormtype' => $form->createView(),
        ]);
    }
    /*
        public function new(Request $request, ObjectManager $manager)
        {



            $prestataire = new Prestataire();
            $prestataire->addUser($user);
            $form = $this->createForm(PrestataireFormType::class, $prestataire);


            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {


                $manager->persist($prestataire);
                $manager->flush();
                $this->addFlash('success', "demande bien envoyer");
                return $this->redirectToRoute('home', ['id' => $user->getUser()]);//, ['id' => $produit->getId()]);
            }
            return $this->render('registration/prestaRegister.html.twig', [

                'PrestataireFormtype' => $form->createView(),
            ]);
        }*



/**
* @Route("/admin/userAdd", name="admin.userAdd")
*/
    public function register1(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }





    /*$em = $this->getDoctrine()->getManager();
    $user = $this->getUser();
    $form = $this->createForm(ResetPasswordType::class, $user);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {

        $passwordEncoder = $this->get('security.password_encoder');
        $oldPassword = $request->request->get('etiquettebundle_user')['oldPassword'];

        // Si l'ancien mot de passe est bon
        if ($passwordEncoder->isPasswordValid($user, $oldPassword)) {
            $newEncodedPassword = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($newEncodedPassword);

            $em->persist($user);
            $em->flush();

            $this->addFlash('notice', 'Votre mot de passe à bien été changé !');

            return $this->redirectToRoute('profile');
        } else {
            $form->addError(new FormError('Ancien mot de passe incorrect'));
        }
    }*/

    /**
     * @Route("/author-{id}-ressetpassword", name="author.ressetpassword")
     */
    /*  public function ressetPassord($id, UserRepository $repository,Request $request, ObjectManager $manager, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator)
      {

          $user = $repository->findOneBy(['id' => $id]);


          $em = $this->getDoctrine()->getManager();
          $user = $this->getUser();
          $form = $this->createForm(ResetPassWordType::class, $user);

          $form->handleRequest($request);
          if ($form->isSubmitted() && $form->isValid()) {

              $passwordEncoder = $this->get('security.password_encoder');
              $oldPassword = $request->request->get('etiquettebundle_user')['oldPassword'];

              // Si l'ancien mot de passe est bon
              if ($passwordEncoder->isPasswordValid($user, $oldPassword)) {
                  $newEncodedPassword = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                  $user->setPassword($newEncodedPassword);

                  $em->persist($user);
                  $em->flush();

                  $this->addFlash('notice', 'Votre mot de passe à bien été changé !');

                  return $this->redirectToRoute('profile');
              } else {
                  $form->addError(new FormError('Ancien mot de passe incorrect'));
              }
          }

          return $this->render('author/ressetpassword.html.twig', array(

              'formRessetpasswordType' => $form->createView()
          ));

      }*/
    /*  public function ressetPassord(Request $request)
      {
          $em = $this->getDoctrine()->getManager();
          $user = $this->getUser();
          $form = $this->createForm(ResetPasswordType::class, $user);

          $form->handleRequest($request);
          if ($form->isSubmitted() && $form->isValid()) {

              $passwordEncoder = $this->get('security.password_encoder');
              $oldPassword = $request->request->get('etiquettebundle_user')['oldPassword'];

              // Si l'ancien mot de passe est bon
              if ($passwordEncoder->isPasswordValid($user, $oldPassword)) {
                  $newEncodedPassword = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                  $user->setPassword($newEncodedPassword);

                  $em->persist($user);
                  $em->flush();

                  $this->addFlash('notice', 'Votre mot de passe à bien été changé !');

                  return $this->redirectToRoute('profile');
              } else {
                  $form->addError(new FormError('Ancien mot de passe incorrect'));
              }
          }

          return $this->render('author/edit.html.twig', array(
              'formResset' => $form->createView(),
          ));
      }
  */


}


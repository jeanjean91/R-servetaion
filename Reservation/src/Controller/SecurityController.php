<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\UserType;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\AppRoleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Tests\Logout;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;













class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();






        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/admin-AdminLogin", name="admin.AdminLogin")
     */
    public function adminLogin(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();






        return $this->render('admin/AdminLogin.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

   // /**
    // * @Route("/prestatair", name="prestatair")
   ///  */

/*    public function prestatairLogin(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();






        return $this->render('prestatair/dashboard.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }*/

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {

    }


    /**
     * @Route("/oubli-mot-de-passe", name="app_forgotten_password", methods="GET|POST")
     */
    public function forgottenPassword(Request $request, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator): Response
    {
        if ($request->isMethod('POST')) {

            $email = $request->request->get('email');

            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->findOneByEmail($email);

            if ($user === null) {
                $this->addFlash('danger', 'Email Inconnu, recommence !');
                return $this->redirectToRoute('app_forgotten_password');
            }
            $token = $tokenGenerator->generateToken();

            try{
                $user->setResetToken($token);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('home');
            }

            $url = $this->generateUrl('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

            $message = (new \Swift_Message('Oubli de mot de passe - Réinisialisation'))
                ->setFrom(array('jeandesir84@gmail.com'=> 'Sport Shop'))
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'security/emails/resetPasswordMail.html.twig',
                        [
                            'user'=>$user,
                            'url'=>$url
                        ]
                    ),
                    'text/html'
                );
            $mailer->send($message);

            $this->addFlash('success', 'Un email vous a ete envoyer avec unlien pour modifier votre mot de pass !');

            return $this->redirectToRoute('home');
        }

        return $this->render('security/forgottenPassword.html.twig');
    }

    /** Réinisialiation du mot de passe par mail
     * @Route("/reinitialiser-mot-de-passe/{token}", name="app_reset_password")
     */
    public function resetPassword(Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder)
    {
        //Reset avec le mail envoyé
        if ($request->isMethod('POST')) {
            $entityManager = $this->getDoctrine()->getManager();

            $user = $entityManager->getRepository(User::class)->findOneByResetToken($token);
            /* @var $user User */

            if ($user === null) {
                $this->addFlash('danger', 'Mot de passe non reconnu');
                return $this->redirectToRoute('home');
            }

            $user->setResetToken(null);
            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe mis à jour ! vous pouvez vous connecter avec votre nouveau mot de passe');

            return $this->redirectToRoute('home');
        }else {

            return $this->render('security/resetPassword.html.twig', ['token' => $token]);
        }

    }

}

<?php

namespace LoginExampleBundle\Controller;

use LoginExampleBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use LoginExampleBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;



class UserController extends BaseController
{
    /**
     * @Route("/register", name="user_register")
     * @Method("GET")
     */
    public function registerAction()
    {
        if ($this->isUserLoggedIn()) {
            return $this->redirect($this->generateUrl('homepage'));
        }

        return $this->render('user/register.twig', array('user' => new User()));
    }

    /**
     * @Route("/register", name="user_register_handle")
     * @Method("POST")
     */
    public function registerHandleAction(Request $request)
    {
        $errors = array();

        if (!$email = $request->request->get('email')) {
            $errors[] = '"email" is required';
        }
        if (!$plainPassword = $request->request->get('password')) {
            $errors[] = '"password" is required';
        }
        if (!$username = $request->request->get('username')) {
            $errors[] = '"username" is required';
        }

        $userRepository = $this->getUserRepository();

        // make sure we don't already have this user!
        if ($existingUser = $userRepository->findUserByEmail($email)) {
            $errors[] = 'A user with this email is already registered!';
        }

        // make sure we don't already have this user!
        if ($existingUser = $userRepository->findUserByUsername($username)) {
            $errors[] = 'A user with this username is already registered!';
        }

        $user = new User();
        $user->setEmail($email);
        $user->setUsername($username);
        $encodedPassword = $this->container->get('security.password_encoder')
            ->encodePassword($user, $plainPassword);
        $user->setPassword($encodedPassword);

        // errors? Show them!
        if (count($errors) > 0) {
            // return $this->render('user\register.twig', array('errors' => $errors, 'user' => $user));
            // $view = $this->view("teste"));
            // return $this->handleView($view);
            return new JsonResponse($errors);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $this->loginUser($user);

        // return $this->redirect($this->generateUrl('homepage'));

        return new JsonResponse($user);
    }

    /**
     * @Route("/login", name="security_login_form")
     */
    public function loginAction(Request $request)
    {
        if ($this->isUserLoggedIn()) {
            return new JsonResponse("User is logged in");
        }

        return new JsonResponse("Need to log in");

    }

    /**
     * @Route("/login_check", name="security_login_check")
     */
    public function loginCheckAction()
    {
        throw new \Exception('Should not get here - this should be handled magically by the security system!');
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logoutAction()
    {
        throw new \Exception('Should not get here - this should be handled magically by the security system!');
    }

    /**
     * @Route("/test/login")
     */
    public function testLoginAction(Request $request)
    {
        $logger = $this->get('logger');
        $logger->info("teste>> " . $request->request->get('_username'));
        $user = $this->getDoctrine()->getRepository('LoginExampleBundle:User')->findOneBy(['username' => $request->request->get('_username')]);
        if(!$user){
            return new JsonResponse(['errors' => ['invalid user']],404);
            // throw $this->createNotFoundException();
        }
        $isValid = $this->get('security.password_encoder')->isPasswordValid($user, $request->request->get('_password'));

        if (!$isValid) {
            // throw new BadCredentialsException();
            return new JsonResponse(['errors' => ["Bad credentials"]],404);
        }
        $logger->info('user: ' . $user->getUsername());

        $token = $this->get('lexik_jwt_authentication.encoder')->encode([
            'username' => $user->getUsername(),
            'exp' => time() + 3600 // 1 hour expiration
        ]);

        return new JsonResponse(['token' => $token]);
    }
}

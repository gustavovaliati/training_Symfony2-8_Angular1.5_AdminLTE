<?php

namespace LoginExampleBundle\Controller\APIv1;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use FOS\RestBundle\Controller\FOSRestController;

use LoginExampleBundle\Entity\User;

use LoginExampleBundle\Entity\Orange;

class TokenController extends Controller
{
    /**
     * @Route("/tokens")
     * @Method("POST")
     */
    public function newTokenAction(Request $request)
    {
        $logger = $this->get('logger');

        $data = json_decode($request->getContent(), true);
        $request->request->replace($data);
        $user = $this->getDoctrine()
            ->getRepository('LoginExampleBundle:User')
            ->findOneBy(['username' => $request->request->get('username')]);

        $orange = $this->getDoctrine()->getRepository('LoginExampleBundle:Orange')->findAll();

        $logger->info("antes");
        $logger->info(serialize($user));
        $logger->info("depois");

        if (!$user || null == $user) {
            // throw $this->createNotFoundException();
            return new JsonResponse(['erro' => "user not found", 'userObj' => serialize($user)]);
        }

        $isValid = $this->get('security.password_encoder')
            ->isPasswordValid($user, $request->getPassword());

        if (!$isValid) {
            // throw new BadCredentialsException();
            return new JsonResponse(['erro' => "pass not valid", 'userObj' => serialize($user)]);
        }
        $token = $this->get('lexik_jwt_authentication.encoder')
            ->encode([
                'username' => $user->getUsername(),
                'exp' => time() + 3600 // 1 hour expiration
            ]);

        return new JsonResponse(['token' => "$token"]);
    }
}

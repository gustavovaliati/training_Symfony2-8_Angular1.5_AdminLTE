<?php

namespace LoginExampleBundle\Controller\APIv1;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use FOS\RestBundle\Controller\FOSRestController;

use LoginExampleBundle\Entity\Orange;

/**
 * @Route("/admin")
 * @Method({"POST", "GET"})
 */
class AdminController extends FOSRestController
{

    /**
    * @Route("/role")
    * @Method("GET")
    * @Security("has_role('ROLE_ADMIN')")
 */
    public function roleAction()
    {

        $view = $this->view("HAS ROLE_ADMIN");
        return $this->handleView($view);
    }

    /**
    * @Route("/auth")
    * @Method("GET")
    * @Security("has_role('ROLE_ADMIN')")
    */
    public function authAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $view = $this->view("IS AUTHENTICATED");
        return $this->handleView($view);
    }

    /**
    * @Route("/acl")
    * @Method("GET")
    */
    public function aclTestOneAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getUser();
        // the above is a shortcut for this
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $view = $this->view($user->getFirstName());
        return $this->handleView($view);
    }



}

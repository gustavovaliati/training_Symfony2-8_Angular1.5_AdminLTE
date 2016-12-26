<?php

namespace LoginExampleBundle\Controller\APIv1;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use FOS\RestBundle\Controller\FOSRestController;

use LoginExampleBundle\Entity\User;
use LoginExampleBundle\Form\UserType;

/**
 * @Route("/user")
 * @Method({"POST", "GET"})
 */
class UserController extends FOSRestController
{

    /**
    * @Route("/login")
    * @Method("POST")
    */
    public function loginAction()
    {
        $user =  $this->getDoctrine()->getRepository('LoginExampleBundle:Orange')->findAll();
        $view = $this->view($user);
        return $this->handleView($view);
    }

    /**
    * @ApiDoc(
     *  description="Returns a collection of Object",
     *  output="LoginExampleBundle\Controller\APIv1\OrangeController.php",
     *  input="LoginExampleBundle\Controller\APIv1\OrangeController.php",
     *  statusCodes={
     *         200="Returned when successful",
     *         403="Returned when the user is not authorized to say hello",
     *         404={
     *           "Returned when the user is not found",
     *           "Returned when something else is not found"
     *         }
     *     },
     *  requirements={
     *      {
     *          "name"="username",
     *          "dataType"="string",
     *          "requirement"="\s+",
     *          "description"="how many objects to return"
     *      }
     *  },
     *  parameters={
     *      {"name"="categoryId", "dataType"="integer", "required"=true, "description"="category id"}
     *  }
     * )
     *
     * @Route("/register")
     * @Method("POST")
     */
    public function registerAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $request->request->replace($data);

        $user = new User();;

        $user->setUsername($request->request->get('username', 'defaultValue'));
        $user->setEmail($request->request->get('email'));
        $user->setPassword($request->request->get('password'));

        // TODO how to add validator to user ?
        $validator = $this->get('validator');
        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            /*
             * Uses a __toString method on the $errors variable which is a
             * ConstraintViolationList object. This gives us a nice string
             * for debugging.
             */
            $errorsString = (string) $errors;
            $view = $this->view($errorsString);
            return $this->handleView($view);
        }

        $em = $this->getDoctrine()->getManager();

        $em->persist($user);
        $em->flush();

        $view = $this->view($user);
        return $this->handleView($view);

    }

    /**
    * @Route("/")
    * @Method("GET")
    */
    public function getAllAction()
    {
        $users =  $this->getDoctrine()->getRepository('LoginExampleBundle:User')->findAll();
        $view = $this->view($users);
        return $this->handleView($view);
    }

    /**
    * @Route("/hello")
    */
    public function helloAction()
    {
        $data = array("hello" => "world");
        $view = $this->view($data);
        return $this->handleView($view);
    }



}

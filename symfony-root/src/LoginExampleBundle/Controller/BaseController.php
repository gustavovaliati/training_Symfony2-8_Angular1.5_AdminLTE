<?php

namespace LoginExampleBundle\Controller;

// use LoginExampleBundle\Repository\ProgrammerRepository;
use LoginExampleBundle\Repository\UserRepository;
// use LoginExampleBundle\Repository\ProjectRepository;
// use LoginExampleBundle\Repository\BattleRepository;
use LoginExampleBundle\Repository\ApiTokenRepository;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Request;
use LoginExampleBundle\Entity\User;

abstract class BaseController extends Controller
{
    /**
     * Is the current user logged in?
     *
     * @return boolean
     */
    public function isUserLoggedIn()
    {
        return $this->container->get('security.authorization_checker')
            ->isGranted('IS_AUTHENTICATED_FULLY');
    }

    /**
     * Logs this user into the system
     *
     * @param User $user
     */
    public function loginUser(User $user)
    {
        $logger = $this->get('logger');

        $token = new UsernamePasswordToken($user, $user->getPassword(), 'main', $user->getRoles());
        $logger->info($token);

        $this->container->get('security.token_storage')->setToken($token);
    }

    public function addFlash($message, $positiveNotice = true)
    {
        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $noticeKey = $positiveNotice ? 'notice_happy' : 'notice_sad';

        $request->getSession()->getFlashbag()->add($noticeKey, $message);
    }

    /**
     * Used to find the fixtures user - I use it to cheat in the beginning
     *
     * @param $username
     * @return User
     */
    public function findUserByUsername($username)
    {
        return $this->getUserRepository()->findUserByUsername($username);
    }

    /**
     * @return UserRepository
     */
    protected function getUserRepository()
    {
        return $this->getDoctrine()
            ->getRepository('LoginExampleBundle:User');
    }

    /**
     * @return ProgrammerRepository
     */
    protected function getProgrammerRepository()
    {
        return $this->getDoctrine()
            ->getRepository('LoginExampleBundle:Programmer');
    }

    /**
     * @return ProjectRepository
     */
    protected function getProjectRepository()
    {
        return $this->getDoctrine()
            ->getRepository('LoginExampleBundle:Project');
    }

    /**
     * @return BattleRepository
     */
    protected function getBattleRepository()
    {
        return $this->getDoctrine()
            ->getRepository('LoginExampleBundle:Battle');
    }

    /**
     * @return \LoginExampleBundle\Battle\BattleManager
     */
    protected function getBattleManager()
    {
        return $this->container->get('battle.battle_manager');
    }

    /**
     * @return ApiTokenRepository
     */
    protected function getApiTokenRepository()
    {
        return $this->getDoctrine()
            ->getRepository('LoginExampleBundle:ApiToken');
    }

    protected function createApiResponse($data, $statusCode = 200)
    {
        $json = $this->serialize($data);

        return new Response($json, $statusCode, array(
            'Content-Type' => 'application/json'
        ));
    }

    protected function serialize($data, $format = 'json')
    {
        $context = new SerializationContext();
        $context->setSerializeNull(true);

        $request = $this->get('request_stack')->getCurrentRequest();
        $groups = array('Default');
        if ($request->query->get('deep')) {
            $groups[] = 'deep';
        }
        $context->setGroups($groups);

        return $this->container->get('jms_serializer')
            ->serialize($data, $format, $context);
    }
}

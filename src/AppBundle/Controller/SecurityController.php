<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login_route")
     * @Template
     */
    public function loginAction()
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('homepage');
        }

        $authenticationUtils = $this->get('security.authentication_utils');

        return [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error'         => $authenticationUtils->getLastAuthenticationError(),
        ];
    }

    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheckAction()
    {
        // this controller will not be executed,
        // as the route is handled by the Security system
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/sign_up", name="register")
     * @Method("GET")
     * @Template()
     */
    public function registerAction()
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('homepage');
        }

        $entity = new User();

        return [
            'entity' => $entity,
            'form'   => $this->createRegistrationForm($entity)->createView(),
        ];
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/sign_up", name="sign_up")
     * @Method("POST")
     * @Template("AppBundle:User:register.html.twig")
     */
    public function signUpAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('homepage');
        }

        $entity = new User();
        $form   = $this->createRegistrationForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setPassword(
                $this->container->get('security.password_encoder')->encodePassword($entity, $entity->getPassword())
            );
            $entity->addRole($em->getRepository('AppBundle:Role')->findOperatorRole());
            $em->persist($entity);
            $em->flush();

            $token = new UsernamePasswordToken($entity, null, 'main', $entity->getRoles());
            $this->get('security.token_storage')->setToken($token);

            return $this->redirect($this->generateUrl('homepage'));
        }

        return [
            'entity' => $entity,
            'form'   => $form->createView(),
        ];
    }

    /**
     * Creates a form to sign up a User.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createRegistrationForm(User $entity)
    {
        $form = $this->createForm(new RegistrationType(), $entity, [
            'action' => $this->generateUrl('sign_up'),
            'method' => 'POST',
        ]);

        $form->add('submit', 'submit', ['label' => 'Join']);

        return $form;
    }
}

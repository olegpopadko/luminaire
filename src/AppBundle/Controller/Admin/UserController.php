<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Form\Model\UserWithPlainPassword;
use AppBundle\Form\UserWithPlainPasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;

/**
 * User controller.
 * @Route("/user")
 */
class UserController extends Controller
{

    /**
     * Lists all User entities.
     *
     * @Route("/", name="admin_user")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:User')->findAll();

        return [
            'entities' => $entities,
        ];
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/", name="admin_user_create")
     * @Method("POST")
     * @Template("AppBundle:Admin/User:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new User();
        $form   = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setPassword(
                $this->container->get('security.password_encoder')->encodePassword($entity, $entity->getPassword())
            );
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_user_edit', ['id' => $entity->getId()]));
        }

        return [
            'entity' => $entity,
            'form'   => $form->createView(),
        ];
    }

    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, [
            'action'            => $this->generateUrl('admin_user_create'),
            'method'            => 'POST',
            'validation_groups' => ['User', 'not_blank_password'],
        ]);

        $form->add('submit', 'submit', ['label' => 'Create']);

        return $form;
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/new", name="admin_user_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new User();
        $form   = $this->createCreateForm($entity);

        return [
            'entity' => $entity,
            'form'   => $form->createView(),
        ];
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="admin_user_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createEditForm(new UserWithPlainPassword($entity));

        return [
            'entity'    => $entity,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * Creates a form to edit a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(UserWithPlainPassword $entity)
    {
        $form = $this->createForm(new UserWithPlainPasswordType(new UserType()), $entity, [
            'action' => $this->generateUrl('admin_user_update', ['id' => $entity->getUser()->getId()]),
            'method' => 'PUT'
        ]);

        $form->add('submit', 'submit', ['label' => 'Update']);

        return $form;
    }

    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}", name="admin_user_update")
     * @Method("PUT")
     * @Template("AppBundle:Admin/User:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createEditForm(new UserWithPlainPassword($entity));
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $password = $editForm->getData()->getPlainPassword();
            if (!is_null($password)) {
                $entity->setPassword(
                    $this->container->get('security.password_encoder')->encodePassword($entity, $password)
                );
            }
            $em->flush();

            return $this->redirect($this->generateUrl('admin_user'));
        }

        return [
            'entity'    => $entity,
            'edit_form' => $editForm->createView(),
        ];
    }
}

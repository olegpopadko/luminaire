<?php

namespace AppBundle\Controller;

use AppBundle\Form\Model\UserWithPlainPassword;
use AppBundle\Form\UserWithPlainPasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\User;
use AppBundle\Form\ProfileType;

/**
 * User controller.
 *
 * @Route("/profile")
 */
class ProfileController extends Controller
{
    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="profile")
     * @Method("GET")
     * @Template()
     * @Security("is_granted('view', entity)")
     */
    public function indexAction(User $entity)
    {
        return [
            'entity'          => $entity,
        ];
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="profile_edit")
     * @Method("GET")
     * @Template()
     * @Security("is_granted('edit', entity)")
     */
    public function editAction(User $entity)
    {
        return [
            'entity'    => $entity,
            'edit_form' => $this->createEditForm(new UserWithPlainPassword($entity))->createView(),
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
        $form = $this->createForm(new UserWithPlainPasswordType(new ProfileType()), $entity, [
            'action' => $this->generateUrl('profile_update', ['id' => $entity->getUser()->getId()]),
            'method' => 'PUT',
        ]);

        $form->add('submit', 'submit', ['label' => 'Update']);

        return $form;
    }

    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}", name="profile_update")
     * @Method("PUT")
     * @Template("AppBundle:Profile:edit.html.twig")
     * @Security("is_granted('edit', entity)")
     */
    public function updateAction(Request $request, User $entity)
    {
        $editForm = $this->createEditForm(new UserWithPlainPassword($entity));
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $password = $editForm->getData()->getPlainPassword();
            if (!is_null($password)) {
                $entity->setPassword(
                    $this->container->get('security.password_encoder')->encodePassword($entity, $password)
                );
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('profile', ['id' => $entity->getId()]));
        }

        return [
            'entity'    => $entity,
            'edit_form' => $editForm->createView(),
        ];
    }
}

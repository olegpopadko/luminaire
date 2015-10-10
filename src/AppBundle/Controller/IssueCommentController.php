<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Issue;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\IssueComment;
use AppBundle\Form\IssueCommentType;

/**
 * IssueComment controller.
 */
class IssueCommentController extends Controller
{
    /**
     * Creates a new IssueComment entity.
     *
     * @Route("/issue/{code}/comment", name="issue_comment_create")
     * @Method("POST")
     * @Template("AppBundle:IssueComment:new.html.twig")
     * @Security("is_granted('view', issue)")
     */
    public function createAction(Request $request, Issue $issue)
    {
        $entity = new IssueComment();
        $entity->setIssue($issue);
        $entity->setUser($this->getUser());
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $url = $this->generateUrl('issue_show', ['code' => $this->getIssueCode($entity->getIssue())]);
            return $this->redirect($url);
        }

        return [
            'entity' => $entity,
            'form'   => $form->createView(),
        ];
    }

    /**
     * Creates a form to create a IssueComment entity.
     *
     * @param IssueComment $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(IssueComment $entity)
    {
        $action = $this->generateUrl('issue_comment_create', ['code' => $this->getIssueCode($entity->getIssue())]);
        $form   = $this->createForm(new IssueCommentType(), $entity, [
            'action' => $action,
            'method' => 'POST',
        ]);

        $form->add('submit', 'submit', ['label' => 'Create']);

        return $form;
    }

    /**
     * Displays a form to create a new IssueComment entity.
     *
     * @Route("/issue/{code}/comment/new", name="issue_comment_new")
     * @Method("GET")
     * @Template()
     * @Security("is_granted('view', issue)")
     */
    public function newAction(Request $request, Issue $issue)
    {
        $entity = new IssueComment();
        $entity->setIssue($issue);
        $entity->setUser($this->getUser());
        if ($request->get('parent_id')) {
            $em           = $this->getDoctrine()->getManager();
            $parentEntity = $em->getReference('AppBundle:IssueComment', $request->get('parent_id'));
            $entity->setParent($parentEntity);
        }
        $form = $this->createCreateForm($entity);

        return [
            'entity' => $entity,
            'form'   => $form->createView(),
        ];
    }

    /**
     * Displays a form to edit an existing IssueComment entity.
     *
     * @Route("/comment/{id}/edit", name="issue_comment_edit")
     * @Method("GET")
     * @Template()
     * @Security("is_granted('edit', entity)")
     */
    public function editAction(IssueComment $entity)
    {
        return [
            'entity'    => $entity,
            'edit_form' => $this->createEditForm($entity)->createView(),
        ];
    }

    /**
     * Creates a form to edit a IssueComment entity.
     *
     * @param IssueComment $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(IssueComment $entity)
    {
        $form = $this->createForm(new IssueCommentType(), $entity, [
            'action' => $this->generateUrl('issue_comment_update', ['id' => $entity->getId()]),
            'method' => 'PUT',
        ]);

        $form->add('submit', 'submit', ['label' => 'Update']);

        return $form;
    }

    /**
     * Edits an existing IssueComment entity.
     *
     * @Route("/comment/{id}", name="issue_comment_update")
     * @Method("PUT")
     * @Template("AppBundle:IssueComment:edit.html.twig")
     * @Security("is_granted('edit', entity)")
     */
    public function updateAction(Request $request, IssueComment $entity)
    {
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $url = $this->generateUrl('issue_show', ['code' => $this->getIssueCode($entity->getIssue())]);
            return $this->redirect($url);
        }

        return [
            'entity'    => $entity,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * @param Issue $issue
     * @return string
     */
    public function getIssueCode(Issue $issue)
    {
        return $this->get('app.issue_code_converter')->getCode($issue);
    }
}

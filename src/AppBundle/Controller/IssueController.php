<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Issue;

/**
 * Issue controller.
 */
class IssueController extends Controller
{
    /**
     * Creates a new Issue entity.
     *
     * @Route("/project/{code}/issue", name="issue_create")
     * @Method("POST")
     * @Template("AppBundle:Issue:new.html.twig")
     * @Security("is_granted('create_issue') and is_granted('view', project)")
     */
    public function createAction(Request $request, Project $project)
    {
        $entity = new Issue();
        $entity->setProject($project);
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('issue_show', ['code' => $this->getIssueCode($entity)]));
        }

        return [
            'entity' => $entity,
            'form'   => $form->createView(),
        ];
    }

    /**
     * Creates a form to create a Issue entity.
     *
     * @param Issue $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Issue $entity)
    {
        $form = $this->createForm('appbundle_issue', $entity, [
            'action' => $this->generateUrl('issue_create', ['code' => $entity->getProject()->getCode()]),
            'method' => 'POST',
        ]);

        $form->add('submit', 'submit', ['label' => 'Create']);

        return $form;
    }

    /**
     * Displays a form to create a new Issue entity.
     *
     * @Route("/project/{code}/issue/new", name="issue_new")
     * @Method("GET")
     * @Template()
     * @Security("is_granted('create_issue') and is_granted('view', project)")
     */
    public function newAction(Project $project)
    {
        $entity = new Issue();
        $entity->setProject($project);
        $form = $this->createCreateForm($entity);

        return [
            'entity' => $entity,
            'form'   => $form->createView(),
        ];
    }

    /**
     * Finds and displays a Issue entity.
     *
     * @Route("/issue/{code}", name="issue_show")
     * @Method("GET")
     * @Template()
     * @Security("is_granted('view', entity)")
     */
    public function showAction(Issue $entity)
    {
        return [
            'entity' => $entity,
        ];
    }

    /**
     * Displays a form to edit an existing Issue entity.
     *
     * @Route("/issue/{code}/edit", name="issue_edit")
     * @Method("GET")
     * @Template()
     * @Security("is_granted('edit', entity)")
     */
    public function editAction(Issue $entity)
    {
        return [
            'entity'    => $entity,
            'edit_form' => $this->createEditForm($entity)->createView(),
        ];
    }

    /**
     * Creates a form to edit a Issue entity.
     *
     * @param Issue $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Issue $entity)
    {
        $form = $this->createForm('appbundle_issue', $entity, [
            'action' => $this->generateUrl('issue_update', ['code' => $this->getIssueCode($entity)]),
            'method' => 'PUT',
        ]);

        $form->add('submit', 'submit', ['label' => 'Update']);

        return $form;
    }

    /**
     * Edits an existing Issue entity.
     *
     * @Route("/issue/{code}", name="issue_update")
     * @Method("PUT")
     * @Template("AppBundle:Issue:edit.html.twig")
     * @Security("is_granted('edit', entity)")
     */
    public function updateAction(Request $request, Issue $entity)
    {
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('issue_show', ['code' => $this->getIssueCode($entity)]));
        }

        return [
            'entity'    => $entity,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * @param Issue $entity
     * @return string
     */
    private function getIssueCode(Issue $entity)
    {
        return $this->get('app.issue_code_converter')->getCode($entity);
    }
}

<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Issue;
use AppBundle\Form\IssueType;

/**
 * Issue controller.
 *
 * @Route("/issue")
 */
class IssueController extends Controller
{

    /**
     * Lists all Issue entities.
     *
     * @Route("/", name="issue")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Issue')->findAll();

        return [
            'entities' => $entities,
        ];
    }

    /**
     * Creates a new Issue entity.
     *
     * @Route("/", name="issue_create")
     * @Method("POST")
     * @Template("AppBundle:Issue:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Issue();
        $form   = $this->createCreateForm($entity);
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
        $form = $this->createForm(new IssueType(), $entity, [
            'action' => $this->generateUrl('issue_create'),
            'method' => 'POST',
        ]);

        $form->add('submit', 'submit', ['label' => 'Create']);

        return $form;
    }

    /**
     * Displays a form to create a new Issue entity.
     *
     * @Route("/new", name="issue_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Issue();
        $form   = $this->createCreateForm($entity);

        return [
            'entity' => $entity,
            'form'   => $form->createView(),
        ];
    }

    /**
     * Finds and displays a Issue entity.
     *
     * @Route("/{code}", name="issue_show")
     * @Method("GET")
     * @Template()
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
     * @Route("/{code}/edit", name="issue_edit")
     * @Method("GET")
     * @Template()
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
        $form = $this->createForm(new IssueType(), $entity, [
            'action' => $this->generateUrl('issue_update', ['code' => $this->getIssueCode($entity)]),
            'method' => 'PUT',
        ]);

        $form->add('submit', 'submit', ['label' => 'Update']);

        return $form;
    }

    /**
     * Edits an existing Issue entity.
     *
     * @Route("/{code}", name="issue_update")
     * @Method("PUT")
     * @Template("AppBundle:Issue:edit.html.twig")
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

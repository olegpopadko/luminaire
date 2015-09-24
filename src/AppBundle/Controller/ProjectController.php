<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Project;
use AppBundle\Form\ProjectType;

/**
 * Project controller.
 *
 * @Route("/project")
 */
class ProjectController extends Controller
{

    /**
     * Lists all Project entities.
     *
     * @Route("/", name="project")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $findForm = $this->createFindForm();
        $findForm->handleRequest($request);

        $formData = $findForm->getData();

        $queryBuilder = $em->getRepository('AppBundle:Project')->createQueryBuilder('p')
            ->where('p.code like :q or p.label like :q or p.summary like :q')
            ->setParameter('q', '%' . $formData['q'] . '%');
        $this->get('app.project_filter')->apply($queryBuilder);

        return [
            'find_form' => $findForm->createView(),
            'entities'  => $queryBuilder->getQuery()->execute(),
        ];
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    private function createFindForm()
    {
        return $this->get('form.factory')->createNamedBuilder('', 'form', ['q' => null], [
            'action' => $this->generateUrl('project'),
            'method' => 'GET',
        ])->add('q', 'text', ['required' => false])
            ->add('submit', 'submit')
            ->getForm();
    }

    /**
     * Creates a new Project entity.
     *
     * @Route("/", name="project_create")
     * @Method("POST")
     * @Template("AppBundle:Project:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $this->denyAccessUnlessGranted('create_project');

        $entity = new Project();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $entity->setCode($this->get('app.name_converter')->toAcronym($entity->getLabel()));

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('project_show', ['code' => $entity->getCode()]));
        }

        return [
            'entity' => $entity,
            'form'   => $form->createView(),
        ];
    }

    /**
     * Creates a form to create a Project entity.
     *
     * @param Project $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Project $entity)
    {
        $form = $this->createForm(new ProjectType(), $entity, [
            'action' => $this->generateUrl('project_create'),
            'method' => 'POST',
        ]);

        $form->add('submit', 'submit', ['label' => 'Create']);

        return $form;
    }

    /**
     * Displays a form to create a new Project entity.
     *
     * @Route("/new", name="project_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $this->denyAccessUnlessGranted('create_project');

        $entity = new Project();
        $form = $this->createCreateForm($entity);

        return [
            'entity' => $entity,
            'form'   => $form->createView(),
        ];
    }

    /**
     * Finds and displays a Project entity.
     *
     * @Route("/{code}", name="project_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($code)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Project')->findOneByCode($code);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        $this->denyAccessUnlessGranted('view', $entity);

        return [
            'entity' => $entity,
        ];
    }

    /**
     * Displays a form to edit an existing Project entity.
     *
     * @Route("/{code}/edit", name="project_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($code)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Project')->findOneByCode($code);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        $this->denyAccessUnlessGranted('edit', $entity);

        $editForm = $this->createEditForm($entity);

        return [
            'entity'    => $entity,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * Creates a form to edit a Project entity.
     *
     * @param Project $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Project $entity)
    {
        $form = $this->createForm(new ProjectType(), $entity, [
            'action' => $this->generateUrl('project_update', ['code' => $entity->getCode()]),
            'method' => 'PUT',
        ]);

        $form->add('submit', 'submit', ['label' => 'Update']);

        return $form;
    }

    /**
     * Edits an existing Project entity.
     *
     * @Route("/{code}", name="project_update")
     * @Method("PUT")
     * @Template("AppBundle:Project:edit.html.twig")
     */
    public function updateAction(Request $request, $code)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Project')->findOneByCode($code);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        $this->denyAccessUnlessGranted('edit', $entity);

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('project_show', ['code' => $code]));
        }

        return [
            'entity'    => $entity,
            'edit_form' => $editForm->createView(),
        ];
    }
}

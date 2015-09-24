<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Project;

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
        $this->get('app.security.project_filter')->apply($queryBuilder);

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
        $form = $this->get('form.factory')->createNamed('', 'form', ['q' => null], [
            'action' => $this->generateUrl('project'),
            'method' => 'GET',
        ]);
        $form->add('q', 'text', ['required' => false])
            ->add('submit', 'submit');

        return $form;
    }

    /**
     * Creates a new Project entity.
     *
     * @Route("/", name="project_create")
     * @Method("POST")
     * @Template("AppBundle:Project:new.html.twig")
     * @Security("is_granted('create_project')")
     */
    public function createAction(Request $request)
    {
        $entity = new Project();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

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
        $form = $this->container->get('form.factory')->create('appbundle_project', $entity, [
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
     * @Security("is_granted('create_project')")
     */
    public function newAction()
    {
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
     * @Security("is_granted('view', entity)")
     */
    public function showAction(Project $entity)
    {
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
     * @Security("is_granted('edit', entity)")
     */
    public function editAction(Project $entity)
    {
        return [
            'entity'    => $entity,
            'edit_form' => $this->createEditForm($entity)->createView(),
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
        $form = $this->container->get('form.factory')->create('appbundle_project', $entity, [
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
     * @Security("is_granted('edit', entity)")
     */
    public function updateAction(Request $request, Project $entity)
    {
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('project_show', ['code' => $entity->getCode()]));
        }

        return [
            'entity'    => $entity,
            'edit_form' => $editForm->createView(),
        ];
    }
}

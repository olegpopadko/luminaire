<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Activity;

/**
 * Class ActivityController
 *
 * @Route("/activity")
 */
class ActivityController extends Controller
{
    /**
     * @Route("/", name="activity")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $filter = $this->get('app.activity_extractor_factory')->create();

        if ($request->get('project_id')) {
            $project = $em->getReference('AppBundle:Project', $request->get('project_id'));
            $filter->whereProject($project);
        }

        if ($request->get('assignee_id')) {
            $user = $em->getReference('AppBundle:User', $request->get('assignee_id'));
            $filter->whereUserIsAssigned($user);
        }

        return [
            'entities' => $filter->getResults(),
        ];
    }

    /**
     * @Route("/{id}", name="activity_show")
     * @Template()
     */
    public function showAction(Activity $entity)
    {
        return [
            'entity' => $entity,
        ];
    }
}

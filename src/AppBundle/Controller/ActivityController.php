<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

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

        if ($request->get('member_id')) {
            $user = $em->getReference('AppBundle:User', $request->get('member_id'));
            $filter->whereUserIsMember($user);
        }

        if ($request->get('issue_id')) {
            $issue = $em->getReference('AppBundle:Issue', $request->get('issue_id'));
            $filter->whereIssue($issue);
        }

        if ($request->get('limit')) {
            $filter->setMaxResults($request->get('limit'));
        }

        return [
            'entities' => $filter->getResults(),
        ];
    }
}

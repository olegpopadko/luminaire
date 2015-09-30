<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Activity;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

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

        if ($request->get('project_code')) {
            $project = $em->getRepository('AppBundle:Project')->findOneByCode($request->get('project_code'));
            $filter->whereProject($project);
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

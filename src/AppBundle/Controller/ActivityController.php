<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Activity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
    public function indexAction()
    {
        return [
            'entities' => $this->getDoctrine()->getManager()->getRepository('AppBundle:Activity')->findAll(),
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

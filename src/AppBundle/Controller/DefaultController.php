<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction()
    {
        return [
            'collaborators_issues' => $this->getCollaboratorsIssues(),
        ];
    }

    /**
     * Get issues where user is collaborator
     */
    private function getCollaboratorsIssues()
    {
        $em           = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:Issue')->createQueryBuilder('i')
            ->innerJoin('i.collaborators', 'c')
            ->where('c = :user')
            ->setParameter('user', $this->getUser())
            ->andWhere('i.status not in  (:statuses)')
            ->setParameter(
                'statuses',
                [
                    $em->getRepository('AppBundle:IssueStatus')->findClosed(),
                    $em->getRepository('AppBundle:IssueStatus')->findResolved(),
                ]
            )
            ->orderBy('i.updatedAt', 'DESC');
        $this->get('app.security.issue_filter')->apply($queryBuilder);

        return $queryBuilder->getQuery()->execute();
    }
}

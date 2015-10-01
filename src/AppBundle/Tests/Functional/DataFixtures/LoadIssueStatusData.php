<?php

namespace AppBundle\Tests\Functional\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\IssueStatus;

/**
 * Class LoadIssueStatusData
 */
class LoadIssueStatusData extends AbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach (['Open', 'In progress', 'Closed', 'Resolved', 'Reopened'] as $label) {
            $entity = new IssueStatus();
            $entity->setLabel($label);
            $manager->persist($entity);
            $this->addReference(strtolower($label) . '-issue-status', $entity);
        }
        $manager->flush();
    }
}

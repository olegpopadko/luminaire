<?php

namespace AppBundle\Tests\Functional\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\IssueType;

/**
 * Class LoadIssueData
 */
class LoadIssueTypeData extends AbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach (['Bug', 'Subtask', 'Task', 'Story'] as $label) {
            $entity = new IssueType();
            $entity->setLabel($label);
            $manager->persist($entity);
            $this->addReference(strtolower($label) . '-issue-type', $entity);
        }
        $manager->flush();
    }
}

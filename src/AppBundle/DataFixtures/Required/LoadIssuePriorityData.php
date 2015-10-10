<?php

namespace AppBundle\DataFixtures\Required;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\IssuePriority;

/**
 * Class LoadIssuePriorityData
 */
class LoadIssuePriorityData extends AbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach (['Major', 'Blocker', 'Critical', 'Minor', 'Trivial'] as $label) {
            $entity = new IssuePriority();
            $entity->setLabel($label);
            $manager->persist($entity);
            $this->addReference(strtolower($label) . '-issue-priority', $entity);
        }
        $manager->flush();
    }
}

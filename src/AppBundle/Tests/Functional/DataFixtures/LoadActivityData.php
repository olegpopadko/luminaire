<?php

namespace AppBundle\Tests\Functional\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Activity;

class LoadActivityData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $entity = new Activity();
        $entity->setUser($this->getReference('operator-user'));
        $entity->setIssue($this->getReference('test-issue'));
        $entity->setChanges([
            'type' => 'issue_created',
            'entity_id' => $this->getReference('test-issue')->getId(),
            'entity_class' => get_class($this->getReference('test-issue')),
        ]);
        $manager->persist($entity);
        $this->addReference('test-activity', $entity);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            'AppBundle\Tests\Functional\DataFixtures\LoadIssueData',
            'AppBundle\Tests\Functional\DataFixtures\LoadUserData',
        ];
    }
}

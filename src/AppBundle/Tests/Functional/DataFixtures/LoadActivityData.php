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
        $issue = $this->getReference('operator-issue');
        $entity = new Activity();
        $entity->setUser($this->getReference('operator-user'));
        $entity->setIssue($issue);
        $entity->setChanges([
            'type' => 'issue_created',
            'entity_id' => $issue->getId(),
            'entity_class' => get_class($issue),
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

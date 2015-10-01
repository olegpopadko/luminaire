<?php

namespace AppBundle\Tests\Functional\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Issue;

class LoadIssueData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $entity = new Issue();
        $entity->setCode(1);
        $entity->setSummary('Test issue summary');
        $entity->setDescription('Test issue description');
        $entity->setProject($this->getReference('test-operator-project'));
        $entity->setAssignee($this->getReference('operator-user'));
        $entity->setReporter($this->getReference('operator-user'));
        $entity->setStatus($this->getReference('open-issue-status'));
        $entity->setPriority($this->getReference('major-issue-priority'));
        $entity->setType($this->getReference('task-issue-type'));
        $manager->persist($entity);
        $this->addReference('test-issue', $entity);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            'AppBundle\Tests\Functional\DataFixtures\LoadProjectData',
            'AppBundle\Tests\Functional\DataFixtures\LoadUserData',
        ];
    }
}

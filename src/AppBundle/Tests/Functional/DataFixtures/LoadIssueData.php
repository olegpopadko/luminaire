<?php

namespace AppBundle\Tests\Functional\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Issue;

class LoadIssueData extends AbstractFixture implements DependentFixtureInterface
{
    private function getData()
    {
        return [
            [
                'code'           => 1,
                'summary'        => 'Test operator issue summary',
                'description'    => 'Test operator issue description',
                'project'        => 'test-operator-project',
                'assignee'       => 'operator-user',
                'reporter'       => 'operator-user',
                'status'         => 'open-issue-status',
                'priority'       => 'major-issue-priority',
                'type'           => 'task-issue-type',
                'reference_name' => 'operator',
            ],
            [
                'code'           => 2,
                'summary'        => 'Test operator1 issue summary',
                'description'    => 'Test operator1 issue description',
                'project'        => 'test-operator-project',
                'assignee'       => 'operator1-user',
                'reporter'       => 'operator1-user',
                'status'         => 'closed-issue-status',
                'priority'       => 'major-issue-priority',
                'type'           => 'task-issue-type',
                'reference_name' => 'operator1',
            ],
            [
                'code'           => 1,
                'summary'        => 'Test manager issue summary',
                'description'    => 'Test manager issue description',
                'project'        => 'test-manager-project',
                'assignee'       => 'manager-user',
                'reporter'       => 'manager-user',
                'status'         => 'open-issue-status',
                'priority'       => 'major-issue-priority',
                'type'           => 'task-issue-type',
                'reference_name' => 'manager',
            ],
            [
                'code'           => 2,
                'summary'        => 'Test manager1 issue summary',
                'description'    => 'Test manager1 issue description',
                'project'        => 'test-manager-project',
                'assignee'       => 'manager1-user',
                'reporter'       => 'manager1-user',
                'status'         => 'open-issue-status',
                'priority'       => 'major-issue-priority',
                'type'           => 'task-issue-type',
                'reference_name' => 'manager1',
            ],
            [
                'code'           => 1,
                'summary'        => 'Test admin issue summary',
                'description'    => 'admin Test issue description',
                'project'        => 'test-admin-project',
                'assignee'       => 'admin-user',
                'reporter'       => 'admin-user',
                'status'         => 'open-issue-status',
                'priority'       => 'major-issue-priority',
                'type'           => 'task-issue-type',
                'reference_name' => 'admin',
            ],
            [
                'code'           => 2,
                'summary'        => 'Test admin1 issue summary',
                'description'    => 'Test admin1 issue description',
                'project'        => 'test-admin-project',
                'assignee'       => 'admin1-user',
                'reporter'       => 'admin1-user',
                'status'         => 'open-issue-status',
                'priority'       => 'major-issue-priority',
                'type'           => 'task-issue-type',
                'reference_name' => 'admin1',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->getData() as $data) {
            $entity = new Issue();
            $entity->setCode($data['code']);
            $entity->setSummary($data['summary']);
            $entity->setDescription($data['description']);
            $entity->setProject($this->getReference($data['project']));
            $entity->setAssignee($this->getReference($data['assignee']));
            $entity->setReporter($this->getReference($data['reporter']));
            $entity->setStatus($this->getReference($data['status']));
            $entity->setPriority($this->getReference($data['priority']));
            $entity->setType($this->getReference($data['type']));
            $manager->persist($entity);
            $this->addReference($data['reference_name'] . '-issue', $entity);
        }
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

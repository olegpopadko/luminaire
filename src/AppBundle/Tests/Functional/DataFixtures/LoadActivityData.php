<?php

namespace AppBundle\Tests\Functional\DataFixtures;

use AppBundle\Entity\Issue;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Activity;

class LoadActivityData extends AbstractFixture implements DependentFixtureInterface
{
    private function getData()
    {
        $activities = [];
        $issues     = [
            $this->getReference('operator-issue'),
            $this->getReference('operator1-issue'),
        ];
        foreach ($issues as $issue) {
            $activities = array_merge($activities, [
                [
                    'user'    => 'operator-user',
                    'issue'   => $issue,
                    'changes' => [
                        'type'         => 'issue_created',
                        'entity_id'    => $issue->getId(),
                        'entity_class' => get_class($issue),
                    ],
                ],
                [
                    'user'    => 'operator-user',
                    'issue'   => $issue,
                    'changes' => [
                        'type'         => 'issue_comment_created',
                        'entity_id'    => $issue->getId(),
                        'entity_class' => get_class($issue),
                    ],
                ],
                [
                    'user'    => 'operator-user',
                    'issue'   => $issue,
                    'changes' => [
                        'type'         => 'issue_status_changed',
                        'old_status'   => 'Status',
                        'new_status'   => 'New Status',
                        'entity_id'    => $issue->getId(),
                        'entity_class' => get_class($issue),
                    ],
                ],
            ]);
        }
        return $activities;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->getData() as $data) {
            $entity = new Activity();
            $entity->setUser($this->getReference($data['user']));
            $entity->setIssue($data['issue']);
            $entity->setChanges($data['changes']);
            $manager->persist($entity);
        }
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

<?php

namespace AppBundle\DataFixtures\Sample;

use AppBundle\Utils\ActivityNotification;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Issue;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class LoadIssueData
 */
class LoadIssueData extends AbstractFixture implements DependentFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->disableNotificationEvent();

        $project = 1;
        $code    = 1;
        $number  = 1;
        foreach (json_decode(file_get_contents(__DIR__ . '/json/issue.json'), true) as $data) {
            $user  = $this->getReference($data['reporter'] . '-user');
            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->container->get('security.token_storage')->setToken($token);
            if ($project !== intval($data['project'])) {
                $project = intval($data['project']);
                $code    = 1;
            }
            $entity = new Issue();
            $entity->setCode($code);
            $entity->setSummary($data['summary']);
            $entity->setDescription($data['description']);
            $entity->setProject($this->getReference($data['project'] . '-project'));
            if (!empty($data['assignee'])) {
                $entity->setAssignee($this->getReference($data['assignee'] . '-user'));
            }
            $entity->setReporter($this->getReference($data['reporter'] . '-user'));
            $entity->setStatus($this->getReference('open-issue-status'));
            $entity->setPriority($this->getReference($data['priority'] . '-issue-priority'));
            $entity->setType($this->getReference($data['type'] . '-issue-type'));
            $manager->persist($entity);
            $this->generateStatusChanged($data['status'], $entity, $manager);
            $this->addReference($number . '-issue', $entity);
            $number++;
            $code++;
        }
        $manager->flush();
    }

    /**
     * @param $status
     * @param $entity
     * @param ObjectManager $manager
     */
    private function generateStatusChanged($status, $entity, ObjectManager $manager)
    {
        if ($status === 'closed') {
            $manager->flush();
            $entity->setStatus($this->getReference('in-progress-issue-status'));
            $manager->persist($entity);
            $manager->flush($entity);
            $entity->setStatus($this->getReference('closed-issue-status'));
            $manager->persist($entity);
            $manager->flush($entity);
        }
    }

    /**
     *
     */
    private function disableNotificationEvent()
    {
        $eventDispatcher      = $this->container->get('event_dispatcher');
        $activityNotification = null;
        $listeners            = $eventDispatcher->getListeners('app.events.activity_created');
        foreach ($listeners as $wrappedListeners) {
            foreach ($wrappedListeners as $listener) {
                if ($listener instanceof ActivityNotification) {
                    $activityNotification = $listener;
                    break 2;
                }
            }
        }

        if ($activityNotification) {
            $callable = [$activityNotification, 'onActivityCreated'];
            $eventDispatcher->removeListener('app.events.activity_created', $callable);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            'AppBundle\DataFixtures\Required\LoadIssuePriorityData',
            'AppBundle\DataFixtures\Required\LoadIssueResolutionData',
            'AppBundle\DataFixtures\Required\LoadIssueStatusData',
            'AppBundle\DataFixtures\Required\LoadIssueTypeData',
            'AppBundle\DataFixtures\Sample\LoadProjectData',
            'AppBundle\DataFixtures\Sample\LoadUserData',
        ];
    }
}

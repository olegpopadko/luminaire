<?php

namespace AppBundle\DataFixtures\Sample;

use AppBundle\Entity\IssueComment;
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
class LoadIssueCommentData extends AbstractFixture implements DependentFixtureInterface, ContainerAwareInterface
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
        $issueCount = count(json_decode(file_get_contents(__DIR__ . '/json/issue.json'), true));
        $comments   = json_decode(file_get_contents(__DIR__ . '/json/comment.json'), true);
        for ($i = 1; $i <= $issueCount; $i++) {
            $issue  = $this->getReference($i . '-issue');
            $number = 1;
            foreach ($comments as $data) {
                $entity = new IssueComment();
                $entity->setIssue($issue);
                $entity->setUser($this->getRandomUser($issue));
                $entity->setBody($data);
                if ($number > 1 && rand(0, 1)) {
                    $entity->setParent($this->getReference($i . '-issue-' . ($number - 1) . '-comment'));
                }
                $manager->persist($entity);
                $this->setReference($i . '-issue-' . $number . '-comment', $entity);
                $number++;
            }
        }
        $manager->flush();
    }

    /**
     * @param Issue $issue
     * @return mixed
     */
    private function getRandomUser(Issue $issue)
    {
        $users = $issue->getProject()->getUsers()->toArray();
        return $users[array_rand($users)];
    }

    /**
     * @return array
     */
    public function getDependencies()
    {
        return [
            'AppBundle\DataFixtures\Sample\LoadProjectData',
            'AppBundle\DataFixtures\Sample\LoadUserData',
            'AppBundle\DataFixtures\Sample\LoadIssueData',
        ];
    }
}

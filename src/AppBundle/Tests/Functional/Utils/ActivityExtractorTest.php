<?php

namespace AppBundle\Tests\Functional\Utils;

use AppBundle\Tests\Functional\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ActivityExtractorTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->setToken();
    }

    public function testActivityExtractorFactory()
    {
        $this->assertEquals(
            'AppBundle\Utils\ActivityExtractor',
            get_class($this->get('app.activity_extractor_factory')->create())
        );
    }

    public function testActivityExtractor()
    {
        $activityExtractor = $this->get('app.activity_extractor_factory')->create();

        $results = $activityExtractor->getResults();
        $this->assertNotEmpty($results);
        $user = $this->getManager()->getRepository('AppBundle:User')->find($this->getUser()->getId());
        foreach ($results as $activity) {
            $this->assertTrue($user->hasProject($activity->getIssue()->getProject()));
        }
    }

    public function testActivityExtractorWhereIssue()
    {
        $activityExtractor = $this->get('app.activity_extractor_factory')->create();

        $issue   = $this->getReference('operator1-issue');
        $results = $activityExtractor->whereIssue($issue)->getResults();
        $this->assertNotEmpty($results);
        foreach ($results as $activity) {
            $this->assertEquals($issue->getId(), $activity->getIssue()->getId());
        }
    }

    public function testActivityExtractorWhereProject()
    {
        $activityExtractor = $this->get('app.activity_extractor_factory')->create();

        $project = $this->getReference('test-operator-project');
        $results = $activityExtractor->whereProject($project)->getResults();
        $this->assertNotEmpty($results);
        foreach ($results as $activity) {
            $this->assertEquals($project->getId(), $activity->getIssue()->getProject()->getId());
        }
    }

    public function testActivityExtractorWhereUserIsAssigned()
    {
        $activityExtractor = $this->get('app.activity_extractor_factory')->create();

        $user    = $this->getReference('operator1-user');
        $results = $activityExtractor->whereUserIsAssigned($user)->getResults();
        $this->assertNotEmpty($results);
        foreach ($results as $activity) {
            $this->assertEquals($user->getId(), $activity->getIssue()->getAssignee()->getId());
        }
    }

    public function testActivityExtractorWhereUserIsMember()
    {
        $activityExtractor = $this->get('app.activity_extractor_factory')->create();

        $user    = $this->getReference('operator1-user');
        $results = $activityExtractor->whereUserIsMember($user)->getResults();
        $this->assertNotEmpty($results);
        $user = $this->getManager()->getRepository('AppBundle:User')->find($user->getId());
        foreach ($results as $activity) {
            $this->assertTrue($user->hasProject($activity->getIssue()->getProject()));
        }
    }

    public function testActivityExtractorWhereUserIsAuthor()
    {
        $activityExtractor = $this->get('app.activity_extractor_factory')->create();

        $user    = $this->getReference('operator-user');
        $results = $activityExtractor->whereUserIsAuthor($user)->getResults();
        $this->assertNotEmpty($results);
        $user = $this->getManager()->getRepository('AppBundle:User')->find($user->getId());
        foreach ($results as $activity) {
            $this->assertEquals($user->getId(), $activity->getUser()->getId());
        }
    }

    public function testActivityExtractorOnlyOpenedIssue()
    {
        $activityExtractor = $this->get('app.activity_extractor_factory')->create();

        $results = $activityExtractor->onlyOpenedIssue()->getResults();
        $this->assertNotEmpty($results);
        foreach ($results as $activity) {
            $this->assertNotEquals('Closed', $activity->getIssue()->getStatus()->getLabel());
            $this->assertNotEquals('Resolved', $activity->getIssue()->getStatus()->getLabel());
        }
    }

    private function setToken()
    {
        $user     = $this->getUser();
        $firewall = 'main';
        $token    = new UsernamePasswordToken($user, null, $firewall, $user->getRoles());
        $this->get('security.token_storage')->setToken($token);
    }

    private function getUser()
    {
        return $this->getReference('operator-user');
    }
}

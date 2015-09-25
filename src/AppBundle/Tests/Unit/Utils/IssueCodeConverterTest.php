<?php

namespace AppBundle\Tests\Unit\Utils;

use AppBundle\Entity\Issue;
use AppBundle\Entity\Project;
use AppBundle\Tests\Unit\TestCase;
use AppBundle\Utils\IssueCodeConverter;

class IssueCodeConverterTest extends TestCase
{
    public function testToAcronym()
    {
        /** @var \Doctrine\Common\Persistence\ObjectManager $objectManager */
        $objectManager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $issueCodeConverter = new IssueCodeConverter($objectManager);

        $project = new Project();
        $project->setCode('project');

        $entity = new Issue();
        $entity->setProject($project);
        $entity->setCode('1');

        $this->assertEquals('project-1', $issueCodeConverter->getCode($entity));

        $project->setCode(2);
        $entity->setCode(1);
        $this->assertEquals('2-1', $issueCodeConverter->getCode($entity));
    }
}

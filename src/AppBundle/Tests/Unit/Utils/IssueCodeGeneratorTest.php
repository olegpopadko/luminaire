<?php

namespace AppBundle\Tests\Unit\Utils;

use AppBundle\Tests\Unit\TestCase;
use AppBundle\Utils\IssueCodeGenerator;

class IssueCodeGeneratorTest extends TestCase
{
    /**
     * @dataProvider data
     */
    public function testGetCode($dbResult, $result)
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Doctrine\Common\Persistence\ObjectManager $objectManager */
        $objectManager      = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $issueCodeGenerator = new IssueCodeGenerator($objectManager);

        $repository = $this->getMockBuilder('AppBundle\Entity\IssueRepository')
            ->disableOriginalConstructor()->getMock();

        $project = $this->getMock('AppBundle\Entity\Project');
        /** @var \PHPUnit_Framework_MockObject_MockObject|\AppBundle\Entity\Issue $issue */
        $issue   = $this->getMockBuilder('AppBundle\Entity\Issue')
            ->disableOriginalConstructor()
            ->getMock();

        $issue->expects($this->once())
            ->method('getProject')
            ->willReturn($project);

        $objectManager->expects($this->once())
            ->method('getRepository')
            ->with('AppBundle:Issue')
            ->willReturn($repository);

        $repository->expects($this->once())
            ->method('findOneByProjectAndOrderByCode')
            ->with($project)
            ->willReturn($dbResult);

        $this->assertEquals($result, $issueCodeGenerator->getCode($issue));
    }

    public function data()
    {
        $issueResult = $this->getMockBuilder('AppBundle\Entity\Issue')
            ->disableOriginalConstructor()
            ->getMock();

        $issueResult->expects($this->once())
            ->method('getCode')
            ->willReturn('5');
        return [
            [null, 1],
            [$issueResult, 6],
        ];
    }
}

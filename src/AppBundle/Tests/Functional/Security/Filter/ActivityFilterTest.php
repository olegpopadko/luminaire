<?php

namespace AppBundle\Tests\Functional\Security\Filter;

use AppBundle\Security\Filter\ActivityFilter;
use AppBundle\Tests\Functional\TestCase;

class ActivityFilterTest extends TestCase
{
    public function testProjectFilterApply()
    {
        $operator = $this->logInOperator();
        $queryBuilder = $this->getManager()->getRepository('AppBundle:Activity')->createQueryBuilder('a');

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->once())
            ->method('getUser')
            ->willReturn($operator);

        $tokenStorage = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage');
        $tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $service = new ActivityFilter($tokenStorage);

        $service->apply($queryBuilder);

        $result = $queryBuilder->getQuery()->execute();

        $this->assertNotEmpty($result);
        foreach ($result as $activity) {
            $this->assertContains($operator, $activity->getIssue()->getProject()->getUsers());
        }
    }
}

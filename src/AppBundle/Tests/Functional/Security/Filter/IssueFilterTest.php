<?php

namespace AppBundle\Tests\Functional\Security\Filter;

use AppBundle\Security\Filter\IssueFilter;
use AppBundle\Tests\Functional\TestCase;

class IssueFilterTest extends TestCase
{
    public function testIssueFilterApply()
    {
        $operator = $this->logInOperator();
        $queryBuilder = $this->getManager()->getRepository('AppBundle:Issue')->createQueryBuilder('i');

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->once())
            ->method('getUser')
            ->willReturn($operator);

        $tokenStorage = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage');
        $tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $service = new IssueFilter($tokenStorage);

        $service->apply($queryBuilder);

        $result = $queryBuilder->getQuery()->execute();

        $this->assertNotEmpty($result);
        foreach ($result as $issue) {
            $this->assertContains($operator, $issue->getProject()->getUsers());
        }
    }
}

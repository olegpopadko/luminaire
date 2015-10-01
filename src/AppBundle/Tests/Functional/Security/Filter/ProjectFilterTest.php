<?php

namespace AppBundle\Tests\Functional\Security\Filter;

use AppBundle\Security\Filter\ProjectFilter;
use AppBundle\Tests\Functional\TestCase;

class ProjectFilterTest extends TestCase
{
    public function testProjectFilterApply()
    {
        $operator = $this->logInOperator();
        $queryBuilder = $this->getManager()->getRepository('AppBundle:Project')->createQueryBuilder('p');

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->once())
            ->method('getUser')
            ->willReturn($operator);

        $tokenStorage = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage');
        $tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $service = new ProjectFilter($tokenStorage);

        $service->apply($queryBuilder);

        $result = $queryBuilder->getQuery()->execute();

        $this->assertNotEmpty($result);
        foreach ($result as $project) {
            $this->assertContains($operator, $project->getUsers());
        }
    }
}

<?php

namespace AppBundle\Tests\Functional\Controller\DataFixtures;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;

class LoadUserData extends AbstractFixture implements ContainerAwareInterface, DependentFixtureInterface
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
        foreach (['operator', 'manager', 'admin'] as $role) {
            $entity = new User();
            $entity->setUsername($role);
            $entity->setEmail($role . '@test');
            $entity->setFullName(ucfirst($role));
            $entity->setTimezone('Europe/Kiev');
            $entity->setPassword($this->container->get('security.password_encoder')->encodePassword($entity, 'secret'));

            /** @var \AppBundle\Entity\Role $roleEntity */
            $roleEntity = $this->getReference($role . '-role');
            $entity->addRole($roleEntity);

            $manager->persist($entity);
            $this->addReference($role . '-user', $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return ['AppBundle\Tests\Functional\Controller\DataFixtures\LoadRoleData'];
    }
}

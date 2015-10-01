<?php

namespace AppBundle\DataFixtures\Required;

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
        $entity = new User();
        $entity->setUsername('admin');
        $entity->setEmail('admin@luminaire');
        $entity->setFullName('Admin');
        $entity->setTimezone('UTC');
        $password = $this->container->get('security.password_encoder')->encodePassword($entity, 'admin');
        $entity->setPassword($password);

        /** @var \AppBundle\Entity\Role $roleEntity */
        $roleEntity = $this->getReference('admin-role');
        $entity->addRole($roleEntity);
        $manager->persist($entity);

        $manager->flush();
    }

    public function getDependencies()
    {
        return ['AppBundle\DataFixtures\Required\LoadRoleData'];
    }
}

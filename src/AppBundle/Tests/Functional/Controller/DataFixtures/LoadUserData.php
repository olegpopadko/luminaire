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
        for ($i = 0; $i < 2; $i++) {
            foreach (['operator', 'manager', 'admin'] as $role) {
                $username = $i ? $role . $i : $role;
                $entity = new User();
                $entity->setUsername($username);
                $entity->setEmail($username . '@test.com');
                $entity->setFullName(ucfirst($username));
                $entity->setTimezone('Europe/Kiev');
                $password = $this->container->get('security.password_encoder') ->encodePassword($entity, $username);
                $entity->setPassword($password);

                /** @var \AppBundle\Entity\Role $roleEntity */
                $roleEntity = $this->getReference($role . '-role');
                $entity->addRole($roleEntity);

                $manager->persist($entity);
                $this->addReference($username . '-user', $entity);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return ['AppBundle\Tests\Functional\Controller\DataFixtures\LoadRoleData'];
    }
}

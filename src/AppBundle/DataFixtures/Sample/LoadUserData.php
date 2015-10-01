<?php

namespace AppBundle\DataFixtures\Sample;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
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
        $number = 1;
        foreach (json_decode(file_get_contents(__DIR__ . '/json/user.json'), true) as $data) {
            $entity = new User();
            $entity->setUsername($data['username']);
            $entity->setEmail($data['email']);
            $entity->setFullName($data['full_name']);
            $entity->setTimezone($data['timezone']);
            $password = $this->container->get('security.password_encoder')->encodePassword($entity, $data['password']);
            $entity->setPassword($password);

            /** @var \AppBundle\Entity\Role $roleEntity */
            $roleEntity = $this->getReference($data['role'] . '-role');
            $entity->addRole($roleEntity);

            $manager->persist($entity);
            $this->addReference($number . '-user', $entity);
            $number++;
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            'AppBundle\DataFixtures\Required\LoadUserData',
            'AppBundle\DataFixtures\Required\LoadRoleData',
        ];
    }
}

<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Role;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadRoleData extends AbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach (['ROLE_OPERATOR', 'ROLE_MANAGER', 'ROLE_ADMIN'] as $role) {
            $entity = new Role();
            $entity->setRole($role);
            $manager->persist($entity);
            $this->addReference(strtolower(preg_replace('/^ROLE_/', '', $role)) . '-role', $entity);
        }

        $manager->flush();
    }
}

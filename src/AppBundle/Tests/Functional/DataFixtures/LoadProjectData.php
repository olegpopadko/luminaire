<?php

namespace AppBundle\Tests\Functional\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Project;

class LoadProjectData extends AbstractFixture implements DependentFixtureInterface
{
    private function getData()
    {
        return [
            [
                'label'          => 'Test Project Operator',
                'code'           => 'TP',
                'summary'        => 'Test Project Operator Summary',
                'users'          => [
                    'operator-user',
                    'operator1-user',
                ],
                'reference_name' => 'test-operator',
            ],
            [
                'label'          => 'Test Project Manager',
                'code'           => 'TPM',
                'summary'        => 'Test Project Manager Summary',
                'users'          => [
                    'manager-user'
                ],
                'reference_name' => 'test-manager',
            ],
            [
                'label'          => 'Test Project Admin',
                'code'           => 'TPA',
                'summary'        => 'Test Project Admin',
                'users'          => [
                    'admin-user'
                ],
                'reference_name' => 'test-admin',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->getData() as $data) {
            $entity = new Project();
            $entity->setLabel($data['label']);
            $entity->setCode($data['code']);
            $entity->setSummary($data['summary']);
            foreach ($data['users'] as $user) {
                $entity->addUser($this->getReference($user));
            }
            $manager->persist($entity);
            $this->addReference($data['reference_name'] . '-project', $entity);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            'AppBundle\Tests\Functional\DataFixtures\LoadUserData',
        ];
    }
}

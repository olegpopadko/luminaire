<?php

namespace AppBundle\Tests\Functional\Controller\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Project;

class LoadProjectData extends AbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $entity = new Project();
        $entity->setLabel('Test project');
        $entity->setCode('TP');
        $entity->setSummary('Test project summary');
        $manager->persist($entity);
        $this->addReference('test-project', $entity);
        $manager->flush();
    }
}

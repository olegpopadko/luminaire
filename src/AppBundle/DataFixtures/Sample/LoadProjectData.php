<?php

namespace AppBundle\DataFixtures\Sample;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Project;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadProjectData extends AbstractFixture implements DependentFixtureInterface, ContainerAwareInterface
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
        $codeGenerator = $this->container->get('app.project_code_converter');
        $number = 1;
        foreach (json_decode(file_get_contents(__DIR__ . '/json/project.json'), true) as $data) {
            $entity = new Project();
            $entity->setLabel($data['label']);
            $entity->setSummary($data['summary']);
            foreach (explode('|', $data['users']) as $user) {
                $entity->addUser($this->getReference($user . '-user'));
            }
            $entity->setCode($codeGenerator->getCode($entity));
            $manager->persist($entity);
            $this->addReference($number . '-project', $entity);
            $number++;
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            'AppBundle\DataFixtures\Sample\LoadUserData',
        ];
    }
}

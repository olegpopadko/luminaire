<?php

namespace AppBundle\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\IssueResolution;

/**
 * Class LoadIssueResolutionData
 */
class LoadIssueResolutionData extends AbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $labels = [
            'Fixed',
            'Won\'t Fix',
            'Duplicate',
            'Incomplete',
            'Cannot Reproduce',
            'Done',
            'Won\'t Do',
        ];
        foreach ($labels as $label) {
            $entity = new IssueResolution();
            $entity->setLabel($label);
            $manager->persist($entity);
            $this->addReference(strtolower($label) . '-issue-resolution', $entity);
        }
        $manager->flush();
    }
}

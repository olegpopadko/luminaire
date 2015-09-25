<?php

namespace AppBundle\Entity;

/**
 * Class IssueTypeRepository
 *
 * @method Issue|null findByLabel() findByLabel(string $label)
 */
class IssueTypeRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return Issue|null
     */
    public function findStory()
    {
        return $this->findByLabel('Story');
    }
}

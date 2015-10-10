<?php

namespace AppBundle\Entity;

/**
 * Class IssueStatusRepository
 *
 * @method IssueStatus|null findOneByLabel() findOneByLabel(string $label)
 */
class IssueStatusRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return IssueStatus|null
     */
    public function findClosed()
    {
        return $this->findOneByLabel('Closed');
    }

    /**
     * @return IssueStatus|null
     */
    public function findResolved()
    {
        return $this->findOneByLabel('Resolved');
    }
}

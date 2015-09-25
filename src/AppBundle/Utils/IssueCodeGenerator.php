<?php

namespace AppBundle\Utils;

use AppBundle\Entity\Issue;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class IssueCodeGenerator
 */
class IssueCodeGenerator
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param Issue $entity
     */
    public function getCode(Issue $entity)
    {
        $orderedEntity = $this->objectManager->getRepository('AppBundle:Issue')
            ->findOneByProjectAndOrderByCode($entity->getProject());

        return !is_null($orderedEntity) ? $orderedEntity->getCode() + 1 : 1;
    }
}

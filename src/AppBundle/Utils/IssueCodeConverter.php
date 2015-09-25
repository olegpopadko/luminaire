<?php

namespace AppBundle\Utils;

use AppBundle\Entity\Issue;
use AppBundle\Utils\Exception\UnsupportedIssueCode;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class IssueCodeConverter
 */
class IssueCodeConverter
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
     * @return string
     */
    public function getCode(Issue $entity)
    {
        return $entity->getProject()->getCode() . '-' . $entity->getCode();
    }

    /**
     * @param $code
     * @return mixed
     * @throws UnsupportedIssueCode
     */
    public function find($code)
    {
        $parts = explode('-', $code);
        if (count($parts) !== 2) {
            throw new UnsupportedIssueCode('Unsupported issue code accepted.');
        }
        return $this->objectManager->getRepository('AppBundle:Issue')->findByCodeAndProjectCode($parts[0], $parts[1]);
    }
}

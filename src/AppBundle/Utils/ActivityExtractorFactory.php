<?php

namespace AppBundle\Utils;

use AppBundle\Security\Filter\ActivityFilter;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class ActivityExtractorFactory
 */
class ActivityExtractorFactory
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ActivityFilter
     */
    private $activityFilter;

    /**
     * @param Registry $registry
     */
    public function __construct(Registry $registry, ActivityFilter $activityFilter)
    {
        $this->registry       = $registry;
        $this->activityFilter = $activityFilter;
    }

    /**
     * @return ActivityExtractor
     */
    public function create()
    {
        return new ActivityExtractor($this->registry->getManager(), $this->activityFilter);
    }
}

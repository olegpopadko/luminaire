<?php

namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use AppBundle\Entity\Activity;

/**
 * Class ActivityEvent
 */
class ActivityEvent extends Event
{
    /**
     * @var Activity
     */
    private $activity;

    /**
     * @param Activity $activity
     */
    public function __construct(Activity $activity)
    {
        $this->activity = $activity;
    }

    /**
     * @return Activity
     */
    public function getActivity()
    {
        return $this->activity;
    }
}

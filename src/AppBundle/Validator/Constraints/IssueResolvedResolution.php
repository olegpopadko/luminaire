<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class IssueResolvedResolution
 *
 * @Annotation
 */
class IssueResolvedResolution extends Constraint
{
    /**
     * @var string
     */
    public $message = 'Resolution can be set only for Resolved issue.';

    /**
     * @var string
     */
    public $messageResolution = 'Resolution must be set for Resolved issue.';

    /**
     * @return string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}

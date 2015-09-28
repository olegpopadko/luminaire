<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class IssueSubtaskParent
 *
 * @Annotation
 */
class IssueSubtaskParent extends Constraint
{
    /**
     * @var string
     */
    public $message = 'Parent issue can be set only for Subtask issue.';

    /**
     * @var string
     */
    public $messageEmptyParent = 'Parent issue must be set for Subtask issue.';

    /**
     * @return string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}

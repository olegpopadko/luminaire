<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class IssueSubtaskParentValidator
 */
class IssueSubtaskParentValidator extends ConstraintValidator
{
    /**
     * @param \AppBundle\Entity\Issue $entity
     * @param Constraint $constraint
     */
    public function validate($entity, Constraint $constraint)
    {
        if (!empty($entity->getParent()) && !$entity->getType()->isSubtask()) {
            $this->context->buildViolation($constraint->message)
                ->atPath('parent')
                ->addViolation();
        }

        if (empty($entity->getParent()) && $entity->getType()->isSubtask()) {
            $this->context->buildViolation($constraint->messageEmptyParent)
                ->atPath('parent')
                ->addViolation();
        }
    }
}

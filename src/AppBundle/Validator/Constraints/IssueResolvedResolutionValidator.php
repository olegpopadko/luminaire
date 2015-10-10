<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IssueResolvedResolutionValidator extends ConstraintValidator
{
    /**
     * @param \AppBundle\Entity\Issue $entity
     * @param Constraint $constraint
     */
    public function validate($entity, Constraint $constraint)
    {
        if (!empty($entity->getResolution()) && !$entity->getStatus()->isResolved()) {
            $this->context->buildViolation($constraint->message)
                ->atPath('resolution')
                ->addViolation();
        }

        if (empty($entity->getResolution()) && $entity->getStatus()->isResolved()) {
            $this->context->buildViolation($constraint->messageResolution)
                ->atPath('resolution')
                ->addViolation();
        }
    }
}

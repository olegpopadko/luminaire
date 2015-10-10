<?php

namespace AppBundle\Utils;

use AppBundle\Entity\Project;

/**
 * Class ProjectCodeConverter
 */
class ProjectCodeConverter
{
    /**
     * Converts label of Project entity to acronym
     *
     * @param Project $entity
     * @return string
     */
    public function getCode(Project $entity)
    {
        $string = $entity->getLabel();

        $patterns = [
            '/\p{Lu}/u',
            '/(?<!\p{L})\p{L}/u',
        ];

        $matches = [];

        foreach ($patterns as $pattern) {
            $result = preg_match_all($pattern, $string, $matches);
            if ($result > 0) {
                return mb_strtoupper(implode('', $matches[0]), 'utf-8');
            }
        }

        return null;
    }
}

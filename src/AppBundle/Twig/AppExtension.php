<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Issue;
use AppBundle\Utils\IssueCodeConverter;

/**
 * Class AppExtension
 */
class AppExtension extends \Twig_Extension
{
    /**
     * @var IssueCodeConverter
     */
    private $issueCodeConverter;

    /**
     * @param IssueCodeConverter $issueCodeConverter
     */
    public function __construct(IssueCodeConverter $issueCodeConverter)
    {
        $this->issueCodeConverter = $issueCodeConverter;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('issue_code', [$this, 'getIssueCode']),
        ];
    }

    /**
     * @param Issue $entity
     * @return string
     */
    public function getIssueCode(Issue $entity)
    {
        return $this->issueCodeConverter->getCode($entity);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_extension';
    }
}

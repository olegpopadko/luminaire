<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Issue;
use AppBundle\Templating\Comment\CommentTree;
use AppBundle\Utils\IssueCodeConverter;
use Doctrine\Common\Collections\Collection;

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
     * @var CommentTree
     */
    private $commentTree;

    /**
     * @var \Twig_Environment
     */
    private $environment;

    /**
     * @param IssueCodeConverter $issueCodeConverter
     */
    public function __construct(IssueCodeConverter $issueCodeConverter, CommentTree $commentTree)
    {
        $this->issueCodeConverter = $issueCodeConverter;
        $this->commentTree = $commentTree;
    }

    /**
     * @param \Twig_Environment $environment
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('issue_code', [$this, 'getIssueCode']),
            new \Twig_SimpleFunction('comments', [$this, 'renderComments'], ['is_safe' => ['html']]),
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
     * @param $comments
     * @return string
     */
    public function renderComments($comments)
    {
        if ($comments instanceof Collection) {
            $comments = $comments->toArray();
        }
        return $this->commentTree->render($this->environment, $comments);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_extension';
    }
}

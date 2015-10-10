<?php

namespace AppBundle\Templating\Comment;

use Symfony\Component\Templating\EngineInterface;
use AppBundle\Utils\Tree\TreeNode;
use AppBundle\Utils\Tree\TreeNodeVisitorInterface;

/**
 * Class CommentTreeRenderer
 */
class CommentTreeRenderer implements TreeNodeVisitorInterface
{
    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var string
     */
    private $string = '';

    /**
     * @param EngineInterface $templating
     */
    public function __construct($templating)
    {
        $this->templating = $templating;
    }

    /**
     * @param TreeNode $treeNode
     */
    public function visit($entity)
    {
        $this->string .= $this->templating->render('AppBundle:IssueComment:show.html.twig', ['entity' => $entity]);
    }

    /**
     * @return string
     */
    public function getRenderedString()
    {
        return $this->string;
    }
}

<?php

namespace AppBundle\Templating\Comment;

use AppBundle\Entity\IssueComment;
use AppBundle\Utils\Tree\NullTreeNode;
use AppBundle\Utils\Tree\TreeNode;

/**
 * Class CommentTree
 */
class CommentTree
{
    /**
     * @param array $comments
     */
    public function render($templating, $comments)
    {
        $tree = $this->buildTree($comments);

        $tree->sort(function (IssueComment $a, IssueComment $b) {
            $a = $a->getCreatedAt();
            $b = $b->getCreatedAt();

            if ($a === $b) {
                return 0;
            }
            return $a < $b ? -1 : 1;
        });

        $renderer = new CommentTreeRenderer($templating);

        $tree->accept($renderer);

        return $renderer->getRenderedString();
    }

    /**
     * @param $comments|IssueComments[]
     * @return NullTreeNode
     */
    private function buildTree($comments)
    {
        $comments = array_reduce($comments, function ($result, $comment) {
            /** @var IssueComment $comment */
            $result[$comment->getId()] = new TreeNode($comment);
            return $result;
        }, []);

        $root = new NullTreeNode();

        /** @var TreeNode[] $comments */
        foreach ($comments as $comment) {
            $parent = $root;
            if (!is_null($comment->getData()->getParent())) {
                $parent = $comments[$comment->getData()->getParent()->getId()];
            }
            $parent->addChild($comment);
        }

        return $root;
    }
}

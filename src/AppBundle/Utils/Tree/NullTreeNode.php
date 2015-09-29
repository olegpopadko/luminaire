<?php

namespace AppBundle\Utils\Tree;

/**
 * Class NullTreeNode
 */
class NullTreeNode extends TreeNode
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct(null);
    }

    /**
     * @param TreeNodeVisitorInterface $visitor
     */
    public function accept(TreeNodeVisitorInterface $visitor)
    {
        foreach ($this->children as $child) {
            $child->accept($visitor);
        }
    }

    /**
     * @param $function
     */
    public function sort($function)
    {
        foreach ($this->children as $child) {
            $child->sort($function);
        }
    }
}

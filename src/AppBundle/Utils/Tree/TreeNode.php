<?php

namespace AppBundle\Utils\Tree;

/**
 * Class TreeNode
 */
class TreeNode
{
    /**
     * @var mixed
     */
    private $data;

    /**
     * @var array|TreeNode[]
     */
    protected $children = [];

    /**
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param TreeNode $data
     */
    public function addChild(TreeNode $data)
    {
        $this->children[] = $data;
    }

    /**
     * @param TreeNodeVisitorInterface $visitor
     */
    public function accept(TreeNodeVisitorInterface $visitor)
    {
        $visitor->visit($this->getData());

        foreach ($this->children as $child) {
            $child->accept($visitor);
        }
    }

    /**
     * @param $function
     */
    public function sort($function)
    {
        usort($this->children, function (TreeNode $a, TreeNode $b) use ($function) {
            return call_user_func_array($function, [$a->getData(), $b->getData()]);
        });

        foreach ($this->children as $child) {
            $child->sort($function);
        }
    }
}

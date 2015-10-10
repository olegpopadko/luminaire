<?php

namespace AppBundle\Utils\Tree;

/**
 * Interface TreeNodeVisitorInterface
 */
interface TreeNodeVisitorInterface
{
    /**
     * @param $data
     * @return mixed
     */
    public function visit($data);
}

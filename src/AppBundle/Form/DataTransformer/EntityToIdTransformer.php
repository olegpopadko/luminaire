<?php

namespace AppBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class EntityToIdTransformer
 */
class EntityToIdTransformer implements DataTransformerInterface
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @param ObjectManager $manager
     * @param string $class
     */
    public function __construct(ObjectManager $manager, $class)
    {
        $this->manager = $manager;
        $this->class   = $class;
    }

    /**
     * @param mixed $entity
     * @return mixed
     */
    public function transform($entity)
    {
        if (is_null($entity)) {
            return null;
        }

        if (get_class($entity) !== $this->class && !is_subclass_of($entity, $this->class)) {
            throw new TransformationFailedException(sprintf('Expected a %s object.', $this->class));
        }

        return $entity->getId();
    }

    /**
     * @param mixed $id
     * @return object
     */
    public function reverseTransform($id)
    {
        if (is_null($id)) {
            return null;
        }

        $entity = $this->manager->getRepository($this->class)->find($id);

        if (is_null($entity)) {
            throw new TransformationFailedException(sprintf('The %s entity does not exist.', $this->class));
        }

        return $entity;
    }
}

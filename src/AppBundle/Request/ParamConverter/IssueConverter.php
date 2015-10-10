<?php

namespace AppBundle\Request\ParamConverter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DoctrineParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\Common\Persistence\ManagerRegistry;
use AppBundle\Utils\Exception\UnsupportedIssueCode;
use AppBundle\Utils\IssueCodeConverter;

/**
 * Class IssueConverter
 */
class IssueConverter extends DoctrineParamConverter
{
    /**
     * @var
     */
    private $issueCodeConverter;

    /**
     * @param IssueCodeConverter $issueCodeConverter
     * @param ManagerRegistry|null $registry
     */
    public function __construct(IssueCodeConverter $issueCodeConverter, ManagerRegistry $registry = null)
    {
        parent::__construct($registry);

        $this->issueCodeConverter = $issueCodeConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $name = $configuration->getName();

        $code = $request->attributes->get('code');

        if (is_null($code)) {
            return parent::apply($request, $configuration);
        }

        try {
            $entity = $this->issueCodeConverter->find($code);
            if (is_null($entity) && $configuration->isOptional() === false) {
                $this->throwNotFoundException();
            }
            $request->attributes->set($name, $entity);
        } catch (UnsupportedIssueCode $e) {
            $this->throwNotFoundException();
        }

        return true;
    }

    /**
     *
     */
    private function throwNotFoundException()
    {
        throw new NotFoundHttpException('AppBundle\Entity\Issue object not found.');
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() === 'AppBundle\Entity\Issue';
    }
}

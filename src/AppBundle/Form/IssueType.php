<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Utils\IssueCodeGenerator;
use AppBundle\Entity\IssueRepository;
use AppBundle\Entity\Issue;

/**
 * Class IssueType
 */
class IssueType extends AbstractType
{
    /**
     * @var
     */
    private $issueCodeGenerator;

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @param IssueCodeGenerator $issueCodeGenerator
     */
    public function __construct(IssueCodeGenerator $issueCodeGenerator, ObjectManager $manager)
    {
        $this->issueCodeGenerator = $issueCodeGenerator;
        $this->manager            = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Issue $entity */
        $entity  = $builder->getData();
        $project = $entity->getProject();

        $builder
            ->add('summary')
            ->add('description')
            ->add('status')
            ->add('priority')
            ->add('resolution')
            ->add('type')
            ->add('reporter', null, [
                'choices' => $project->getUsers(),
            ])
            ->add('assignee', null, [
                'choices' => $project->getUsers(),
            ])
            ->add('parent', null, [
                'query_builder' => function (IssueRepository $issueRepository) use ($entity) {
                    $type = $this->manager->getRepository('AppBundle:IssueType')->findStory();
                    return $issueRepository->createQueryBuilder('i')
                        ->where('i.project = :project')
                        ->andWhere('i.type = :type')
                        ->setParameter('project', $entity->getProject())
                        ->setParameter('type', $type);
                },
            ])
            ->add('collaborators', null, [
                'choices' => $project->getUsers(),
            ]);

        $builder->addEventListener(FormEvents::SUBMIT, [$this, 'onSubmit']);
    }

    /**
     * @param FormEvent $event
     */
    public function onSubmit(FormEvent $event)
    {
        $entity = $event->getData();
        /*
         * Set code only on create
         */
        if (!$entity->getId()) {
            $entity->setCode($this->issueCodeGenerator->getCode($entity));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Issue'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'appbundle_issue';
    }
}

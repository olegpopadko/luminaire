<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AppBundle\Utils\IssueCodeGenerator;

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
     * @param IssueCodeGenerator $issueCodeGenerator
     */
    public function __construct(IssueCodeGenerator $issueCodeGenerator)
    {
        $this->issueCodeGenerator = $issueCodeGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('summary')
            ->add('description')
            ->add('status')
            ->add('priority')
            ->add('resolution')
            ->add('type')
            ->add('reporter')
            ->add('assignee')
            ->add('project')
            ->add('parent')
            ->add('collaborators');

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

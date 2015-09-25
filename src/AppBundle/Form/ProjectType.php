<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AppBundle\Utils\ProjectCodeConverter;

class ProjectType extends AbstractType
{
    /**
     * @var ProjectCodeConverter
     */
    private $projectCodeConverter;

    /**
     * @param ProjectCodeConverter $nameConverter
     */
    public function __construct(ProjectCodeConverter $projectCodeConverter)
    {
        $this->projectCodeConverter = $projectCodeConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label')
            ->add('summary')
            ->add('users');

        $builder->addEventListener(
            FormEvents::SUBMIT,
            [$this, 'onSubmit']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Project'
        ]);
    }

    /**
     * @param FormEvent $event
     */
    public function onSubmit(FormEvent $event)
    {
        $entity = $event->getData();
        $entity->setCode($this->projectCodeConverter->getCode($entity));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'appbundle_project';
    }
}

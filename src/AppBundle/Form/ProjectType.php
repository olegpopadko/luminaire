<?php

namespace AppBundle\Form;

use AppBundle\Utils\NameConverter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectType extends AbstractType
{
    /**
     * @var NameConverter
     */
    private $nameConverter;

    /**
     * @param NameConverter $nameConverter
     */
    public function __construct(NameConverter $nameConverter)
    {
        $this->nameConverter = $nameConverter;
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
        $event->getData()->setCode($this->nameConverter->toAcronym($entity->getLabel()));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'appbundle_project';
    }
}

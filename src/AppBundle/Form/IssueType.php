<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class IssueType
 */
class IssueType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('summary')
            ->add('code')
            ->add('description')
            ->add('status')
            ->add('priority')
            ->add('resolution')
            ->add('type')
            ->add('reporter')
            ->add('assignee')
            ->add('project')
            ->add('parent')
            ->add('collaborators')
        ;
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

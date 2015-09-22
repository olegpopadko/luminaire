<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfileType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('username')
            ->add('fullName')
            ->add('password', 'repeated', [
                'first_name'  => 'password',
                'second_name' => 'confirm',
                'type'        => 'password',
                'required'    => false,
            ])
            ->add('timezone', 'timezone');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\User'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'appbundle_user';
    }
}

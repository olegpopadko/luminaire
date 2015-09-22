<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserWithPlainPasswordType extends AbstractType
{
    /**
     * @var FormTypeInterface
     */
    private $userType;

    /**
     * @param FormTypeInterface $userType
     */
    public function __construct(FormTypeInterface $userType)
    {
        $this->userType = $userType;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', $this->userType)
            ->add('plainPassword', 'repeated', [
                'required'    => false,
                'first_name'  => 'password',
                'second_name' => 'confirm',
                'type'        => 'password',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Form\Model\UserWithPlainPassword'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->userType->getName();
    }
}

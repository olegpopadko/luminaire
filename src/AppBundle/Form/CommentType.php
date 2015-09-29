<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Issue;

/**
 * Class IssueType
 */
class CommentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('issue', 'app_entity_hidden', [
                'class' => 'AppBundle\Entity\Issue'
            ])
            ->add('user', 'app_entity_hidden', [
                'class' => 'AppBundle\Entity\User'
            ])
            ->add('parent', 'app_entity_hidden', [
                'class' => 'AppBundle\Entity\IssueComment'
            ])
            ->add('body');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\IssueComment'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'appbundle_issue_comment';
    }
}

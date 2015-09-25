<?php

namespace AppBundle\Controller\Traits;

/**
 * Class FindTrait
 */
trait FindTrait
{
    /**
     * @return \Symfony\Component\Form\Form
     */
    private function createFindForm($action)
    {
        /** @var \Symfony\Component\Form\Form $form */
        $form = $this->get('form.factory')->createNamed('', 'form', ['q' => null], [
            'action' => $action,
            'method' => 'GET',
        ]);
        $form->add('q', 'text', ['required' => false])
            ->add('submit', 'submit');

        return $form;
    }
}

<?php

namespace AppBundle\Exceptions;

use Symfony\Component\Form\FormInterface;

class FormValidationException extends \Exception
{
    /**
     * @var FormInterface
     */
    private $form;

    public function __construct(FormInterface $form)
    {
        $this->form = $form;

        parent::__construct("Form validation failed");
    }

    /**
     * @return FormInterface
     */
    public function getForm(): FormInterface
    {
        return $this->form;
    }

}
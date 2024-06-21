<?php

namespace App\Api\Preparer;

use App\Api\Preparer\Exception\EntityNotSupportedException;
use App\Entity\Api\EntityInterface;
use Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class FormPreparer
{
    protected string               $formType;
    protected FormFactoryInterface $formFactory;
    private RequestStack           $requestStack;

    public function __construct(
        FormFactoryInterface $formFactory,
        RequestStack $requestStack
    ) {
        $this->formFactory  = $formFactory;
        $this->requestStack = $requestStack;
    }

    /**
     * @throws Exception
     */
    public function prepareForm(EntityInterface $entity, array $options = []): FormInterface
    {
        if (!$this->support($entity)) {
            throw new EntityNotSupportedException();
        }

        $form = $this->formFactory->create($this->formType, $entity, $options);

        $request = $this->requestStack->getMainRequest();
        $form->handleRequest($request);

        return $form;
    }

    abstract protected function support(EntityInterface $entity): bool;
}

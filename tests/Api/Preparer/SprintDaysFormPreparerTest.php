<?php

namespace App\Tests\Api\Preparer;

use App\Api\Preparer\Exception\EntityNotSupportedException;
use App\Api\Preparer\SprintDaysFormPreparer;
use App\Entity\Api\Sprint;
use App\Entity\Api\SprintStory;
use App\Form\Api\SprintDaysType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class SprintDaysFormPreparerTest extends TestCase
{
    private SprintDaysFormPreparer $formPreparer;
    private FormFactoryInterface   $formFactory;
    private RequestStack           $requestStack;

    protected function setUp(): void
    {
        $this->formFactory  = $this->createMock(FormFactoryInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->formPreparer = new SprintDaysFormPreparer($this->formFactory, $this->requestStack);
    }

    /**
     * @dataProvider dataProviderPrepareForm
     */
    public function testPrepareForm($entity, $expectedException): void
    {
        $form = $this->createMock(FormInterface::class);

        $this->formFactory
            ->expects(self::exactly((int)!$expectedException))
            ->method('create')
            ->with(SprintDaysType::class, $entity, [])
            ->willReturn($form);

        $request = $this->createMock(Request::class);
        $this->requestStack->push($request);

        if ($expectedException !== null) {
            $this->expectException($expectedException);
        }

        $preparedForm = $this->formPreparer->prepareForm($entity);
        !$expectedException && $this->assertInstanceOf(FormInterface::class, $preparedForm);
    }

    public static function dataProviderPrepareForm(): iterable
    {
        yield [new Sprint(), null];
        yield [new SprintStory(), EntityNotSupportedException::class];
    }
}

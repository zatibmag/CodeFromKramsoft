<?php

namespace App\Tests\Api\Preparer;

use App\Api\Preparer\CapacityDayChartPointFormPreparer;
use App\Api\Preparer\Exception\EntityNotSupportedException;
use App\Entity\Api\SprintStory;
use App\Entity\CapacityDayChartPoint;
use App\Form\Api\CapacityDayChartPointType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CapacityDayChartPointFormPreparerTest extends TestCase
{
    private CapacityDayChartPointFormPreparer $formPreparer;
    private FormFactoryInterface   $formFactory;
    private RequestStack           $requestStack;

    protected function setUp(): void
    {
        $this->formFactory  = $this->createMock(FormFactoryInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->formPreparer = new CapacityDayChartPointFormPreparer($this->formFactory, $this->requestStack);
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
            ->with(CapacityDayChartPointType::class, $entity, [])
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
        yield [new CapacityDayChartPoint(), null];
        yield [new SprintStory(), EntityNotSupportedException::class];
    }
}

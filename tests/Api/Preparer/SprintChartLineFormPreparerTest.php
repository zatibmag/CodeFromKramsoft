<?php

namespace App\Tests\Api\Preparer;

use App\Api\Preparer\SprintChartLineFormPreparer;
use App\Entity\ChartLine;
use App\Form\Api\SprintChartLineType;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SprintChartLineFormPreparerTest extends TestCase
{
    private SprintChartLineFormPreparer $formPreparer;
    private FormFactoryInterface        $formFactory;

    protected function setUp(): void
    {
        $this->formFactory  = $this->createMock(FormFactoryInterface::class);
        $this->formPreparer = new SprintChartLineFormPreparer(
            $this->formFactory, $this->createMock(RequestStack::class)
        );
    }

    /**
     * @dataProvider chartLineDataProvider
     * @throws Exception
     */
    public function testPrepareForm($chartLine): void
    {
        $form = $this->createMock(FormInterface::class);

        $this->formFactory
            ->expects(self::once())
            ->method('create')
            ->with(SprintChartLineType::class, $chartLine)
            ->willReturn($form);

        $preparedForm = $this->formPreparer->prepareForm($chartLine);

        $this->assertInstanceOf(FormInterface::class, $preparedForm);
    }

    public function chartLineDataProvider(): array
    {
        return [
            'valid_chart_line'   => [new ChartLine()],
            'another_chart_line' => [new ChartLine()],
            'empty_chart_line'   => [new ChartLine()],
        ];
    }
}

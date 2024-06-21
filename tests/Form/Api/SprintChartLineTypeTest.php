<?php

namespace App\Tests\Form\Api;

use Symfony\Component\Form\Test\TypeTestCase;
use App\Form\Api\SprintChartLineType;
use App\Entity\ChartLine;

class SprintChartLineTypeTest extends TypeTestCase
{
    /**
     * @dataProvider validDataProvider
     */
    public function testSubmitValidData(array $formData, int $expectedCapacity)
    {
        $objectToCompare = new ChartLine();

        $form = $this->factory->create(SprintChartLineType::class, $objectToCompare);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expectedCapacity, $objectToCompare->getCapacity());
    }

    public function validDataProvider(): array
    {
        return [
            [['capacity' => 10], 10],
            [['capacity' => 20], 20],
        ];
    }

    /**
     * @dataProvider nullDataProvider
     */
    public function testSubmitNullData(array $formData, $expectedCapacity)
    {
        $objectToCompare = new ChartLine();

        $form = $this->factory->create(SprintChartLineType::class, $objectToCompare);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame($expectedCapacity, $objectToCompare->getCapacity());
    }

    public function nullDataProvider(): array
    {
        return [
            [['capacity' => null], null],
        ];
    }
}

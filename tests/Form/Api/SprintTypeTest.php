<?php

namespace App\Tests\Form\Api;

use App\Entity\Api\Sprint;
use App\Form\Api\SprintType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SprintTypeTest extends TestCase
{
    protected function setUp(): void
    {
        $this->formType = new SprintType();
    }

    public function testBuildForm()
    {
        $builder = $this->createMock(FormBuilderInterface::class);

        $expectedCalls = [
            ['name', null],
            ['capacity', null],
            ['startAt', DateType::class],
            ['endAt', DateType::class],
            ['listDoneId', null],
            ['capacityType', null],
        ];

        $builder->expects($this->exactly(count($expectedCalls)))
            ->method('add')
            ->willReturnCallback(function ($name, $type) use ($builder, &$expectedCalls) {
                $expectedCall = array_shift($expectedCalls);

                $this->assertEquals($expectedCall[0], $name);
                $this->assertEquals($expectedCall[1], $type);

                return $builder;
            });

        $this->formType->buildForm($builder, []);
    }

    public function testConfigureOptions()
    {
        $resolver = $this->createMock(OptionsResolver::class);

        $resolver->expects($this->once())
            ->method('setDefaults')
            ->with(['data_class' => Sprint::class]);

        $this->formType->configureOptions($resolver);
    }
}

<?php

namespace App\Tests\Form\Api;

use App\Entity\Api\Sprint;
use App\Form\Api\SprintDaysType;
use App\Form\Api\SprintExcludedDayType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SprintDaysTypeTest extends TestCase
{
    protected function setUp(): void
    {
        $this->formType = new SprintDaysType();
    }

    public function testBuildForm()
    {
        $builder = $this->createMock(FormBuilderInterface::class);

        $expectedCalls = [
            [
                'excludedDays',
                CollectionType::class,
                [
                    'entry_type'   => SprintExcludedDayType::class,
                    'allow_add'    => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ],
            ],
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

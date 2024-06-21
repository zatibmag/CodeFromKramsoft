<?php

namespace App\Tests\Form\Api;

use App\Entity\Api\SprintStory;
use App\Form\Api\SprintStoryType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SprintStoryTypeTest extends TestCase
{
    protected function setUp(): void
    {
        $this->formType = new SprintStoryType();
    }

    public function testBuildForm()
    {
        $builder = $this->createMock(FormBuilderInterface::class);

        $expectedCalls = [
            ['capacity', null],
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
            ->with(['data_class' => SprintStory::class]);

        $this->formType->configureOptions($resolver);
    }
}

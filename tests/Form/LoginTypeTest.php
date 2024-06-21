<?php

namespace App\Tests\Form;

use App\Entity\Api\User;
use App\Form\LoginType;
use Symfony\Component\Form\Test\TypeTestCase;

class LoginTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'username' => 'testuser',
            'password' => 'password123',
        ];

        $user = new User();
        $form = $this->factory->create(LoginType::class, $user);

        $expectedUser = new User();
        $expectedUser->setUsername('testuser');
        $expectedUser->setPassword('password123');

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expectedUser, $user);

        $view     = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    public function testFormRendering()
    {
        $user = new User();
        $form = $this->factory->create(LoginType::class, $user);

        $view     = $form->createView();
        $children = $view->children;

        foreach (['username', 'password'] as $fieldName) {
            $this->assertArrayHasKey($fieldName, $children);
            $this->assertArrayHasKey('class', $children[$fieldName]->vars['attr']);
            $this->assertStringContainsString('form-control', $children[$fieldName]->vars['attr']['class']);
        }

        $this->assertArrayHasKey('submit', $children);
        $this->assertArrayHasKey('class', $children['submit']->vars['attr']);
        $this->assertStringNotContainsString('form-control', $children['submit']->vars['attr']['class']);
    }
}

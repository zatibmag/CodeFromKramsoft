<?php

namespace App;

use App\DependencyInjection\Extension\AppExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }

    protected function getContainerExtensionClass(): string
    {
        return AppExtension::class;
    }
}

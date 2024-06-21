<?php

namespace App\Api\Manager;

use InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;

class PermissionManager
{
    public function identifyRole(InputInterface $input): array
    {
        $role = $input->getOption('role');

        $roleMap = [
            'scrum-master' => 'ROLE_SCRUM_MASTER',
            'team-member'  => 'ROLE_TEAM_MEMBER',
            'admin'        => 'ROLE_ADMIN',
            'super-admin'  => 'ROLE_SUPER_ADMIN',
        ];

        if (!isset($roleMap[$role])) {
            throw new InvalidArgumentException('Wrong role argument provided: 
            --role super-admin
            --role admin
            --role scrum-master
            --role team-member'
            );
        }

        return [$roleMap[$role]];
    }
}

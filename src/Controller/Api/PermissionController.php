<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Api\UserRepository;
use App\Entity\Api\User;

class PermissionController extends AbstractController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/api/get-permission", name="api_get_permission", methods={"POST"})
     */
    public function hasPermissions(): JsonResponse
    {
        $roles = $this->getUser()->getRoles();

        $availableRoles = array_flip(User::AVAILABLE_ROLES);

        $currentRole     = current($roles);
        $permissionLevel = $availableRoles[$currentRole];

        return $this->json($permissionLevel);
    }
}

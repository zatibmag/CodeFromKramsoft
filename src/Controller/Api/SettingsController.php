<?php

namespace App\Controller\Api;

use App\Entity\Api\User;
use App\Repository\Api\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("api/settings")
 */
class SettingsController extends AbstractController
{
    private UserRepository         $userRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager  = $entityManager;
    }

    /**
     * @Route("/users", name="settings_users", methods={"POST"})
     */
    public function index(): JsonResponse
    {
        $currentRole = $this->currentRole();

        $users           = $this->userRepository->findAll();
        $serializedUsers = [];

        foreach ($users as $user) {
            if ($currentRole == User::ROLE_ADMIN_NAME && in_array(User::ROLE_SUPER_ADMIN_NAME, $user->getRoles())) {
                continue;
            }
            $serializedUsers[] = $user->toArray();
        }

        return $this->json($serializedUsers);
    }

    /**
     * @Route("/available-roles", name="settings_users_roles", methods={"POST"})
     */
    public function availableRoles(): JsonResponse
    {
        return $this->json(User::AVAILABLE_ROLES);
    }

    /**
     * @Route("/change-role/{userId}", name="settings_change_role", methods={"POST"})
     */
    public function changeRole(int $userId, Request $request): Response
    {
        $user = $this->userRepository->find($userId);

        $selectedRole = $request->query->get('role');

        $user->setRoles([$selectedRole]);

        $this->entityManager->flush();

        return new JsonResponse(['success' => true]);
    }

    private function currentRole()
    {
        $roles       = $this->getUser()->getRoles();
        $currentRole = current($roles);

        return $currentRole;
    }
}

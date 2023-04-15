<?php

namespace App\Controller\User;

use App\Entity\Image;
use App\Entity\User;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsController]
final class RegistrationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface      $entityManager,
        private UserPasswordHasherInterface $hasher
    ) {
    }

    public function __invoke(User $userRequest, $request, FileUploader $fileUploader, $user): user
    {
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->hasher->hashPassword($user, $user->getPassword()));
        $uploadedFile = $request->files->get('file');
        if (!$uploadedFile) {
            throw new BadRequestHttpException('"file" is required');
        }

        $userRequest = new Image();
        $userRequest->email = $request->get('email');
        $userRequest->updated_at = $request->get('updated_at');
        $userRequest->created_at = $request->get('created_at');

        $userRequest->cover = $fileUploader->upload($uploadedFile);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}

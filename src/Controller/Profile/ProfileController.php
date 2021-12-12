<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\NetworkRepository;
use App\Model\User\Entity\User\UserRepository;
use App\Service\Upload\UploadHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\MimeTypesInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @IsGranted("ROLE_USER")
 * Class ProfileController
 * @package App\Controller\Profile
 */
class ProfileController extends AbstractController
{
    private UserRepository $users;
    private ValidatorInterface $validator;
    private MimeTypesInterface $mimeTypes;
    private UploadHelper $uploadHelper;

    /**
     * ProfileController constructor.
     * @param UserRepository $users
     * @param ValidatorInterface $validator
     * @param MimeTypesInterface $mimeTypes
     */
    public function __construct(
        UserRepository $users,
        ValidatorInterface $validator,
        MimeTypesInterface $mimeTypes
    )
    {
        $this->users = $users;
        $this->validator = $validator;
        $this->mimeTypes = $mimeTypes;
    }

    /**
     * @Route("/profile", name="profile")
     * @return Response
     */
    public function index(): Response
    {
        $user = $this->users->get(new Id($this->getUser()->getId()));
        return $this->render('app/profile/show.html.twig', compact('user'));
    }

    /**
     * @Route("/profile/image-upload", name="profile.image_upload")
     * @param Request $request
     * @param UploadHelper $uploadHelper
     * @return Response
     * @throws \League\Flysystem\FileExistsException
     */
    public function uploadProfileImage(Request $request, UploadHelper $uploadHelper, UserRepository $userRepository): Response
    {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('cropped-image');
        $userId = $request->get('user-id');

        $user = $userRepository->get(new Id($userId));

        $violations = $this->validator->validate($uploadedFile, [
           new NotBlank(),
           new File([
               'maxSize' => '5M',
               'mimeTypes' => array_merge($this->mimeTypes->getMimeTypes('jpeg'), $this->mimeTypes->getMimeTypes('png'))
           ])
        ]);

        if ($violations->count() > 0) {
            /** @var ConstraintViolation $violation */
            $violation = $violations[0];

            $this->addFlash('error', $violation->getMessage());
            return $this->redirectToRoute('profile');
        }

        $filename = $uploadHelper->uploadFile(
            $uploadedFile,
            (new \ReflectionClass($user))->getShortName(),
            $user->getId()->getValue()
        );
        dd('cool');
    }
}
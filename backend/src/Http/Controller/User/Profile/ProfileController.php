<?php

declare(strict_types=1);

namespace App\Http\Controller\User\Profile;

use App\Domain\User\Entity\User\Id;
use App\Domain\User\Entity\User\ImageRepository;
use App\Domain\User\Entity\User\UserRepository;
use App\Domain\User\UseCase\Image;
use App\Infrastructure\Upload\UploadHelper;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\MimeTypesInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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
    private LoggerInterface $logger;
    private ImageRepository $images;

    /**
     * ProfileController constructor.
     * @param UserRepository $users
     * @param ImageRepository $images
     * @param ValidatorInterface $validator
     * @param MimeTypesInterface $mimeTypes
     * @param LoggerInterface $logger
     */
    public function __construct(
        UserRepository $users,
        ImageRepository $images,
        ValidatorInterface $validator,
        MimeTypesInterface $mimeTypes,
        LoggerInterface $logger
    )
    {
        $this->users = $users;
        $this->validator = $validator;
        $this->mimeTypes = $mimeTypes;
        $this->logger = $logger;
        $this->images = $images;
    }

    /**
     * @Route("/profile", name="profile")
     * @return Response
     */
    public function index(): Response
    {
        $user = $this->users->get(new Id($this->getUser()->getId()));
        $activeImage = $this->images->findActiveImageByUserId($user->getId());
        return $this->render('app/profile/show.html.twig', compact('user', 'activeImage'));
    }

    /**
     * @Route("/profile/image-upload", name="profile.image_upload")
     * @param Request $request
     * @param Image\Attach\Handler $handler
     * @return Response
     */
    public function uploadProfileImage(Request $request, Image\Attach\Handler $handler): Response
    {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('cropped-image');

        $violations = $this->validator->validate($uploadedFile, [
           new NotBlank(),
           new File([
               'maxSize' => '5M',
               'mimeTypes' => \array_merge($this->mimeTypes->getMimeTypes('jpeg'), $this->mimeTypes->getMimeTypes('png'))
           ])
        ]);

        $responseData['error'] = false;

        if ($violations->count() > 0) {
            /** @var ConstraintViolation $violation */
            $violation = $violations[0];
            $responseData['error'] = true;
            $responseData['message'] = $violation->getMessage();

            return $this->json($responseData, 422);
        }

        $command = new Image\Attach\Command($uploadedFile, $request->get('user-id'));

        try {
            $handler->handle($command);
        } catch (\Exception $e) {
            $responseData['error'] = true;
            $responseData['message'] = $e->getMessage();

            return $this->json($responseData, 422);
        }

        return $this->json($responseData);
    }
}
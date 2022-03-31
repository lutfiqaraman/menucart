<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $hashingUserPassword;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hashingUserPassword)
    {
        $this->em = $em;
        $this->hashingUserPassword = $hashingUserPassword;
    }

    #[Route('/register', name: 'register')]
    public function register(Request $request): Response
    {
        $user = new User();

        $registerForm = $this->createForm(RegisterType::class, $user);
        $registerForm->handleRequest($request);

        if ($registerForm->isSubmitted() && $registerForm->isValid())
        {
            $user->setPassword(
                $this->hashingUserPassword->hashPassword(
                    $user, $user->getPassword()
                )
            );

            $this->em->persist($user);
            $this->em->flush();
        }

        return $this->render('registration/index.html.twig', [
            'registerForm' => $registerForm->createView()
        ]);
    }
}

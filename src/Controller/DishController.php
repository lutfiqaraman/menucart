<?php

namespace App\Controller;

use App\Entity\Dishes;
use App\Form\DishType;
use App\Repository\DishesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dish', name: 'dish.')]
class DishController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'edit')]
    public function index(DishesRepository $dishesRepository): Response
    {
        $dishes = $dishesRepository->findAll();

        return $this->render('dish/index.html.twig', [
            'dishes' => $dishes,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request): Response
    {
        $dish = new Dishes();
        $form = $this->createForm(DishType::class, $dish);
        $form->handleRequest($request);

        if ($form->isSubmitted())
        {
            $this->em->persist($dish);
            $this->em->flush();

            return $this->redirect($this->generateUrl('dish.edit'));
        }

        return $this->render('dish/create.html.twig', [
            'createDishForm' => $form->createView()
        ]);
    }
}

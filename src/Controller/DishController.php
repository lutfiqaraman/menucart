<?php

namespace App\Controller;

use App\Entity\Dishes;
use App\Form\DishType;
use App\Repository\DishesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

    #[Route('/', name: 'listofdishes')]
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

        //upload an image
        if ($form->isSubmitted() && $form->isValid())
        {
            $image = $request->files->get('dish')['image'];
            $fileName = "";

            if ($image) {
                $fileName = md5(uniqid()) . '.' . $image->guessExtension();
            }

            $image->move(
                $this->getParameter('public_images_folder'),
                $fileName
            );

            $dish->setImage($fileName);
            $this->em->persist($dish);
            $this->em->flush();

            return $this->redirect($this->generateUrl('dish.listofdishes'));
        }

        return $this->render('dish/editcreatedish.html.twig', [
            'editCreateDishForm' => $form->createView()
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit($id, Request $request): Response
    {
        $dish = $this->em->getRepository(Dishes::class)->find($id);

        $form = $this->createForm(DishType::class, $dish);
        $form->handleRequest($request);

        if ($form->isSubmitted())
        {
            $this->em->persist($dish);
            $this->em->flush();

            return $this->redirect($this->generateUrl('dish.listofdishes'));
        }

        return $this->render('dish/editcreatedish.html.twig', [
            'editCreateDishForm' => $form->createView()
        ]);
    }

    #[Route('/view/{id}', name: 'view')]
    public function view($id, Request $request): Response
    {
        $dish = $this->em->getRepository(Dishes::class)->find($id);

        return $this->render('dish/viewdish.html.twig', [
            'dishInfo' => $dish
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete($id): RedirectResponse
    {
        $dish = $this->em->getRepository(Dishes::class)->find($id);
        $this->em->remove($dish);
        $this->em->flush();

        $this->addFlash('success', 'The dish has been removed successfully');
        return $this->redirect($this->generateUrl('dish.listofdishes'));
    }
}

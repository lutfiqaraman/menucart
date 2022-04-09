<?php

namespace App\Controller;

use App\Entity\Dishes;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/order', name: 'orderlist')]
    public function index(OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->findBy(
            ['tablename' => 'Table 1']
        );

        return $this->render('order/index.html.twig', [
            'theOrder' => $order,
        ]);
    }

    #[Route('/order/{id}', name: 'order')]
    public function order(Dishes $dish) : RedirectResponse
    {
        $order = new Order();

        $order->setTablename('Table 1');
        $order->setName($dish->getName());
        $order->setOrdernumber($dish->getId());
        $order->setPrice($dish->getPrice());
        $order->setStatus('Open');

        $this->em->persist($order);
        $this->em->flush();

        $this->addFlash('Order', $order->getName() . ' is added to the order');
        return
            $this->redirect($this->generateUrl('menu'));
    }
}

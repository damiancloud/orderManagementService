<?php
namespace App\Controller;

use App\Entity\Orders;
use App\Entity\Products;
use App\Entity\ProductOrder;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Stmt\Foreach_;

class OrderController extends AbstractController
{
    #[Route('/createOrder', methods: ['POST'])]
    public function createOrder(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($request->isMethod('POST')) {
            $data = json_decode($request->getContent(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return new JsonResponse(['error' => 'Invalid JSON data'], 400);
            }

            $order = new Orders();
            $order->setName($data['name']);
            $createdAt = new \DateTime();
            $order->setDate($createdAt);

            $entityManager->persist($order);
            
            foreach ($data['products'] as $productData) {
                $product = new Products();
                $product->setTitle($productData['title']);
                $product->setPrice($productData['price']);
                $product->setCategory($productData['category']);
                $product->setDescription($productData['description']);
                $product->setImage($productData['image']);
    
                $order->addProduct($product);

                $entityManager->persist($product);
            }

            $entityManager->flush();

        foreach ($order->getProducts() as $product) {
            $productOrder = new ProductOrder();
            $productOrder->setProduct($product);
            $productOrder->setOrder($order);

            $entityManager->persist($productOrder);
        }

        $entityManager->flush();

            return $this->json(['message' => 'Order created successfully'], Response::HTTP_CREATED);
        }

        return new JsonResponse(['error' => 'Invalid request method'], Response::HTTP_METHOD_NOT_ALLOWED);
    }

    #[Route('/orders', methods: ['GET'])]
    public function getOrders(EntityManagerInterface $entityManager): JsonResponse
    {
        $orders = $entityManager->getRepository(Orders::class)->findAll();
        $ordersData = [];
        foreach ($orders as $order) {
            //dd($orders);
            $ordersData[] = [
                'id' => $order->getId(),
                'product' => $order->getProducts(),
                //'quantity' => $order->getQuantity()
            ];
        }
        dd($ordersData);

        return $this->json($ordersData);
    }

    // TODO
    /*
    GET /order/{order_id}

    Endpoint do pobierania szczegółów zamówienia na podstawie jego identyfikatora.
    Odpowiada na zapytania typu GET i zwraca szczegóły konkretnego zamówienia.
    */

    /*
    DELETE /order/{order_id}

    Endpoint do anulowania zamówienia na podstawie jego identyfikatora.
    Odpowiada na zapytania typu DELETE i usuwa zamówienie z systemu.
    */

    /*
    POST /order/{order_id}/recreate

    Endpoint do odtworzenia zamówienia na podstawie wcześniej złożonego zamówienia.
    Wymaga identyfikatora zamówienia, które ma zostać odtworzone.
    Tworzy nowe zamówienie na podstawie danych z wcześniejszego zamówienia i zwraca potwierdzenie.
    */
}
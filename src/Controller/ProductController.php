<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Orders;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProductController extends AbstractController
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('/client/products', methods: ['GET'])]
    public function getProductsClient(): JsonResponse
    {
        // @todo dodanie stałej 'https://fakestoreapi.com/products' 
        // @todo wyniesienie moze do osobnego serwisu ?
        $response = $this->httpClient->request('GET', 'https://fakestoreapi.com/products');
        if ($response->getStatusCode() === Response::HTTP_OK) {
            $data = $response->toArray();
            return $this->json($data);
        }

        // Obsługa błędu, jeśli nie udało się pobrać produktów
        return $this->json(['error' => 'Unable to fetch products'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    #[Route('/products/{orderId}', methods: ['GET'])]
    public function getProducts(EntityManagerInterface $entityManager, $orderId): JsonResponse
    {
        // Pobierz zamówienie na podstawie ID
        $order = $entityManager->getRepository(Orders::class)->find($orderId);

        if (!$order) {
            return new JsonResponse(['error' => 'Order not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $order->getProducts();
        $products = $order->getProducts();
        dd($products);
        exit;

        $productData = [];
        foreach ($products as $product) {
            $productData[] = [
                'id' => $product->getId(),
                'title' => $product->getTitle(),
                'price' => $product->getPrice(),
                'category' => $product->getCategory(),
                'description' => $product->getDescription(),
                'image' => $product->getImage(),
            ];
        }

        return $this->json($productData);
    }

    //TODO - dodatkowa metoda do zaimplementowania na koncu - ale ..
    // moze musi byc potrzebna w getOrders i tam pobieram produkty tą metodą
    /*
    GET /products/{product_id}

    Endpoint do pobierania szczegółów produktu na podstawie jego identyfikatora.
    Odpowiada na zapytania typu GET i zwraca szczegóły konkretnego produktu.
    */

}
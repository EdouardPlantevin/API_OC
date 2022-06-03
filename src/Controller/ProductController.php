<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api")]
class ProductController extends AbstractController
{

    public function __construct(private ProductRepository $productRepository){}

    #[Route('/products', name: 'products_get', methods: ['GET'])]
    public function get(): JsonResponse
    {
        return $this->json($this->productRepository->findAll(), 200, [], ['groups' => 'product:read']);
    }

    #[Route('/products/{id}', name: 'product_get', methods: ['GET'])]
    public function show($id): JsonResponse
    {
        $product = $this->productRepository->find($id);
        if (!$product) { return $this->json(["No product found"], 404);}

        return $this->json($product, 200, [], ['groups' => 'product:read']);
    }

}
<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use DateTime;
use Symfony\Component\Validator\Constraints\Length;

#[Route('/product')]
class ProductController extends AbstractController
{

    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    #[Route('/', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        try {
            $products = $this->productRepository->findAll();
            $data = [];

            foreach ($products as $product) {
                $data[] = [
                    'id' => $product->getId(),
                    'sku' => $product->getSku(),
                    'name' => $product->getProductName(),
                    'description' => $product->getDescription(),
                    'created_at' => $product->getCreatedAt()->format('Y-m-d H:i:s'), // Format the datetime
                    'updated_at' => $product->getUpdateAt()->format('Y-m-d H:i:s'), // Format the datetime
                ];
            }

            return new JsonResponse($data, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Error ocurrido'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/add', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!is_array($data) || empty($data)) {
                throw new \InvalidArgumentException('La solicitud debe contener un arreglo de productos.');
            }

            $createdProducts = [];
            foreach ($data as $productData) {
                $sku = $productData['sku'];
                $name = $productData['name'];
                $description = $productData['description'] ?? null;

                if (empty($sku) || empty($name)) {
                    throw new \InvalidArgumentException('Faltan parámetros, sku y name son obligatorios.');
                }
                $product = new Product();
                $product->setSku($sku);
                $product->setProductName($name);
                $product->setDescription($description);
                $currentDateTime = new \DateTime();
                $product->setCreatedAt($currentDateTime);
                $product->setUpdateAt($currentDateTime);

                $this->productRepository->saveProduct($product);

                $createdProducts[] = [
                    'id' => $product->getId(),
                    'sku' => $product->getSku(),
                    'name' => $product->getProductName(),
                    'description' => $product->getDescription(),
                    'created_at' => $product->getCreatedAt()->format('Y-m-d H:i:s'),
                    'updated_at' => $product->getUpdateAt()->format('Y-m-d H:i:s'),
                ];
            }
            return new JsonResponse(['status' => 'Productos creados!', 'created_products' => $createdProducts], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Error ocurrido: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[Route('/update', methods: ['PUT'])]
    public function update(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
    
            if (!is_array($data) || empty($data)) {
                throw new \InvalidArgumentException('La solicitud debe contener un arreglo de productos a actualizar.');
            }
    
            $updatedProducts = [];
    
            foreach ($data as $productData) {
                $sku = $productData['sku'] ?? null;
                $name = $productData['name'] ?? null;
                $description = $productData['description'] ?? null;
    
                if (empty($sku) || !is_string($sku)) {
                    throw new \InvalidArgumentException('Cada producto debe tener al menos la clave "sku".');
                }
    
                $product = $this->productRepository->findOneBy(['sku' => $sku]);
    
                if (!$product) {
                    throw new \RuntimeException('Producto con SKU ' . $sku . ' no encontrado.');
                }
    
                if (!empty($name)) {
                    $product->setProductName($name);
                }
    
                if (!empty($description)) {
                    $product->setDescription($description);
                }
    
                $this->productRepository->updateProducts($product);
    
                $updatedProducts[] = [
                    'sku' => $product->getSku(),
                    'name' => $product->getProductName(),
                    'description' => $product->getDescription(),
                ];
            }
    
            return new JsonResponse(['status' => 'Productos actualizados.', 'updated_products' => $updatedProducts], JsonResponse::HTTP_OK);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => 'Error ocurrido: ' . $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    #[Route('/delete/{sku}', methods: ['DELETE'])]
    public function delete($sku): JsonResponse
    {
       try {
            $product = $this->productRepository->findOneBy(['sku' => $sku]);
            $this->productRepository->removeProduct($product);

            return new JsonResponse(['status' => 'Producto eliminado','producto eliminado con sku' => $sku], JsonResponse::HTTP_OK);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => 'Error ocurrido: ' . $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            
       }
    }
}

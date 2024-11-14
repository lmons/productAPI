<?php

namespace App\Controller\API;

use App\Entity\Product;  // Assuming this is your Product entity
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductsRepository;

class ProductController extends AbstractController
{
    /**
     * @Route("/api/products", name="get_products", methods={"GET"})
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get all products",
     *     description="Retrieves a list of all products",
     *     @OA\Response(
     *         response="200",
     *         description="A list of products",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Product::class))
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="No products found",
     *     )
     * )
     */
    #[Route('/api/products', name: 'app_product', methods: ['GET'])]
    public function getAllProducts(ProductsRepository $repo): JsonResponse {
        $products = $repo->findAll();
        return $this->json($products);
    }

    /**
     * @Route("/api/products/{id}", name="id_product", methods={"GET"})
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Get a product by ID",
     *     description="Retrieves a single product by its ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The product's ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Product details",
     *         @OA\JsonContent(ref=@Model(type=Product::class))
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Product not found",
     *     )
     * )
     */
    #[Route('/api/products/{id}', name: 'id_product', methods: ['GET'])]
    public function getProduct(int $id, ProductsRepository $repo): JsonResponse {
        $product = $repo->find($id);
        if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->json($product);
    }

    /**
     * @Route("/api/products/search/{name}", name="search_product", methods={"GET"})
     * @OA\Get(
     *     path="/api/products/search/{name}",
     *     summary="Search products by name",
     *     description="Search for products by their name",
     *     @OA\Parameter(
     *         name="name",
     *         in="path",
     *         description="Product name to search for",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="List of products matching the search",
     *         @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=Product::class)))
     *     )
     * )
     */
    #[Route('/api/products/search/{name}', name: 'search_product', methods: ['GET'])]
    public function getProductsByName(string $name, ProductsRepository $repo): JsonResponse {
        $products = $repo->findByName($name);
        return $this->json($products);
    }

    /**
     * @Route("/api/products/category/{category}", name="category_product", methods={"GET"})
     * @OA\Get(
     *     path="/api/products/category/{category}",
     *     summary="Get products by category",
     *     description="Retrieve products by their category",
     *     @OA\Parameter(
     *         name="category",
     *         in="path",
     *         description="Category of products",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="List of products in the specified category",
     *         @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=Product::class)))
     *     )
     * )
     */
    #[Route('/api/products/category/{category}', name: 'category_product', methods: ['GET'])]
    public function getProductsByCategory(string $category, ProductsRepository $repo): JsonResponse {
        $products = $repo->findByCategory($category);
        return $this->json($products);
    }

    /**
     * @Route("/api/products/{id}", name="delete_product", methods={"DELETE"})
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Delete a product by ID",
     *     description="Deletes a product by its ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The product's ID to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Product successfully deleted",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Product deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Product not found",
     *     )
     * )
     */
    #[Route('/api/products/{id}', name: 'delete_product', methods: ['DELETE'])]
    public function deleteProduct(int $id, ProductsRepository $repo): JsonResponse {
        $product = $repo->find($id);
        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }
        $repo->remove($product, true);
        return $this->json(['message' => 'Product deleted successfully']);
    }

    /**
     * @Route("/api/products/{id}", name="update_product", methods={"PUT"})
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Update a product by ID",
     *     description="Updates the details of a product",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The product's ID to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Product data to update",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="image", type="string"),
     *             @OA\Property(property="category", type="string"),
     *             @OA\Property(property="price", type="number"),
     *             @OA\Property(property="quantity", type="integer"),
     *             @OA\Property(property="internal_reference", type="string"),
     *             @OA\Property(property="shell_id", type="string"),
     *             @OA\Property(property="inventory_status", type="string"),
     *             @OA\Property(property="rating", type="number"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Product successfully updated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Product updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Product not found",
     *     )
     * )
     */
    #[Route('/api/products/{id}', name: 'update_product', methods: ['PUT'])]
    public function updateProduct(int $id, Request $request, ProductsRepository $repo): JsonResponse {
        $product = $repo->find($id);
        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }
        $data = json_decode($request->getContent(), true);
        $product->setName($data['name']);
        $product->setDescription($data['description']);
        $product->setImage($data['image']);
        $product->setCategory($data['category']);
        $product->setPrice($data['price']);
        $product->setQuantity($data['quantity']);
        $product->setInternalReference($data['internal_reference']);
        $product->setShellId($data['shell_id']);
        $product->setInventoryStatus($data['inventory_status']);
        $product->setRating($data['rating']);
        $product->setCreatedAt(new \DateTimeImmutable($data['created_at']));
        $product->setUpdatedAt(new \DateTimeImmutable($data['updated_at']));
        $repo->save($product, true);
        return $this->json(['message' => 'Product updated successfully']);
    }

    /**
     * @Route("/api/products", name="create_product", methods={"POST"})
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create a new product",
     *     description="Creates a new product with the provided data",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Product data to create",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="image", type="string"),
     *             @OA\Property(property="category", type="string"),
     *             @OA\Property(property="price", type="number"),
     *             @OA\Property(property="quantity", type="integer"),
     *             @OA\Property(property="internal_reference", type="string"),
     *             @OA\Property(property="shell_id", type="string"),
     *             @OA\Property(property="inventory_status", type="string"),
     *             @OA\Property(property="rating", type="number"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Product successfully created",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Product created successfully")
     *         )
     *     )
     * )
     */
    #[Route('/api/products', name: 'create_product', methods: ['POST'])]
    public function createProduct(Request $request, ProductsRepository $repo): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $product = new Product();
        $product->setName($data['name']);
        $product->setDescription($data['description']);
        $product->setImage($data['image']);
        $product->setCategory($data['category']);
        $product->setPrice($data['price']);
        $product->setQuantity($data['quantity']);
        $product->setInternalReference($data['internal_reference']);
        $product->setShellId($data['shell_id']);
        $product->setInventoryStatus($data['inventory_status']);
        $product->setRating($data['rating']);
        $product->setCreatedAt(new \DateTimeImmutable($data['created_at']));
        $product->setUpdatedAt(new \DateTimeImmutable($data['updated_at']));
        $repo->save($product, true);
        return $this->json(['message' => 'Product created successfully']);
    }
}

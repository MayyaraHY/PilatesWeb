<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ShoppingcartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }


    #[Route('/shop', name: 'app_product_shop', methods: ['GET'])]
    public function fetch(ProductRepository $productRepository, Request $request, PaginatorInterface $paginator): Response
    {
        // Get all products from the repository
        $productsQuery = $productRepository->findAll();

        // Paginate the results
        $products = $paginator->paginate(
            $productsQuery, // Query to paginate
            $request->query->getInt('page', 1), // Current page number
            1 // Number of items per page
        );

        return $this->render('product/shop.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form['image']->getData();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $this->getParameter('upload_directory'),
                $fileName);
            $product->setImage($fileName);

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{idproduct}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('fetch/{idproduct}', name: 'fetch_product', methods: ['GET'])]
    public function fetchproduct(Product $product): Response
    {
        return $this->render('product/fetch.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{idproduct}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form['image']->getData();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $this->getParameter('upload_directory'),
                $fileName);
            $product->setImage($fileName);

            $entityManager->flush();

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

   
    #[Route('/{idproduct}', name: 'app_product_delete', methods: ['POST'])]
public function delete(Request $request, Product $product, EntityManagerInterface $entityManager, ShoppingcartRepository $shoppingcartRepository): Response
{
    if ($this->isCsrfTokenValid('delete'.$product->getIdproduct(), $request->request->get('_token'))) {
        // Remove the product from any shopping carts
        $shoppingcarts = $shoppingcartRepository->findBy(['idproduct' => $product->getIdproduct()]);
        foreach ($shoppingcarts as $shoppingcart) {
            $entityManager->remove($shoppingcart);
        }

        // Remove the product
        $entityManager->remove($product);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
}




}

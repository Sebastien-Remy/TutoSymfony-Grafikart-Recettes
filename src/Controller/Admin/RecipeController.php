<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin/recettes', name: 'admin.recipe.')]
final class RecipeController extends AbstractController
{

    #[Route('/', name: 'index')]
    public function index(RecipeRepository $repository): Response
    {
        $recipes = $repository->findAll();
        return $this->render('admin/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }


    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $recipe->setCreatedAt(new \DateTimeImmutable());
            $recipe->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->persist($recipe);
            $entityManager->flush();
            $this->addFlash('success', 'Nouvelle recette ajoutée');
            return $this->redirectToRoute('admin.recipe.index');
        }

        return $this->render('admin/recipe/create.html.twig', [
                'form' => $form,
            ]
        );
    }

    #[Route('/{id}', name: 'edit', requirements: ['id' => Requirement::DIGITS], methods: ['GET', 'POST'])]
    public function edit(Request $request, Recipe $recipe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $recipe->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->flush();
            $this->addFlash('success', 'La recette à bien été modifiée');
            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('admin/recipe/edit.html.twig', [
                'recipe' => $recipe,
                'form' => $form,
            ]
        );
    }

    #[Route('/{id}', name: 'delete', requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])]
    public function delete(Recipe $recipe, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($recipe);
        $entityManager->flush();
        $this->addFlash('success', 'La recette à bien été supprimée');
        return $this->redirectToRoute('admin.recipe.index');
    }
}


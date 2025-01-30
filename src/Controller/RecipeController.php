<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RecipeController extends AbstractController
{

    #[Route('/recette', name: 'recipe.index')]
    public function index(RecipeRepository $repository): Response
    {
        $shorts = $repository->findWithDurationLowerThan(10);
        $recipes = $repository->findAll();
        return $this->render('recipe/index.html.twig', [
            'shorts' => $shorts,
            'recipes' => $recipes,
        ]);
    }
    #[Route('/recette/{slug}-{id}', name: 'recipe.show', requirements: ['slug' => '[a-z0-9-_]+' ,'id' => '\d+'])]
    public function show($slug, $id, RecipeRepository $repository): Response
    {
        $recipe = $repository->find($id);
        // bad slug
        if ($recipe->getSlug() !== $slug) {
            return $this->redirectToRoute('recipe.show', [
                'slug' => $recipe->getSlug(),
                'id' => $recipe->getId(),
                ], 301);
        }

        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
            ]
        );
    }

    #[Route('/recette/{id}/edit', name: 'recipe.edit', requirements: ['id' => '\d+'], methods: ['POST', 'GET'])]
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
        return $this->render('recipe/edit.html.twig', [
                'recipe' => $recipe,
                'form' => $form,
            ]
        );
    }
    #[Route('/recette/create', name: 'recipe.create')]
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
            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('recipe/create.html.twig', [
                'form' => $form,
            ]
        );
    }

    #[Route('/recette/{id}/delete', name: 'recipe.delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(Recipe $recipe, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($recipe);
        $entityManager->flush();
        $this->addFlash('success', 'La recette à bien été supprimée');
        return $this->redirectToRoute('recipe.index');
    }
}


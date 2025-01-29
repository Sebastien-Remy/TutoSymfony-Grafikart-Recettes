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

    #[Route('/recette/{id}/edit', name: 'recipe.edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Recipe $recipe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
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
}


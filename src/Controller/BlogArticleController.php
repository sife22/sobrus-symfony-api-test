<?php

namespace App\Controller;

use App\Entity\BlogArticle;
use App\Entity\User;
use App\Enum\StatusEnum;
use App\Repository\BlogArticleRepository;
use App\Service\Utils\BlogArticleUtils;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class BlogArticleController extends AbstractController
{
    // On récupére tous les articles 
    #[Route('/blog_articles', name: 'index', methods: 'GET')]
    public function index(BlogArticleRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $articles = $serializer->serialize($repository->findAll(), 'json');

        return new JsonResponse($articles, 200, ['Content-Type' => 'application/json'], true);
    }
    // =============================

    // On crée un nouvel article 
    #[Route('/blog_articles', name: 'store', methods: ['POST'])]
    public function store(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->request->get('data'), true);
    
        if (empty($data['title'])) {
            return new JsonResponse(['error' => 'Title is required.'], 400);
        }
        if (empty($data['content'])) {
            return new JsonResponse(['error' => 'Content is required.'], 400);
        }
        if (empty($data['author_id'])) {
            return new JsonResponse(['error' => 'Author ID is required.'], 400);
        }
    
        $author = $entityManager->getRepository(User::class)->find($data['author_id']);
        if (!$author) {
            return new JsonResponse(['error' => 'Author not found'], 404);
        }
    
        /** @var UploadedFile $file */
        $file = $request->files->get('cover_picture_ref');
    
        if (!$file) {
            return new JsonResponse(['error' => 'Picture is required.'], 400);
        }

    
        $blogArticle = new BlogArticle();
        $blogArticle->setAuthor($author);
        $blogArticle->setTitle($data['title']);

        $bannedWords = ['racism', 'hate', 'war'];
        $blogArticleUtils = new BlogArticleUtils();
        $filtredBlogArticleContent = $blogArticleUtils->getTopRepeatedWords($data['content'], $bannedWords);
        $blogArticle->setContent($data['content']);
        $blogArticle->setKeywords($filtredBlogArticleContent);

        $errors = $validator->validate($blogArticle);
    
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], 400);
        }

    
        $slugify = new Slugify();
        $slug = $slugify->slugify($blogArticle->getTitle());
        $blogArticle->setSlug($slug);
    
        if ($file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $slug . '-' . uniqid() . '.' . $file->guessExtension(); 
        
            $file->move(
                $this->getParameter('kernel.project_dir') . '/public/uploaded_pictures',
                $newFilename
            );
        
            $blogArticle->setCoverPictureRef($newFilename);
        }
    
        $currentDateTime = new \DateTime();
        $blogArticle->setCreationDate($currentDateTime);
        $blogArticle->setPublicationDate($currentDateTime);
    
        $blogArticle->setStatus(StatusEnum::PUBLISHED);
    
        $entityManager->persist($blogArticle);
        $entityManager->flush();
    
        return new JsonResponse($blogArticle->toArray(), 201, ['Content-Type' => 'application/json']);
    }
    // ===================================

    // On modifie un article par son identifiant 
    #[Route("/blog_articles/{id}", name: 'update', methods: ['PATCH'])]
    public function update(Request $request, BlogArticle $blogArticle, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->request->get('data'), true);

        if (empty($data['title'])) {
            return new JsonResponse(['error' => 'Title is required.'], 400);
        }
        if (empty($data['content'])) {
            return new JsonResponse(['error' => 'Content is required.'], 400);
        }
        if (empty($data['author_id'])) {
            return new JsonResponse(['error' => 'Author ID is required.'], 400);
        }

        $errors = $validator->validate($blogArticle);
    
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], 400);
        }

        $blogArticle->setTitle($data['title']);
        $blogArticle->setContent($data['content']);
        $blogArticle->setKeywords($data['keywords']);

        /** @var UploadedFile $file */
        $file = $request->files->get('cover_picture_ref');
        if ($file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $blogArticle->getSlug() . '-' . uniqid() . '.' . $file->guessExtension();

            $oldFilePath = $this->getParameter('kernel.project_dir') . '/public/uploaded_pictures/' . $blogArticle->getCoverPictureRef();
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }

            $file->move(
                $this->getParameter('kernel.project_dir') . '/public/uploaded_pictures',
                $newFilename
            );

            $blogArticle->setCoverPictureRef($newFilename);
        }

        $currentDateTime = new \DateTime();
        $blogArticle->setPublicationDate($currentDateTime);

        $entityManager->flush();

        return new JsonResponse($blogArticle->toArray(), 200, ['Content-Type' => 'application/json']);
    }

    // On récupére un article spécifique par son identifiant
    #[Route("/blog_articles/{id}", name: 'show', methods: 'GET')]
    public function show(BlogArticle $blogArticle, BlogArticleRepository $blogArticleRepository, int $id, SerializerInterface $serializer): JsonResponse
    {
        $article = $blogArticleRepository->find($id);
        
        if(!$article){
            return $this->json(['response'=>'Blog product not found!'], 404);
        }
        
        $data = $serializer->serialize($article, 'json');

        return new JsonResponse($data, 200, ['Content-Type' => 'application/json'], true);
    }
    // =======================================================

    // On supprime un article par son identifiant
    #[Route("/blog_articles/{id}", name: 'delete', methods: 'DELETE')]
    public function delete(BlogArticle $blogArticle, EntityManagerInterface $entityManager, int $id): JsonResponse
    {
    $coverPictureRef = $blogArticle->getCoverPictureRef();
    
    if ($coverPictureRef) {
        $filePath = $this->getParameter('kernel.project_dir') . '/public/uploaded_pictures/' . $coverPictureRef;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    $entityManager->remove($blogArticle);
    $entityManager->flush();

    return $this->json(['response' => 'Blog article deleted!']);
    }
    // ==========================================
}

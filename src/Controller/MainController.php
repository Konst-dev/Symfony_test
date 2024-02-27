<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\{Book, Author, Publisher};
use App\Repository\PublisherRepository;
use App\Service\DbService;
use Doctrine\Persistence\ManagerRegistry;

class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'message' => 'Hello World',
        ]);
    }


    #[Route('/bd', name: 'bd')]
    public function create(ManagerRegistry $doctrine): Response
    {
        $book = new Book();
        $book->setTitle('Новая книга');
        $book->setYear(2024);

        $author = new Author();
        $author->setFirstName('Иван');
        $author->setLastName('Иванов');

        $publisher = new Publisher();
        $publisher->setName('Издательство №1');
        $publisher->setAddress('СПб, Невский пр. д.1');

        $book->addAuthor($author);
        $book->setPublisher($publisher);

        //$doctrine = new ManagerRegistry();
        $entityManager = $doctrine->getManager();
        $entityManager->persist($book);
        $entityManager->persist($author);
        $entityManager->persist($publisher);
        $entityManager->flush();
        return $this->render('default/index.html.twig', [
            'message' => 'Книга: ' . $book->getId() . ' Автор: ' . $author->getId() . ' Издательство: ' . $publisher->getId(),
        ]);
    }

    #[Route('/bd2', name: 'bd')]
    public function create2(ManagerRegistry $doctrine): Response
    {
        $book = new Book();
        $book->setTitle('Еще книга');
        $book->setYear(2024);

        $author = $doctrine->getRepository(Author::class)->find(2);


        $publisher = $doctrine->getRepository(Publisher::class)->find(2);

        $book->addAuthor($author);
        $book->setPublisher($publisher);

        //$doctrine = new ManagerRegistry();
        $entityManager = $doctrine->getManager();
        $entityManager->persist($book);
        $entityManager->persist($author);
        $entityManager->persist($publisher);
        $entityManager->flush();
        return $this->render('default/index.html.twig', [
            'message' => 'Книга: ' . $book->getId() . ' Автор: ' . $author->getId() . ' Издательство: ' . $publisher->getId(),
        ]);
    }
}

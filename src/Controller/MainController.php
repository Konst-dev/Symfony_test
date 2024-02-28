<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\{Book, Author, Publisher};
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'message' => 'Hello World',
        ]);
    }

    #[Route('/books/getallbooks', name: 'get-all-books', methods: ['GET'])]
    public function getAllBooks(ManagerRegistry $doctrine) //возвращает response в виде JSON-объекта
    {
        $data = [];
        $books = $doctrine->getRepository(Book::class)->findAll();
        $i = 0;
        foreach ($books as $book) {
            $info = [
                'title' => $book->getTitle(),
                'year' => $book->getYear(),
                'last_names' => [],
                'publisher' => $book->getPublisher()->getName(),
            ];
            $authors = $book->getAuthor();
            foreach ($authors as $author) {
                $info['last_names'][] = $author->getLastName();
            }
            $data[] = $info;
        }

        $response = new Response();
        $response->setContent(json_encode($data, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    //Сохраняет нового автора. Вход: last_name и first_name
    #[Route('/books/createnewauthor', name: 'create-new-author', methods: ['POST'])]
    public function createNewAuthor(Request $request, EntityManagerInterface $em)
    {
        $last_name = $request->query->get('last_name');
        $first_name = $request->query->get('first_name');

        if ($last_name && $first_name) {
            $author = new Author();
            $author->setLastName($last_name);
            $author->setFirstName($first_name);
            $em->persist($author);
            $em->flush();
            return $this->json(['status' => 'OK']);
        }
        return $this->json(['status' => 'Error']);
    }


    //сохраняет новую книгу. Вход: title, year, author_id, publisher_id
    #[Route('/books/addnewbook', name: 'add-new-book', methods: ['POST'])]
    public function addNewBook(Request $request, EntityManagerInterface $em, ManagerRegistry $doctrine)
    {
        $title = $request->query->get('title');
        $year = $request->query->get('year');
        $author_id = $request->query->get('author_id');
        $publisher_id = $request->query->get('publisher_id');
        if ($title && $year && $author_id && $publisher_id) {
            $book = new Book();
            $book->setTitle($title);
            $book->setYear($year);
            $author = $doctrine->getRepository(Author::class)->find($author_id);
            $publisher = $doctrine->getRepository(Publisher::class)->find($author_id);
            $book->addAuthor($author);
            $book->setPublisher($publisher);
            $em->persist($book);
            $em->flush();
            return $this->json(['status' => 'OK']);
        }
        return $this->json(['status' => 'Error']);
    }

    //сохрааняет новые данные об издателе. Вход: id, name, address 
    #[Route('/books/editpublisher', name: 'edit-publisher', methods: ['POST'])]
    public function saveEditedPublisher(Request $request, EntityManagerInterface $em, ManagerRegistry $doctrine)
    {
        $id = $request->query->get('id');
        $name = $request->query->get('name');
        $address = $request->query->get('address');
        if ($id && $name && $address) {
            $publisher = $doctrine->getRepository(Publisher::class)->find($id);
            $publisher->setName($name);
            $publisher->setAddrees($address);
            $em->persist($publisher);
            $em->flush();
            return $this->json(['status' => 'OK']);
        }
        return $this->json(['status' => 'Error']);
    }
}

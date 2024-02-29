<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\{Book, Author, Publisher};
use App\Form\EditPublisherType;
use App\Form\NewAuthorType;
use App\Form\NewBookType;
use App\Repository\AuthorRepository;
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

    //Сохраняет нового автора. Вход: массив new_author с ключами last_name и first_name
    #[Route('/books/createnewauthor', name: 'create-new-author', methods: ['POST'])]
    public function createNewAuthor(Request $request, EntityManagerInterface $em)
    {
        $last_name = $request->request->all('new_author')['last_name'];
        $first_name = $request->request->all('new_author')['first_name'];

        if ($last_name && $first_name) {
            $author = new Author();
            $author->setLastName($last_name);
            $author->setFirstName($first_name);
            $em->persist($author);
            $em->flush();
            return $this->redirectToRoute('main', []);
        }
        return $this->redirectToRoute('main', []);;
    }


    //сохраняет новую книгу. Вход: массив new_book с ключами title, year, author(массив), publisher
    #[Route('/books/addnewbook', name: 'add-new-book', methods: ['POST'])]
    public function addNewBook(Request $request, EntityManagerInterface $em, ManagerRegistry $doctrine)
    {
        $title = $request->request->all('new_book')['title'];
        $year = $request->request->all('new_book')['year'];
        $author_ids = $request->request->all('new_book')['author'];
        $publisher_id = $request->request->all('new_book')['publisher'];
        if ($title && $year && count($author_ids) && $publisher_id) {
            $book = new Book();
            $book->setTitle($title);
            $book->setYear($year);
            //$author = $doctrine->getRepository(Author::class)->find($author_id);
            $publisher = $doctrine->getRepository(Publisher::class)->find($publisher_id);
            //$book->addAuthor($author);
            foreach ($author_ids as $author_id) {
                $author = $doctrine->getRepository(Author::class)->find($author_id);
                $book->addAuthor($author);
            }
            $book->setPublisher($publisher);
            $em->persist($book);
            $em->flush();
            return $this->redirectToRoute('main', []);
        }
        return $this->redirectToRoute('main', []);
    }

    //сохрааняет новые данные об издателе. Вход: массив edit_publisher с ключами id, name, address 
    #[Route('/books/editpublisher', name: 'edit-publisher', methods: ['POST'])]
    public function saveEditedPublisher(Request $request, EntityManagerInterface $em, ManagerRegistry $doctrine)
    {
        $id = $request->request->all('edit_publisher')['id'];
        $name = $request->request->all('edit_publisher')['name'];
        $address = $request->request->all('edit_publisher')['address'];
        if ($id && $name && $address) {
            $publisher = $doctrine->getRepository(Publisher::class)->find($id);
            $publisher->setName($name);
            $publisher->setAddress($address);
            $em->persist($publisher);
            $em->flush();
            return $this->redirectToRoute('main', []);
        }
        return $this->redirectToRoute('main', []);
    }

    //удаляет книгу. Вход: массив delete_book c ключом id
    #[Route('/books/deletebook', name: 'delete-book', methods: ['POST'])]
    public function deleteBook(Request $request, EntityManagerInterface $em, ManagerRegistry $doctrine)
    {
        $id = $request->request->all('delete_book')['id'];
        if ($id) {
            $book = $doctrine->getRepository(Book::class)->find($id);
            $em->remove($book);
            $em->flush();
            return $this->redirectToRoute('main', []);
        }
        return $this->redirectToRoute('main', []);
    }


    //удаляет Автора. Вход: массив delete_author c ключом id
    #[Route('/books/deleteauthor', name: 'delete-author', methods: ['POST'])]
    public function deleteAuthor(Request $request, EntityManagerInterface $em, ManagerRegistry $doctrine)
    {
        $id = $request->request->all('delete_author')['id'];
        if ($id) {
            $author = $doctrine->getRepository(Author::class)->find($id);
            $em->remove($author);
            $em->flush();
            return $this->redirectToRoute('main', []);
        }
        return $this->redirectToRoute('main', []);
    }

    //удаляет Издателя. Вход: массив delete_publisher c ключом id
    #[Route('/books/deletepublisher', name: 'delete-publisher', methods: ['POST'])]
    public function deletePublisher(Request $request, EntityManagerInterface $em, ManagerRegistry $doctrine)
    {
        $id = $request->request->all('delete_publisher')['id'];
        if ($id) {
            $publisher = $doctrine->getRepository(Publisher::class)->find($id);
            $em->remove($publisher);
            $em->flush();
            return $this->redirectToRoute('main', []);
        }
        return $this->redirectToRoute('main', []);
    }

    #[Route('/books/newauthorform', name: 'new-author-form', methods: ['GET'])]
    public function showNewAuthorForm()
    {
        $author = new Author();
        $form = $this->createForm(NewAuthorType::class, $author, [
            'action' => $this->generateUrl('create-new-author'),
            'method' => 'POST',
        ]);
        return $this->render('default/newAuthor.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/books/newbookform', name: 'new-book-form', methods: ['GET'])]
    public function showNewBookForm()
    {
        $book = new Book();
        $form = $this->createForm(NewBookType::class, $book, [
            'action' => $this->generateUrl('add-new-book'),
            'method' => 'POST',
        ]);
        return $this->render('default/newBook.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/books/editpublisherform/{id}', name: 'edit-publisher-form', methods: ['GET'])]
    public function showEditPublisherForm(int $id, ManagerRegistry $doctrine)
    {
        $publisher = $doctrine->getRepository(Publisher::class)->find($id);

        $form = $this->createForm(EditPublisherType::class, $publisher, [
            'action' => $this->generateUrl('edit-publisher'),
            'method' => 'POST',
        ]);
        return $this->render('default/editPublisher.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/books/deletebookform/', name: 'delete-book-form', methods: ['GET'])]
    public function showDeleteBookForm(ManagerRegistry $doctrine)
    {
        $books = $doctrine->getRepository(Book::class)->findAll();
        return $this->render('default/deleteBook.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/books/deleteauthorform/', name: 'delete-author-form', methods: ['GET'])]
    public function showDeleteAuthorForm(ManagerRegistry $doctrine)
    {
        $authors = $doctrine->getRepository(Author::class)->findAll();
        return $this->render('default/deleteAuthor.html.twig', [
            'authors' => $authors,
        ]);
    }

    #[Route('/books/deletepublisherform/', name: 'delete-publisher-form', methods: ['GET'])]
    public function showDeletePublisherForm(ManagerRegistry $doctrine)
    {
        $publishers = $doctrine->getRepository(Publisher::class)->findAll();
        return $this->render('default/deletePublisher.html.twig', [
            'publishers' => $publishers,
        ]);
    }
}

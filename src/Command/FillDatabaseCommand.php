<?php

namespace App\Command;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Publisher;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'fill-database',
    description: 'Заполняет базу данных тестовой информацией',
)]
class FillDatabaseCommand extends Command
{
    protected EntityManagerInterface $entityManager;
    protected ManagerRegistry $doctrine;

    public function __construct(EntityManagerInterface $em, ManagerRegistry $doctrine)
    {
        $this->entityManager = $em;
        $this->doctrine = $doctrine;
        parent::__construct();
    }

    protected function configure(): void
    {
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        for ($i = 1; $i <= 5; $i++) {
            $publisher = new Publisher();
            $publisher->setName('Издательство №' . $i);
            $publisher->setAddress('Адрес ' . $i);
            $publisher->setName('Издательство №' . $i);
            $this->entityManager->persist($publisher);
            $this->entityManager->flush();
        }
        $firstNames = [
            'Иван', 'Петр', 'Сергей', 'Антон', 'Борис', 'Николай', 'Роман', 'Павел', 'Максим',
            'Лев', 'Константин', 'Иннокентий', 'Артур', 'Артем', 'Владимир', 'Степан', 'Эдгар'
        ];
        $lastNames = [
            'Иванов', 'Петров', 'Сидоров', 'Козлов', 'Баранов', 'Воронько', 'Грановский', 'Артемьев',
            'Толстой', 'Медведев', 'Екимов', 'Алексеев', 'Корнеев', 'Кинчев'
        ];
        for ($i = 1; $i <= 10; $i++) {
            $author = new Author();
            $n = array_rand($firstNames);
            $author->setFirstName($firstNames[$n]);
            unset($firstNames[$n]);
            $n = array_rand($lastNames);
            $author->setLastName($lastNames[$n]);
            unset($lastNames[$n]);
            $this->entityManager->persist($author);
            $this->entityManager->flush();
        }

        $publishers = $this->doctrine->getRepository(Publisher::class)->findAll();
        $authors = $this->doctrine->getRepository(Author::class)->findAll();

        for ($i = 1; $i <= 20; $i++) {
            $book = new Book();
            $book->setTitle('Название книги ' . $i);
            $book->setYear(rand(1950, 2024));
            $n = rand(0, count($authors) - 2); //оставляем одного автора без книг для тестирования удаления
            $book->addAuthor($authors[$n]);
            $m = rand(0, count($publishers) - 1);
            $book->setPublisher($publishers[$m]);

            $this->entityManager->persist($book);
            $this->entityManager->flush();
        }

        $io->success('База данных успешно заполнена!');
        return Command::SUCCESS;
    }
}

<?php

namespace App\Command;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Publisher;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\ORMSetup;
use PHPUnit\Framework\Constraint\Count;

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
        // $this
        //     ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
        //     ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        // ;
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        // $arg1 = $input->getArgument('arg1');

        // if ($arg1) {
        //     $io->note(sprintf('You passed an argument: %s', $arg1));
        // }

        // if ($input->getOption('option1')) {
        //     // ...
        // }

        // $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

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
            $n = rand(0, count($authors) - 1);
            $book->addAuthor($authors[$n]);
            $m = rand(0, count($publishers) - 1);
            $book->setPublisher($publishers[$m]);

            $this->entityManager->persist($book);
            //$this->entityManager->persist($authors[$n]);
            //$this->entityManager->persist($publishers[$m]);
            $this->entityManager->flush();
        }


        //$str = count($publishers);



        // $io->success($str);
        $io->success('База данных успешно заполнена!');
        return Command::SUCCESS;
    }
}

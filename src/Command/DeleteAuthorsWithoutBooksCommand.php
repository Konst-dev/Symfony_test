<?php

namespace App\Command;

use App\Entity\Author;
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
    name: 'delete-authors-without-books',
    description: 'Удаляет авторов, у которых нет книг',
)]
class DeleteAuthorsWithoutBooksCommand extends Command
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

        $authors = $this->doctrine->getRepository(Author::class)->findAll();

        $str = 'Следующие авторы были удалены: ';
        foreach ($authors as $author) {
            $books = $author->getBooks();
            if (!count($books)) {
                $str .= $author->getLastName() . " " . $author->getFirstName() . ";  ";
                $this->entityManager->remove($author);
                $this->entityManager->flush();
            }
        }

        $io->success($str);

        return Command::SUCCESS;
    }
}

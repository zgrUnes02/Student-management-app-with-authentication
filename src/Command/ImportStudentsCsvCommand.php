<?php

namespace App\Command;

use App\Entity\Student;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[AsCommand(
    name: 'app:ImportStudentsCsv',
    description: 'Import students form csv file to database',
)]
class ImportStudentsCsvCommand extends Command
{
    private SymfonyStyle $io ;
    private EntityManagerInterface $entityManager ;
    private string $myDirectory ;
    private StudentRepository $studentRepository ;

    public function __construct( EntityManagerInterface $entityManager , string $myDirectory , StudentRepository $studentRepository )
    {
        parent::__construct();
        $this -> entityManager = $entityManager ;
        $this -> studentRepository = $studentRepository ;
        $this -> myDirectory = $myDirectory ;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this -> io = new SymfonyStyle($input , $output) ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this -> createStudents() ;

        return Command::SUCCESS;
    }

    private function getDataFromFile() 
    {
        $file = $this -> myDirectory . 'students.csv' ;
        $fileExtension = pathinfo($file , PATHINFO_EXTENSION) ;
        $normalizer = [new ObjectNormalizer()] ;
        $encoders = [ new CsvEncoder() ] ;
        $serializer = new Serializer($normalizer , $encoders) ;
        $fileContent = file_get_contents($file) ;
        $data = $serializer -> decode($fileContent , $fileExtension) ;
        return $data ;
    }

    private function createStudents(): void
    {
        //! To calculate how many student imported in database
        $howManyUserCreated = 0 ;

        foreach ( $this -> getDataFromFile() as $row ) {

            //* I ignored the checker for checking is there is any student in database 
            //* because i found some problems 

            // Check if the student in csv file exists in database 
            // $student = $this -> studentRepository -> findOneBy(['first_name' , $row['first_name']]) ;

            //! If there is no student with this name
            // if ( !$student ) {

            //! Add student to database
            $student = new Student() ;
            $student -> setFirstName($row['first_name']) 
                        -> setLastName($row['last_name']) 
                        -> setAge($row['age']) ;

            //! Persist the student
            $this -> entityManager -> persist($student) ;

            //! Increment the counter
            $howManyUserCreated += 1 ;

            // }

        }

        //! Flush student
        $this -> entityManager -> flush() ;

        if ( $howManyUserCreated > 0 ) {
            $message = "{$howManyUserCreated} students has been imported with success !" ;
        } else if ( $howManyUserCreated == 1 ) {
            $message = "1 student has been imported with success !" ;
        } else {
            $message = " No student has been imported !" ;
        }

        $this -> io -> success($message) ;
    }
}

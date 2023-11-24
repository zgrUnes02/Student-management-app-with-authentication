<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends AbstractController
{

    //! The first page
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        
        return $this -> render('student/home.html.twig');
    }

    //! Page that display all students
    #[Route('/students', name: 'students_page')]
    public function getAllStudents(StudentRepository $studentRepository): Response
    {   
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        //* Get all students 
        $students = $studentRepository -> findAll();

        //* Display the template with all students
        return $this -> render('student/students.html.twig' , [
            'students' => $students ,
        ]) ;
    }

    //! Page for creating new student
    #[Route('/students/create', name:'create_student_page')]
    public function create(Request $request , EntityManagerInterface $em): Response
    {   
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $student = new Student() ;

        $form = $this -> createForm(StudentType::class , $student);

        $form -> handleRequest($request);

        if ( $form -> isSubmitted() && $form -> isValid() ) {

            //! Insert data from the form and store it in $student
            $student = $form -> getData();

            //! Persist and flush
            $em -> persist($student);
            $em -> flush();

            flash() -> addSuccess('The student has been created with success !');
            return $this -> redirectToRoute('students_page') ; 
        }

        return $this -> render('student/create.html.twig' , [
            'form' => $form -> createView() ,
        ]) ;
    }

    //! Page that show one student information
    #[Route('/students/{id}', name:'show_student_page')]
    public function show(StudentRepository $studentRepository , int $id): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $student = $studentRepository -> find($id) ;

        if ( !$student ) {
            return $this -> render('student/error404.html.twig') ;
        }

        return $this -> render('student/show.html.twig' , [
            'student' => $student ,
        ]) ;
    }

    //! Update a student
    #[Route('/students/update/{id}', name:'update_student')]
    public function updateStudent(Request $request , Student $student , EntityManagerInterface $em): Response
    {   
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this -> createForm(StudentType::class , $student);

        $form -> handleRequest($request);

        if ( $form -> isSubmitted() && $form -> isValid() ) {

            //! Insert data from the form and store it in $student
            $student = $form -> getData();

            //! Persist and flush
            $em -> persist($student);
            $em -> flush();

            flash() -> addSuccess('The student has been updated with success !');
            return $this -> redirectToRoute('students_page') ; 
        }

        return $this -> render('student/edit.html.twig' , [
            'form' => $form -> createView() ,
        ]) ;
    }
    

    //! Delete a student
    #[Route('/students/delete/{id}', name:'delete_student')]
    public function deleteStudent(Student $student , EntityManagerInterface $em): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $em -> remove($student);
        $em -> flush() ;

        flash() -> success('The student has been deleted with success !');
        return $this -> redirectToRoute('students_page') ; 
    
    }

    //! Generate pdf for a student
    #[Route('/students/generate/pdf/{id}', name:'generate_student_pdf')]
    public function generateStudentPdf(Student $student = null) 
    {
        //
    }
}

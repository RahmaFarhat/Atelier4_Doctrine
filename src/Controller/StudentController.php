<?php

namespace App\Controller;

use App\Entity\Classroom;
use App\Form\RechercheType;
use App\Form\SearchByMoyenneType;
use App\Form\StudentType;
use App\Entity\Student;
use App\Repository\ClassroomRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Repository\StudentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends AbstractController
{
    #[Route('/fetchStudent', name: 'fetchStudent')]
    public function fetchStudent(StudentRepository $rep): Response
    {
        $students = $rep->findAll();
        return $this->render(
            "student/read.html.twig",
            ["students" => $students]
        );
    }
    #[Route('/addStudent', name: 'addStudent')]
    public function  addStudent(ManagerRegistry $doctrine, Request  $request): Response
    {
        $student = new Student();
        $form = $this->createForm(StudentType::class, $student);
        $form->add('ajouter', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $doctrine->getManager();
            $em->persist($student);
            $em->flush();


            return $this->redirectToRoute('fetchStudent');
        }
        return $this->renderForm(
            "student/add.html.twig",
            ["f" => $form]
        );
    }





    #[Route('/student/modifier/{nsc}', name: 'app_student_modifier')]
    public function modifier(Request $request, ManagerRegistry $doctrine, $nsc): Response
    {
        $repository = $doctrine->getRepository(Student::class);
        $em = $doctrine->getManager();
        $student = $repository->find($nsc);
        $form = $this->createForm(StudentType::class, $student);
        $form->add('modifier', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em->flush();
            return $this->redirectToRoute('app_student_read');
        }

        return $this->renderForm('student/modifier.html.twig', array('form' => $form));
    }






    #[Route('/student/read', name: 'app_student_read')]
    public function read(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Student::class);
        $list = $repository->findAll();
        return $this->render('student/index.html.twig', [
            'students' => $list,

        ]);

        return $this->redirectToRoute('app_student_read');
    }




    #[Route('/student/delete/{nsc}', name: 'app_student_delete')]
    public function delete(StudentRepository $repository, $nsc, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $student = $repository->find($nsc);
        $em->remove($student);
        $em->flush();
        return $this->redirectToRoute('app_student_read');
    }
}

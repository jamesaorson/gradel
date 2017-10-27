<?php

namespace AppBundle\Controller;

use \DateTime;

use AppBundle\Entity\User;
use AppBundle\Entity\Course;
use AppBundle\Entity\UserSectionRole;
use AppBundle\Entity\Role;
use AppBundle\Entity\Section;
use AppBundle\Entity\Assignment;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


use Psr\Log\LoggerInterface;


class SectionController extends Controller
{
    public function sectionAction($userId, $sectionId) {

      $em = $this->getDoctrine()->getManager();

      $builder = $em->createQueryBuilder();
      $builder->select('section')
              ->from('AppBundle\Entity\Section section')
              ->where('section.id = :id')
              ->setParameter("id", $sectionId);
      $query = $builder->getQuery();
      $section = $query->getSingleResult();

			$qb = $em->createQueryBuilder();
			$qb->select('u')
					->from('AppBundle\Entity\User', 'u')
					->where('u.id = ?1')
					->setParameter(1, $userId);

			$query = $qb->getQuery();
			$user = $query->getSingleResult();

			$qb->select('usr')
					->from('AppBundle\Entity\UserSectionRole', 'usr')
					->where('usr.user = ?1')
					->andWhere('usr.section = ?2')
					->setParameter(1, $user->id)
					->setParameter(2, $sectionId);


			$query = $qb->getQuery();
			$usr = $query->getSingleResult();

      return $this->render('default/section/index.html.twig', [
        'section' => $section,
        'userId' => $userId,
        'sectionId' => $sectionId,
				'usr' => $usr,
      ]);
    }

		public function newSectionAction($userId) {

      $em = $this->getDoctrine()->getManager();
      $builder = $em->createQueryBuilder();

      $builder->select('c')
              ->from('AppBundle\Entity\Course', 'c')
              ->where('1 = 1');
      $query = $builder->getQuery();
      $sections = $query->getResult();


      $section = new Section();
      $form = $this->createFormBuilder($section)
                    ->add('name', TextType::class)
                    ->add('year', DateType::class)
                    ->add('save', SubmitType::class, array('label' => 'Create Section'))
                    ->getForm();

      $builder = $em->createQueryBuilder();
      $builder->select('u')
              ->from('AppBundle\Entity\User', 'u')
              ->where('1 = 1');
      $query = $builder->getQuery();
      $users = $query->getResult();

			return $this->render('default/section/new.html.twig', [
        'userId' => $userId,
        'sections' => $sections,
        'users' => $users,
        'form' => $form->createView(),
			]);
		}

    public function insertSectionAction(Request $request, $userId, $courseId, $name, $students, $semester, $year, $start_time, $end_time, $is_public, $is_deleted) {

      echo "<br/>";

      $em = $this->getDoctrine()->getManager();
      $course = $em->find('AppBundle\Entity\Course', $courseId);
      $section = new Section();
      $section->name = $name;
      $section->course = $course;
      $section->semester = $semester;
      $section->year = $year;
      $section->start_time =  new DateTime("now");
      $section->end_time = new DateTime("now");
      $section->is_deleted = $is_deleted;
      $section->is_public = $is_public;

      $em->persist($section);
      $em->flush();

      // TODO: user section role insert for prof
      $role = $em->getRepository('AppBundle\Entity\Role')->findOneBy(array('role_name' => 'Teaches'));
      $teacher = $em->find('AppBundle\Entity\User', $userId);
      $usr = new UserSectionRole($teacher, $section, $role);
      $em->persist($usr);
      $em->flush();


      // insert students into the course
      $role = $em->getRepository('AppBundle\Entity\Role')->findOneBy(array('role_name' => 'Takes'));

      foreach (json_decode($students) as $student) {
        echo $student;
        echo "<br/>";

        if ($student != "") {
          $user = $em->getRepository('AppBundle\Entity\User')->findOneBy(array('email' => $student));

          echo $user->getFirstName();
          echo "<br/>";

          $usr = new UserSectionRole();
          $usr->user = $user;
          $usr->role = $role;
          $usr->section = $section;
          $em->persist($usr);
          $em->flush();
        }
      }

      return new Response("it worked");
    }

    private function generateDateTime($year, $date) {
      // TODO: create string based on start and end times for the fields in dcctrine entity
    }
}

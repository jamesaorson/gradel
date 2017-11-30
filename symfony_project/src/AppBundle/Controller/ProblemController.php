<?php

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Submission;
use AppBundle\Entity\Problem;
use AppBundle\Entity\ProblemLanguage;
use AppBundle\Entity\UserSectionRole;

use AppBundle\Utils\Grader;
use AppBundle\Utils\TestCaseCreator;

use Psr\Log\LoggerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\Common\Collections\ArrayCollection;

class ProblemController extends Controller {

    public function newAction($sectionId, $assignmentId) {
		$em = $this->getDoctrine()->getManager();
		$qb = $em->createQueryBuilder();

		$qb->select('l')
			->from('AppBundle\Entity\Language', 'l')
			->where('1 = 1');

		$languages = $qb->getQuery()->getResult();
		
		$section = $em->find('AppBundle\Entity\Section', $sectionId);
		$assignment = $em->find('AppBundle\Entity\Assignment', $assignmentId);
		
		

		return $this->render('problem/new.html.twig', [
			'languages' => $languages,
			'section' => $section,
			'assignment' => $assignment,
		]);
    }

	public function editAction($sectionId, $assignmentId, $problemId) {
		$em = $this->getDoctrine()->getManager();
		$qb = $em->createQueryBuilder();

		$qb->select('l')
			->from('AppBundle\Entity\Language', 'l')
			->where('1 = 1');

		$languages = $qb->getQuery()->getResult();
		
		$section = $em->find('AppBundle\Entity\Section', $sectionId);
		$assignment = $em->find('AppBundle\Entity\Assignment', $assignmentId);
		$problem = $em->find('AppBundle\Entity\Problem', $problemId);
		

		return $this->render('problem/new.html.twig', [
			'languages' => $languages,
			'section' => $section,
			'assignment' => $assignment,
			'problem' => $problem,
		]);
    }
	
	public function deleteAction($sectionId, $assignmentId, $problemId){

		$em = $this->getDoctrine()->getManager();

		$problem = $em->find('AppBundle\Entity\Problem', $problemId);
		if(!$problem){
			die("PROBLEM DOES NOT EXIST");
		}

		$user = $this->get('security.token_storage')->getToken()->getUser();
		if(!$user){
			die("USER DOES NOT EXIST");
		}

		# validate the user
		$grader = new Grader($em);
		if(!$user->hasRole("ROLE_SUPER") && !$user->hasRole("ROLE_ADMIN") && !$grader->isTeaching($user, $problem->assignment->section)){
			die("YOU ARE NOT ALLOWED TO DELETE THIS PROBLEM");
		}

		$em->remove($problem);
		$em->flush();
		return $this->redirectToRoute('assignment', ['sectionId' => $problem->assignment->section->id, 'assignmentId' => $problem->assignment->id]);
	}
	
	public function modifyPostAction(Request $request) {
		
		$em = $this->getDoctrine()->getManager();
				
		# validate the current user
		$user = $this->get('security.token_storage')->getToken()->getUser();
		if(!$user){			
			return $this->returnForbiddenResponse("You are not a user.");
		}
		
		# see which fields were included
		$postData = $request->request->all();		
		
		# get the current assignment
		$assignment = $em->find('AppBundle\Entity\Assignment', $postData['assignmentId']);		
		if(!$assignment){
			return $this->returnForbiddenResponse("Assignment ".$postData['assignmentId']." does not exist");
		}
		
		# only super users/admins/teacher can make/edit an assignment
		$grader = new Grader($em);		
		if(!$user->hasRole("ROLE_SUPER") && !$user->hasRole("ROLE_ADMIN") && !$grader->isTeaching($user, $assignment->section)){			
			return $this->returnForbiddenResponse("You do not have permission to make a problem.");
		}		
			
		
		
		# get the problem or create a new one
		if($postData['problem'] == 0){
			
			$problem = new Problem();		
			$problem->assignment = $assignment;
			$em->persist($problem);
			
		} else {
			
			$problem = $em->find('AppBundle\Entity\Problem', $postData['problem']);
			
			if(!$problem || $assignment != $problem->assignment){
				return $this->returnForbiddenResponse("Problem ".$postData['problem']." does not exist for the given assignment.");
			}			
		}
		
						
		# check mandatory fields		
		if(!isset($postData['name']) || trim($postData['name']) == "" || !isset($postData['description']) || trim($postData['description']) == "" || !isset($postData['weight']) || !isset($postData['time_limit'])){
			
			return $this->returnForbiddenResponse("Not every necessary field was provided");
			
		} else {
			
			if(!is_numeric($postData['weight']) || (int)$postData['weight'] < 1){
				return $this->returnForbiddenResponse("Weight provided is not valid - it must be greater than 0");
			}
			
			if(!is_numeric($postData['time_limit']) || (int)$postData['time_limit'] < 1){
				return $this->returnForbiddenResponse("Time limit provided is not valid - it must be greater than 0");
			}
			
		}
		
		$problem->name = trim($postData['name']);
		$problem->description = trim($postData['description']);
		$problem->weight = $postData['weight'];
		$problem->is_extra_credit = ($postData['is_extra_credit'] == "true");		
		$problem->time_limit = $postData['time_limit'];
		
		if(!isset($postData['languages']) || !isset($postData['testcases'])){
			
			return $this->returnForbiddenResponse("Not every necessary field was provided");
			
		} else {
			
			if(count($postData['languages']) < 1){
				return $this->returnForbiddenResponse("You must specify at least one language");
			}
			
			if(count($postData['testcases']) < 1){
				return $this->returnForbiddenResponse("You must specify at least one test case");
			}
			
		}
		
		# check the optional fields		
		# attempt penalties
		$total_attempts = $postData['total_attempts'];
		$attempts_before_penalty = $postData['attempts_before_penalty'];
		$penalty_per_attempt = $postData['penalty_per_attempt'];

		if(!isset($total_attempts) || !is_numeric($total_attempts) || !isset($attempts_before_penalty) || !is_numeric($attempts_before_penalty) || !isset($penalty_per_attempt) || !is_numeric($penalty_per_attempt)){
			return $this->returnForbiddenResponse("Not every necessary grading method flag was set properly");
		}
		
		if($total_attempts < $attempts_before_penalty){
			return $this->returnForbiddenResponse("Attempts before penalty must be greater than the total attempts");
		}
		
		if($penalty_per_attempt < 0.00 || $penalty_per_attempt > 1.00){
			return $this->returnForbiddenResponse("Penalty per attempts must be between 0 and 1");
		} else{
			$total_attempts = 0;
			$attempts_before_penalty = 0;
			$penalty_per_attempt = 0.00;
		}
		
		$problem->total_attempts = $total_attempts;
		$problem->attempts_before_penalty = $attempts_before_penalty;
		$problem->penalty_per_attempt = $penalty_per_attempt;
		
		
		# feedback flags
		$stop_on_first_fail = $postData['stop_on_first_fail'];
		$response_level = $postData['response_level'];
		$display_testcaseresults = $postData['display_testcaseresults'];
		$testcase_output_level = $postData['testcase_output_level'];
		$extra_testcases_display = $postData['extra_testcases_display'];
		
		if($stop_on_first_fail != null || $response_level != null || $display_testcaseresults != null || $testcase_output_level != null || $extra_testcases_display != null){
			
			if($stop_on_first_fail == null || $response_level == null || $display_testcaseresults == null || $testcase_output_level == null || $extra_testcases_display == null){
				return $this->returnForbiddenResponse("Not every necessary feedback flag was set");
			}
			
			if($response_level != "Long" && $response_level != "Short" && $response_level != "None"){
				return $this->returnForbiddenResponse("Response level is not a valid string value");
			}
			
			if($testcase_output_level != "Both" && $testcase_output_level != "Output" && $testcase_output_level != "None"){
				return $this->returnForbiddenResponse("Testcase output level is not a valid string value");
			}
			
		} else {
			$stop_on_first_fail = false;
			$response_level = "Long";
			$display_testcaseresults = true;
			$testcase_output_level = "Both";
			$extra_testcases_display = true;
		}
				 
		$problem->stop_on_first_fail = ($stop_on_first_fail == "true");
		$problem->response_level = $response_level;
		$problem->display_testcaseresults = ($display_testcaseresults == "true");
		$problem->testcase_output_level = $testcase_output_level;
		$problem->extra_testcases_display = ($extra_testcases_display == "true");

		# go through the problemlanguages
		foreach($problem->problem_languages as $pl){			
			$em->remove($pl);			
		}
		
		
		foreach(array_unique($postData['languages']) as $l) {
			
			$language = $em->find("AppBundle\Entity\Language", $l);
			
			if(!$language){
				return $this->returnForbiddenResponse("Provided language with id ".$l." does not exist");
			}
			
			$problemLanguage = new ProblemLanguage();

			$problemLanguage->language = $language;
			$problemLanguage->problem = $problem;
			$em->persist($problemLanguage);			
		}
	
		# go through the testcases array provided if this was a new problem
		if($postData['problem'] == 0){
			
			$count = 1;
			foreach($postData['testcases'] as $tc){
			
				if(!is_array($tc)){
					return $this->returnForbiddenResponse("Testcase data is not formatted properly");
				}
				
				# build the testcase 
				$response = TestCaseCreator::makeTestCase($em, $problem, $tc, $count);
				$count++;
								
				# check what the makeTestCase returns
				if(!$response->problem){
					return $response;
				} else{
					$testcase = $response;
				}
				
				$em->persist($testcase);
			}
		}
		
		$em->flush();

		return new JsonResponse(array("problemId"=> $problem->id));
	}

	public function resultAction($submission_id) {

		$em = $this->getDoctrine()->getManager();
		$grader = new Grader($em);

		$submission = $em->find("AppBundle\Entity\Submission", $submission_id);

		if(!$submission){
			die("SUBMISSION DOES NOT EXIST");
		}

		# get the user
		$user = $this->get('security.token_storage')->getToken()->getUser();
		if(!$user){
			die("USER DOES NOT EXIST!");
		}

		# make sure the user has permissions to view the submission result
		if($user->hasRole("ROLE_SUPER") && !$grader->isTeaching($user, $submission->problem->assignment->section) && !$grader->isOnTeam($user, $submission->problem->assignment, $submission->team)){
			echo "YOU ARE NOT ALLOWED TO VIEW THIS SUBMISSION";
			die();
		}

		$grader = new Grader($em);
		$feedback = $grader->getFeedback($submission);
		
		# get the usersectionrole
		$qb_usr = $em->createQueryBuilder();
		$qb_usr->select('usr')
			->from('AppBundle\Entity\UserSectionRole', 'usr')
			->where('usr.user = ?1')
			->andWhere('usr.section = ?2')
			->setParameter(1, $user)
			->setParameter(2, $submission->problem->assignment->section);
			
		$usr_query = $qb_usr->getQuery();
		$usersectionrole = $usr_query->getOneOrNullResult();

		return $this->render('problem/result.html.twig', [
			'section' => $submission->problem->assignment->section,
			'assignment' => $submission->problem->assignment,
			'problem' => $submission->problem,
			'submission' => $submission,
			'grader' => new Grader($em),
			'result_page' => true,
			'feedback' => $feedback,
			'usersectionrole' => $usersectionrole,
		]);
	}
	
	private function returnForbiddenResponse($message){		
		$response = new Response($message);
		$response->setStatusCode(Response::HTTP_FORBIDDEN);
		return $response;
	}

}





?>

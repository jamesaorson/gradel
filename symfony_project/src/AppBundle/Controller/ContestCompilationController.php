<?php

namespace AppBundle\Controller;

use \DateTime;
use \DateInterval;

use AppBundle\Entity\User;
use AppBundle\Entity\Course;
use AppBundle\Entity\UserSectionRole;
use AppBundle\Entity\Role;
use AppBundle\Entity\Query;
use AppBundle\Entity\Section;
use AppBundle\Entity\Assignment;
use AppBundle\Entity\Submission;
use AppBundle\Entity\Problem;
use AppBundle\Entity\Team;
use AppBundle\Entity\Testcase;
use AppBundle\Entity\ProblemLanguage;
use AppBundle\Entity\Leaderboard;

use Symfony\Component\Config\Definition\Exception\Exception;

use AppBundle\Utils\Grader;
use AppBundle\Utils\Generator;
use AppBundle\Utils\SocketPusher;
use AppBundle\Utils\Uploader;
use AppBundle\Utils\Zipper;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Psr\Log\LoggerInterface;


class ContestCompilationController extends Controller {


	/* submit */
	public function contestSubmitAction(Request $request, $trialId=0) {

        # entity manager
        $em = $this->getDoctrine()->getManager();		
        $grader = new Grader($em);
        
        # get the current user
		$user= $this->get('security.token_storage')->getToken()->getUser();		
		if(!$user){
			return $this->returnForbiddenResponse("USER DOES NOT EXIST");
        }
        
        # forward to actual compilation controller
        $response = $this->forward('AppBundle\Controller\CompilationController::submitAction', [
            'trialId' => $trialId,
            // password to allow the contest to run the submit controller
            'forwarded' => "secret_code",
        ]);

        $submission_id = json_decode($response->getContent())->submission_id;

        if(!$submission_id){
            return $response;
        }

        $submission = $em->find("AppBundle\Entity\Submission", $submission_id);

        if( !$submission->problem->assignment->post_contest && !$submission->problem->assignment->pre_contest && $grader->getTeam($user, $submission->problem->assignment) && !$submission->isCorrect() && !$submission->isError() ){

            $submission->pending_status = 0;			

            $em->persist($submission);
            $em->flush();
        } 

        $contest = $submission->problem->assignment;
        
        // SOCKET PUSHER UPDATE
        $pusher = new SocketPusher($this->container->get('gos_web_socket.wamp.pusher'), $em, $contest);
        if($submission->pending_status != 0){   
            
            // UPDATE LEADERBOARD
            $contest->updateLeaderboard($grader, $em);
            $pusher->sendGradedSubmission($submission);     
            $pusher->sendScoreboardUpdates();
            
        } else {
            // send the ungraded one to the judges for grading
		    $pusher->sendNewSubmission($submission);
        }
				
        $url = $this->generateUrl('contest_result', [
            'contestId' => $submission->problem->assignment->section->id,
            'roundId' => $submission->problem->assignment->id,
            'problemId' => $submission->problem->id,
            'resultId' => $submission->id,
        ]);

        $response = new Response(json_encode([		
			'redirect_url' => $url,	
			'submission_id' => $submission->id,		
        ]));

		$response->headers->set('Content-Type', 'application/json');
		$response->setStatusCode(Response::HTTP_OK);		
        
        return $response;
    } 

	public function contestQuickAction(Request $request){
				
		$response = $this->forward('AppBundle\Controller\TrialController::trialModifyAction');
				
		if($response->getStatusCode() == Response::HTTP_OK){
				
			return $this->forward('AppBundle\Controller\ContestCompilationController::contestSubmitAction', [
				'trialId' => json_decode($response->getContent())->trial_id,
			]);
			
			
		} else {			
			return $response;	
		}	
	}

    private function returnForbiddenResponse($message){		
		$response = new Response($message);
		$response->setStatusCode(Response::HTTP_FORBIDDEN);
		return $response;
    }
    
    
}

?>
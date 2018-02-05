<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Submission;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Entity\Team;
use AppBundle\Entity\Course;
use AppBundle\Entity\Section;
use AppBundle\Entity\Assignment;
use AppBundle\Entity\Problem;
use AppBundle\Entity\UserSectionRole;
use AppBundle\Entity\Testcase;
use AppBundle\Entity\Language;
use AppBundle\Entity\Feedback;
use AppBundle\Entity\TestcaseResult;
use AppBundle\Entity\Trial;

use AppBundle\Utils\Uploader;
use AppBundle\Utils\Generator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use \DateTime;


class TrialController extends Controller {

	public function trialModifyAction(Request $request){
		
		# entity manager
		$em = $this->getDoctrine()->getManager();		
		
		# gets the gradel/symfony_project directory
		$web_dir = $this->get('kernel')->getProjectDir()."/";
			
		$generator = new Generator($em, $web_dir);			
		$uploader = new Uploader($web_dir);
						
		# get the current user
		$user= $this->get('security.token_storage')->getToken()->getUser();
		
		if(!$user){
			return $this->returnForbiddenResponse("USER DOES NOT EXIST");
		}
		
		# stores all of the data from the post
		$postData = $request->request->all();	
		$files = $request->files;
		
		# get the problem
		$problem_id = $postData['problem_id'];		
		$problem = $em->find('AppBundle\Entity\Problem', $problem_id);
		
		if(!$problem){
			return $this->returnForbiddenResponse("Problem with id ".$problem_id." does not exist");
		}
		
		# get the file content for the trial
		if($files->get('file')){
			
			$file = $files->get('file');
			
			if($file->getClientSize() > 1048576){
				return $this->returnForbiddenResponse("Given file is too large");
			}
			$target_file = $uploader->uploadSubmissionFile($file, $user, $problem);
			
			$file = fopen($target_file, 'r');
			
			if(!$file){
				return $this->returnForbiddenResponse("Could not properly upload file");
			}
			
		} else if(isset($postData['ACE'])){
			
			$file = $postData['ACE'];
			
		} else {			
			return $this->returnForbiddenResponse("File or ACE editor content was not provided");			
		}
		
		# get the old trial or create a new one
		$qb_trial = $em->createQueryBuilder();
		$qb_trial->select('t')
				->from('AppBundle\Entity\Trial', 't')
				->where('t.user = ?1')
				->andWhere('t.problem = ?2')
				->setParameter(1, $user)
				->setParameter(2, $problem);

		$trial_query = $qb_trial->getQuery();
		$trial = $trial_query->getOneorNullResult();
		
		if(!$trial){
			$trial = new Trial();
			$trial->problem = $problem;
			$trial->user = $user;
			
			$em->persist($trial);
		}
		
		$trial->last_edit_time = new \DateTime('now');
		$trial->show_description = $postData["show_description"] != "false";
				
		# get filename and information
		$filename = null;
		$main_class = null;
		$package_name = null;
		$language = null;
		
		$response = $generator->generateFilename($filename, $language, $main_class, $package_name, $problem, $postData);
		
		if($response != 1){
			return $this->returnForbiddenResponse("Cannot save: ".$response);
		}	
		
		// make it zip if it's a zip
		if(pathinfo($target_file, PATHINFO_EXTENSION) == "zip"){
			
			$filename = pathinfo($target_file, PATHINFO_BASENAME);
		}
		
		
		$trial->file = $file;
		$trial->filename = $filename;
		$trial->language = $language;
		$trial->main_class = $main_class;
		$trial->package_name = $package_name;
		
		$em->persist($trial);
		$em->flush();
		
		# RETURN THE ID OF THE TRIAL
		$response = new Response(json_encode([		
			'trial_id' => $trial->id,			
		]));
		
		$response->headers->set('Content-Type', 'application/json');
		$response->setStatusCode(Response::HTTP_OK);
	
		return $response;
	}
	
	
	private function returnForbiddenResponse($message){		
		$response = new Response($message);
		$response->setStatusCode(Response::HTTP_FORBIDDEN);
		return $response;
	}
	
}

?>
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
use AppBundle\Entity\Gradingmethod;
use AppBundle\Entity\Filetype;
use AppBundle\Entity\Feedback;
use AppBundle\Entity\TestcaseResult;

use Psr\Log\LoggerInterface;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;


class UploadController extends Controller {
 
    public function uploadAction($project_id=1, $problem_id=1) {

        // entity manager
        $em = $this->getDoctrine()->getManager();
        
        # query for the current problem
        $qb = $em->createQueryBuilder();
        $qb->select('p')
            ->from('AppBundle\Entity\Problem', 'p')
            ->where('p.id = ?1')
            ->setParameter(1, $problem_id);
            
        $query = $qb->getQuery();
        $problem_entity = $query->getOneorNullResult();
        
        if(!$problem_entity){
            die("PROBLEM DOES NOT EXIST");
        } else{
            echo($problem_entity->id."<br/>");    
        }
        
        
        # get the current user
        $user_entity= $this->get('security.token_storage')->getToken()->getUser();
        
        if(!$user_entity){
            die("USER DOES NOT EXIST");
        } else{
            echo($user_entity->getFirstName()." ".$user_entity->getLastName()."<br/>");
        }
        
        # get the current team
        $qb_teams = $em->createQueryBuilder();
        $qb_teams->select('t')
                ->from('AppBundle\Entity\Team', 't')
                ->where('t.assignment = ?1')
                ->setParameter(1, $problem_entity->assignment);
                
        $query_team = $qb_teams->getQuery();
        $team_entities = $query_team->getResult();    

        # loop over all the teams for this assignment and figure out which team the user is a part of
        $team_entity = null;        
        foreach($team_entities as $team){            
        
            foreach($team->users as $user){                    
            
                if($user_entity->id == $user->id){
                    $team_entity = $team;
                }
            }
        }
        
        if(!$team_entity){
            die("TEAM DOES NOT EXIST");
        } else{            
            echo($team_entity->name."<br/>");        
        }

        // web_dir is /var/www/gradel_dev/user/gradel/symfony_project
        $web_dir = $this->get('kernel')->getProjectDir();
        // save uploaded file to $web_dir.compilation/temp/submission_id/files
        
        // make a new submission if upload was successful
        $submission_entity = new Submission($problem_entity, $team_entity, $user_entity);	
            
        $em = $this->getDoctrine()->getManager(); 
        $em->persist($submission_entity);
        $em->flush();

        $uploadMessage = "";
        $target_dir = $web_dir."/compilation/temp/".$submission_entity->id."/"; // Specify an upload location
        mkdir($target_dir); // Make that directory
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);


        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Overwriting existing file\n";
            $uploadOk = 1; // Change to 0 if you don't want it to upload and overwrite. You can add a hash to the name or something.
        }
        // // Check file size
        // if ($_FILES["fileToUpload"]["size"] > 500000) {
        //     echo "Sorry, your file is too large.";
        //     $uploadOk = 0;
        // }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
			# TODO: Make this work
            #$em->remove($submission_entity);
            #$em->flush();
            #echo "Sorry, your file was not uploaded.";
        
        }
		// if everything is ok, try to upload file
		else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
                
               # die($target_file);
                # call timothy's controller
                // $url = $this->generateUrl('submit', array('submitted_filename' => basename($target_file), 'submission_id' => $submission_entity->id, 'filetype_id' => 1, 'language_id' => 1));
                // die($url);

                return $this->redirectToRoute('submit', array('submitted_filename' => basename($target_file), 'submission_id' => $submission_entity->id, 'filetype_id' => 3, 'language_id' => 4));
            } else {
                $em->remove($submission_entity);
                $em->flush();
                echo "Sorry, there was an error uploading your file.";
            }
        }
		
        // if they didn't send a file, render upload page
        return $this->render('courses/assignments/problems/upload/index.html.twig', [
            'project_id' => $project_id,
            'problem_id' => $problem_id,
        ]);
    }
}

?>
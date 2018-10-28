<?php

namespace AppBundle\Controller;

use AppBundle\Constants;

use AppBundle\Service\SectionService;
use AppBundle\Service\UserSectionRoleService;
use AppBundle\Service\UserService;

use Auth0\SDK\Auth0;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Psr\Log\LoggerInterface;

class SidenavController extends Controller {
    private $logger;
    private $sectionService;
    private $userSectionRoleService;
    private $userService;

    public function __construct(LoggerInterface $logger,
                                SectionService $sectionService,
                                UserSectionRoleService $userSectionRoleService,
                                UserService $userService) {
        $this->logger = $logger;
        $this->sectionService = $sectionService;
        $this->userSectionRoleService = $userSectionRoleService;
        $this->userService = $userService;
	}

    public function dataAction() {
		$entityManager = $this->getDoctrine()->getManager();

		$user = $this->userService->getCurrentUser();
		if (!get_class($user)) {
			return $this->returnForbiddenResponse("USER DOES NOT EXIST");
        }
        
        /* get all of the non-deleted sections
		   they must start in at least 30 days and have ended at most 14 days ago to show up*/
		$sectionsActive = $this->sectionService->getNonDeletedSectionsForHome($entityManager);
	  
		/* get the user section role entities using the user entity and active sections */
		$userSectionRoles = $this->userSectionRoleService->getUserSectionRolesForHome($entityManager, $user, $sectionsActive);
		
		$sectionsTaking = [];
		$sectionsTeaching = [];
		foreach ($userSectionRoles as $userSectionRole) {	
			if ($userSectionRole->role->role_name == Constants::TAKES_ROLE) {
				$assignmentsJSON = [];
				foreach($userSectionRole->section->assignments as $assignment) {
					$problemsJSON = [];
					foreach($assignment->problems as $problem) {
						$problemsJSON[] = array(
							"id" => $problem->id,
							"name" => $problem->name,
						);
					}
					$assignmentsJSON[] = array(
						"id" => $assignment->id,
						"name" => $assignment->name,
						"problems" => $problemsJSON,
					);
				}
				$sectionJSON = array(
					"id" => $userSectionRole->section->id,
					"courseCode" => $userSectionRole->section->course->code,
					"assignments" => $assignmentsJSON,
				);
				$sectionsTaking[] = $sectionJSON;
			} else if ($userSectionRole->role->role_name == Constants::TEACHES_ROLE || 
			           $userSectionRole->role->role_name == Constants::JUDGES_ROLE) {
				$assignmentsJSON = [];
				foreach($userSectionRole->section->assignments as $assignment) {
					$problemsJSON = [];
					foreach($assignment->problems as $problem) {
						$problemsJSON[] = array(
							"id" => $problem->id,
							"name" => $problem->name,
						);
					}
					$assignmentsJSON[] = array(
						"id" => $assignment->id,
						"name" => $assignment->name,
						"problems" => $problemsJSON,
					);
				}
				$sectionJSON = array(
					"id" => $userSectionRole->section->id,
					"courseCode" => $userSectionRole->section->course->code,
					"assignments" => $assignmentsJSON,
				);
				$sectionsTeaching[] = $sectionJSON;
			}
		}

		return new JsonResponse([
            'sections_taking' => $sectionsTaking,
            'sections_teaching' => $sectionsTeaching
        ]);
    }

    private function returnForbiddenResponse($message){		
		$response = new Response($message);
		$response->setStatusCode(Response::HTTP_FORBIDDEN);
		$this->logError($message);
		return $response;
	}
	
	private function logError($message) {
		$errorMessage = "SidenavController: ".$message;
		$this->logger->error($errorMessage);
		return $errorMessage;
	}
}

?>

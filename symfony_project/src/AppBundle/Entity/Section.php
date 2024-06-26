<?php

namespace AppBundle\Entity;

use JsonSerializable;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Config\Definition\Exception\Exception;
use \DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="section")
 */
class Section implements JsonSerializable {

	public function __construct(){

		$a = func_get_args();
		$i = func_num_args();

		if(method_exists($this, $f='__construct'.$i)) {
			call_user_func_array(array($this,$f),$a);
		} else if($i != 0){
			throw new Exception('ERROR: '.get_class($this).' constructor does not accept '.$i.' arguments');
		}

		$this->assignments = new ArrayCollection();
		$this->user_roles = new ArrayCollection();
	}

	public function __construct8($crs, $nm, $semester, $start, $end, $public, $deleted){
		$this->course = $crs;
		$this->name = $nm;
		$this->start_time = $start;
		$this->semester = $semester;
		$this->end_time = $end;
		$this->is_public = $public;
		$this->is_deleted = $deleted;
	}
	
	# clone method override
	public function __clone() {
		
		if($this->id){
			$this->id = null;
			
			# clone assignments
			$assignmentsClone = new ArrayCollection();
			
			foreach($this->assignments as $assignment){
				$assignmentClone = clone $assignment;
				$assignmentClone->section = $this;
				
				$assignmentsClone->add($assignmentClone);
			}
			$this->assignments = $assignmentsClone;
		
			$this->user_roles = new ArrayCollection();			
		}		
	}
	
	//change to use semester->is_currentsemester
	public function isActive(){
		
		$currTime = new \DateTime("now");
		return $this->start_time <= $currTime && $currTime < $this->end_time;
  }
  

  public function getAllUsers(){

    $users = [];

    foreach($this->user_roles as $usr){
      $users[] = $usr->user;
    }

    return $users;
  }

	public function getRegularUsers(){

		$users = [];
	
		foreach($this->user_roles as $usr){
		  if($usr->role->role_name == 'Takes'  && !$usr->user->hasRole("ROLE_SUPER")){
			  $users[] = $usr->user;
			}
		}
	
		return $users;
	}	
		  
	public function getElevatedUsers(){
			
		$users = [];

		foreach($this->user_roles as $usr){
			if($usr->role->role_name == 'Judges' || $usr->role->role_name == 'Teaches' || $usr->user->hasRole("ROLE_SUPER")){
				$users[] = $usr->user;
			}
		}

		return $users;  
	}

	public function getJudgeUsers(){

		$judges = [];

		foreach($this->user_roles as $usr){
			if($usr->role->role_name == 'Judges'){
				$judges[] = $usr->user;
			}
		}

		return $judges;
	}

	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	public $id;

	/**
	* @ORM\OneToMany(targetEntity="Assignment", mappedBy="section", cascade={"persist"})
	* @ORM\OrderBy({"start_time" = "ASC", "id" = "ASC"})
	*/
	public $assignments;
	
	/**
	 * @ORM\OneToMany(targetEntity="UserSectionRole", mappedBy="section", cascade={"persist", "remove"}, orphanRemoval=true)
	*/
	public $user_roles;

	/**
	* @ORM\ManyToOne(targetEntity="Course", inversedBy="sections")
	*/
	public $course;

	/**
	* @ORM\Column(type="string", length=255)
	*/
	public $name;

	/**
	* @ORM\ManyToOne(targetEntity="Semester", inversedBy="sections")
	*/
	public $semester;

	/**
	* @ORM\ManyToOne(targetEntity="Section", inversedBy="slaves")
	*/
	public $master;

	/**
	* @ORM\OneToMany(targetEntity="Section", mappedBy="master")
	* @ORM\OrderBy({"id" = "ASC"})
	*/
	public $slaves;

	/**
	* @ORM\Column(type="datetime")
	*/
	public $start_time;

	/**
	* @ORM\Column(type="datetime")
	*/
	public $end_time;

	/**
	* @ORM\Column(type="boolean")
	*/
	public $is_deleted;

	/**
	* @ORM\Column(type="boolean")
	*/
	public $is_master;

	/**
	* @ORM\Column(type="boolean")
	*/
	public $is_public;
	
	public function jsonSerialize(){
		return [
			'name' => $this->name,			
			'assignments' => $this->assignments->toArray(),
			'user_roles' => $this->user_roles->toArray(),
			'is_master' => $this->is_master
		];
	}

	public function getAllProblems(){

		$allProbs = [];

		foreach($this->assignments->toArray() as $asgn){
			foreach($asgn->problems->toArray() as $prob){
				$allProbs[] = $prob;
			}
		}

		return $allProbs;
	}
}

?>

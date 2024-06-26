<?php

namespace AppBundle\Entity;

use JsonSerializable;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
*@ORM\Entity
*@ORM\Table(name="query")
**/
class Query implements JsonSerializable{
	
	
	public function __construct(){
		
		$a = func_get_args();
		$i = func_num_args();	
		
		if(method_exists($this, $f='__construct'.$i)) {
			call_user_func_array(array($this,$f),$a);
		} else if($i != 0) {
			throw new Exception('ERROR: '.get_class($this).' constructor does not accept '.$i.' arguments');
		} else {
			$this->timestamp = new \DateTime("now");
		}
	}
	
	public function __construct4($reference, $quest, $ans, $time){
		
		if(get_class($reference) == "AppBundle\Entity\Assignment"){
			$this->assignment = $reference;
		} else if(get_class($reference) == "AppBundle\Entity\Problem"){
			$this->problem = $reference;
		} else {
			throw new Exception('ERROR: '.get_class($reference).' is not a valid class for reference');
		}		
		
		$this->question = $quest;
		$this->answer = $ans;
		$this->timestamp = $time;
	}

	# clone override method
	public function __clone(){
		
		if($this->id){
			$this->id = null;

			$this->problem = null;
			$this->assignment = null;

			$this->asker = null;
			$this->answerer = null;
		}

	}
	
	/** 
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	public $id;
	
	/**
     * @ORM\ManyToOne(targetEntity="Problem", inversedBy="queries")
     * @ORM\JoinColumn(name="problem_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
	public $problem;
	
	
	/**
     * @ORM\ManyToOne(targetEntity="Assignment", inversedBy="queries")
     * @ORM\JoinColumn(name="assignment_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
	public $assignment;
	
	/**
	* @ORM\Column(type="text", nullable=true)
	*/	
	public $question;
	
	
	/**
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumn(name="asker_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
	public $asker;
	
	/**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="answer_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
	public $answerer;
	
	
	/**
	* @ORM\Column(type="text", nullable=true)
	*/	
	public $answer;
	
	/**
	* @ORM\Column(type="datetime")
	*/
	public $timestamp;

	public function jsonSerialize(){
		return [
			'id' => $this->id,
			'problem' => $this->problem,
			'assignment' => $this->assignment,
			'question' => $this->question,
			'asker' => $this->asker,
			'answerer' => $this->answerer,
			'answer'=> $this->answer,
			'timestamp' => $this->timestamp,
		];
	}
}

?>
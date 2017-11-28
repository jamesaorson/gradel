<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
*@ORM\Entity
*@ORM\Table(name="testcaseresult")
**/
class TestcaseResult {

	public function __construct(){

		$a = func_get_args();
		$i = func_num_args();

		if(method_exists($this, $f='__construct'.$i)) {
			call_user_func_array(array($this,$f),$a);
		} else if($i != 0) {
			throw new Exception('ERROR: '.get_class($this).' constructor does not accept '.$i.' arguments');
		}
	}

	public function __construct8($sub, $test, $correct, $runout, $runerror, $time, $toolong, $out){
		$this->submission = $sub;
		$this->testcase = $test;
		$this->is_correct = $correct;
		$this->runtime_output = $runout;
		$this->runtime_error = $runerror;
		$this->execution_time = $time;
		$this->exceeded_time_limit = $toolong;
		$this->std_output = $out;
	}


	/**
	*@ORM\Column(type="integer")
	*@ORM\Id
	*@ORM\GeneratedValue(strategy="AUTO")
	*/
	public $id;

	/**
     * @ORM\ManyToOne(targetEntity="Submission", inversedBy="testcaseresults")
     * @ORM\JoinColumn(name="submission_id", referencedColumnName="id", onDelete="CASCADE")
     */
	public $submission;

	/**
     * @ORM\ManyToOne(targetEntity="Testcase")
     * @ORM\JoinColumn(name="testcase_id", referencedColumnName="id", onDelete="CASCADE")
     */
	public $testcase;

	/**
	 * @ORM\Column(type="blob", nullable=true)
	 */
	public $std_output;

	public function deblobinateStdOutput(){
		$val = stream_get_contents($this->std_output);
		rewind($this->std_output);
		
		return $val;
	}

	/**
	* @ORM\Column(type="blob", nullable=true)
	*/
	public $runtime_output;

	public function deblobinateRuntimeOutput(){
		$val = stream_get_contents($this->runtime_output);
		rewind($this->runtime_output);
		
		return $val;
	}

	/**
	* @ORM\Column(type="boolean")
	*/
	public $runtime_error;

	/**
	*@ORM\Column(type="boolean")
	*/
	public $is_correct;

	/**
	*@ORM\Column(type="integer")
	*/
	public $execution_time;

	/**
	* @ORM\Column(type="boolean")
	*/
	public $exceeded_time_limit;
}
?>

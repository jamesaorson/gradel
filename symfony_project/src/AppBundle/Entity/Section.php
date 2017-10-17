<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="section")
 */
class Section{

	public function __construct(){

		$a = func_get_args();
		$i = func_num_args();

		if(method_exists($this, $f='__construct'.$i)) {
			call_user_func_array(array($this,$f),$a);
		} else if($i != 0){
			throw new Exception('Contructor does not accept '.$i.' arguments');
		}

		$this->assignments = new ArrayCollection();
	}

	public function __construct9($crs, $nm, $sem, $yr, $start, $end, $own, $public, $deleted){
		$this->course = $crs;
		$this->name = $nm;
		$this->semester = $sem;
		$this->year = $yr;
		$this->start_time = $start;
		$this->end_time = $end;
		$this->owner = $own;
		$this->is_public = $public;
		$this->is_deleted = $deleted;
	}

	/**
	* @ORM\Column(type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	public $id;

	/**
	* @ORM\OneToMany(targetEntity="Assignment", mappedBy="section", cascade={"persist", "remove"})
	*/
	public $assignments;

	/**
	* @ORM\ManyToOne(targetEntity="Course", inversedBy="sections", cascade={"persist", "remove"})
	*/
	public $course;

	/**
	* @ORM\Column(type="string", length=255)
	*/
	public $name;

	/**
	* @ORM\Column(type="string", length=255)
	*/
	public $semester;

	/**
	* @ORM\Column(type="integer")
	*/
	public $year;

	/**
	* @ORM\Column(type="datetime")
	*/
	public $start_time;

	/**
	* @ORM\Column(type="datetime")
	*/
	public $end_time;

	/**
	* A user has a reference to an access level
	* @ORM\ManyToOne(targetEntity="User", cascade={"persist", "remove"})
	* @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="CASCADE")
	*/
	public $owner;

	/**
	* @ORM\Column(type="boolean")
	*/
	public $is_deleted;

	/**
	* @ORM\Column(type="boolean")
	*/
	public $is_public;
}

?>

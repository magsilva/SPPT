<?php

/**
 * Generic assignment class. It is used as a generic descriptor for an assingment
 * or as base class for specifc assingments.
 *
 * @author Marco Aurélio Graciotto Silva
 */
class Assignment
{
	protected $id;

	protected $name;

	protected $description;

	public function setId($id) {
		$this->id = $id;
	}

	public function getId() {
		return $this->id;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getName() {
		return $this->name;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	public function getDescription() {
		return $this->description;
	}

	public function getSupportedFeatures() {
		$features = array();
		return $features;
	}
}

?>

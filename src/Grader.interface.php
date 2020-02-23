<?php

/**
 * Base grader class.
 *
 * @author Marco Aurélio Graciotto Silva
 */
interface Grader
{
	public function getName();

	/**
	 * Array of features the grader supports.
	 */
	public function getFeatures();

	public function getOutputFormat();

	public function canEvaluate($submission);

	/**
	 * Returns Assessment.
	 */
	public function evaluate($submission);
}

?>

<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class UploadModel extends CFormModel 
{
	public $fridge;
	public $recipe;

	
	

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('fridge', 'file', 'allowEmpty' => false,'types' => 'csv', 'wrongType' => 'Only csv file is allowed.'),
			array('recipe', 'file', 'allowEmpty' => false,'types' => 'json', 'wrongType' => 'Only json file is allowed.'),
		);
	}






}

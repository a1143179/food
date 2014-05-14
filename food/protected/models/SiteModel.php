<?php
class SiteModel {

	protected $whatToCook = null;
	
	const FRIDGE_NAME_INDEX = 0;
	const FRIDGE_AMOUNT_INDEX = 1;
	const FRIDGE_UNIT_INDEX = 2;
	const FRIDGE_USE_BY_INDEX = 3;
	 
	/**
	 * 
	 * Process the form post
	 * @param array $post
	 */
	public function processForm(array $post, UploadModel $uploadModel){
		if(isset($post['UploadModel'])){
			$uploadModel->attributes = $post['UploadModel'];

			$uploadModel->fridge=CUploadedFile::getInstance($uploadModel,'fridge');
			$uploadModel->recipe=CUploadedFile::getInstance($uploadModel,'recipe');
			if($uploadModel->validate())
			{
				$fridgeArray = $this->parseFridge($uploadModel->fridge->tempName);
				$recipeArray = $this->parseRecipe(file_get_contents($uploadModel->recipe->tempName));
				$this->whatToCook = $this->findWhatToCook($fridgeArray, $recipeArray);
				return true;
			}
		}
		return false;
	}

	/**
	 * 
	 * Parse the fridge array from file
	 * @param unknown_type $fridgeCSVFilename
	 * @throws Exception
	 */
	public function parseFridge($fridgeCSVFilename) {
		$output = array();
		if (($handle = fopen($fridgeCSVFilename, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$output[] = $data; 
			}
			fclose($handle);
		} else {
			throw new Exception('File cannot be opened.');
		}
		return $output;
	}

	/**
	 * 
	 * Parse the recipe array from json string
	 * @param unknown_type $recipeJson
	 */
	public function parseRecipe($recipeJson) {
		$array = json_decode($recipeJson, true);
		return $array;
	}
	
	/**
	 * 
	 * Search the material. If found, the expire date will be appended and output
	 * @param unknown_type $fridgeArray
	 * @param unknown_type $targetMaterial
	 */
	protected function findMaterial($fridgeArray, $targetMaterial){
		foreach($fridgeArray as $fridge){
			list($expireDay, $expireMonth, $expireYear) = explode('/', $fridge[self::FRIDGE_USE_BY_INDEX]);
			$expireDate = new DateTime($expireYear.'-'.$expireMonth.'-'.$expireDay);
			// Note that by using DateTime, it will avoid the 2038 problem 
			if($fridge[self::FRIDGE_NAME_INDEX] == $targetMaterial['item']
				&& (int)$fridge[self::FRIDGE_AMOUNT_INDEX] >= (int)$targetMaterial['amount']
				&& $expireDate >= new DateTime()){
				$fridge['expireDate'] = $expireDate;  
				return $fridge;
			}
		}
		return null;
	}
	

	/**
	 * 
	 * Find the urgent available recipe.
	 * @param unknown_type $fridgeArray
	 * @param unknown_type $recipeArray
	 */
	public function findWhatToCook($fridgeArray, $recipeArray){
		$availableRecipes = array();
		$urgentMaterialDate = null;
		$urgentRecipe = null;
		if(!is_array($fridgeArray) || !is_array($recipeArray)){
			throw new Exception('Fridge and recipe should be arrays.');
		}
		foreach($recipeArray as $recipe){
			$recipeAvailable = true;
			foreach($recipe['ingredients'] as $ingredient){
				$material = $this->findMaterial($fridgeArray, $ingredient);
				if(empty($material)){
					$recipeAvailable = false;
				}elseif(!isset($recipe['expireDate']) || $recipe['expireDate'] > $material['expireDate']){
					$recipe['expireDate'] = $material['expireDate'];
				}
			}
			if($recipeAvailable && ($urgentMaterialDate === null || $urgentMaterialDate > $recipe['expireDate'])){
				$urgentMaterialDate = $recipe['expireDate'];
				$urgentRecipe = $recipe['name']; 
			}
		}
		return $urgentRecipe;
	}

	/**
	 * 
	 * Return the urgent available recipe.
	 */
	public function getWhatToCook(){
		return $this->whatToCook;
	}

}
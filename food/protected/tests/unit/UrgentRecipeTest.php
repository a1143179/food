<?php
class UrgentRecipeTest extends PHPUnit_Framework_TestCase{
	
	// row index
	const FRIDGE_CHEESE_INDEX = 1;
	const FRIDGE_RICE_INDEX = 6;
	
	// column index
	const FRIDGE_EXPIRE_INDEX = 3;
	const FRIDGE_STOCK_INDEX = 1;
	
	private $fridge = null;
	private $recipe = null;
	
	protected function setUp(){
		$this->fridge = array(
			array('bread','10','slices','25/12/2014'),
			array('cheese','10','slices','25/12/2014'),
			array('butter','250','grams','25/12/2014'),
			array('peanut butter','250','grams','2/12/2014'),
			array('mixed salad','500','grams','26/12/2013'),
			array('beef','10000','grams','2/10/2015'),
			array('rice','20000','grams','2/8/2015'),
		);
		
		$this->recipe = array(
			array(
				'name'=>'grilled cheese on toast',
				'ingredients'=>array(
					array('item'=>'bread','amount'=>'2','unit'=>'slices'),
					array('item'=>'cheese','amount'=>'2','unit'=>'slices'),
				)
			),
			array(
				'name'=>'salad sandwich',
				'ingredients'=>array(
					array('item'=>'bread','amount'=>'2','unit'=>'slices'),
					array('item'=>'mixed salad','amount'=>'200','unit'=>'grams'),
				)
			),
			array(
				'name'=>'beef and rice',
				'ingredients'=>array(
					array('item'=>'rice','amount'=>'200','unit'=>'grams'),
					array('item'=>'beef','amount'=>'200','unit'=>'grams'),
				)
			),
		);
		
	}

	public function testBasicRecipe(){
		$siteModel = new SiteModel();
		$urgentRecipe = $siteModel->findWhatToCook($this->fridge, $this->recipe);
		$this->assertEquals($urgentRecipe, 'grilled cheese on toast');
	}
	
	public function testUrgentRecipe(){
		$siteModel = new SiteModel();
		$this->fridge[self::FRIDGE_RICE_INDEX][self::FRIDGE_EXPIRE_INDEX]='2/8/2014';
		$urgentRecipe = $siteModel->findWhatToCook($this->fridge, $this->recipe);
		$this->assertEquals($urgentRecipe, 'beef and rice');
	}
	
	public function testMissingMaterialRecipe(){
		$siteModel = new SiteModel();
		unset($this->fridge[self::FRIDGE_CHEESE_INDEX]);
		unset($this->fridge[self::FRIDGE_RICE_INDEX]);
		$urgentRecipe = $siteModel->findWhatToCook($this->fridge, $this->recipe);
		$this->assertEquals($urgentRecipe, null);
	}
	
	public function testLowStock(){
		$siteModel = new SiteModel();
		$this->fridge[self::FRIDGE_CHEESE_INDEX][self::FRIDGE_STOCK_INDEX]='1';
		$urgentRecipe = $siteModel->findWhatToCook($this->fridge, $this->recipe);
		$this->assertEquals($urgentRecipe, 'beef and rice');		
	}
	
	public function testExpireMaterial(){
		$siteModel = new SiteModel();
		$this->fridge[self::FRIDGE_CHEESE_INDEX][self::FRIDGE_EXPIRE_INDEX]='1/1/2013';
		$urgentRecipe = $siteModel->findWhatToCook($this->fridge, $this->recipe);
		$this->assertEquals($urgentRecipe, 'beef and rice');		
	}
	
	/**
	* @expectedException        Exception
    * @expectedExceptionMessage Fridge and recipe should be arrays.
    * */
	public function testNullFridge(){
		$siteModel = new SiteModel();
		$urgentRecipe = $siteModel->findWhatToCook(null, $this->recipe);
	}
	
	/**
	* @expectedException        Exception
    * @expectedExceptionMessage Fridge and recipe should be arrays.
    * */
	public function testNullRecipe(){
		$siteModel = new SiteModel();
		$urgentRecipe = $siteModel->findWhatToCook($this->fridge, null);
	}
}
<?php

class SiteController extends Controller
{

	/**
	 * Declares class-based actions.
	 */

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		$siteModel = new SiteModel();
		$uploadModel = new UploadModel();
		if($siteModel->processForm($_POST, $uploadModel)){
			$this->render('success', array(
				'whatToCook' => $siteModel->getWhatToCook()
			));
		} else {
			$this->render('index',array(
				'uploadModel' => $uploadModel
			));
		}

	}


}
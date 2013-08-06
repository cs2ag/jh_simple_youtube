<?php
namespace TYPO3\JhSimpleYoutube\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Jonathan Heilmann <mail@jonathan-heilmann.de>, Webprogrammierung Jonathan Heilmann
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

//use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 *
 *
 * @package jh_simple_youtube
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class VideoController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * videoRepository
	 *
	 * @var \TYPO3\JhSimpleYoutube\Domain\Repository\VideoRepository
	 * @inject
	 */
	protected $videoRepository;

	/**
	 * action show
	 *
	 * @return void
	 */
	public function showAction() {
		$viewAssign = array();

		//add css file
		$this->response->addAdditionalHeaderData($this->wrapCssFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('jh_simple_youtube').'Resources/Public/css/tx_jhsimpleyoutube.css'));

		//get default settings from template-setup
		$viewAssign['width'] = $this->settings[width];
		$viewAssign['height'] = $this->settings[height];

		//get settings flexform (flexform overrides template-setup if available)
		$viewAssign['id'] = $this->settings[id];
		if(!empty($this->settings[flex_width])) {$viewAssign['width'] = $this->settings[flex_width];}
		if(!empty($this->settings[flex_height])) {$viewAssign['height'] = $this->settings[flex_height];}

		//calculate padding-bottom inline-style for video-container
		$viewAssign['paddingBottom'] = ($viewAssign['height'] / $viewAssign['width']) * 100;

		//get player parameters
		$playerParameters = '';
		$enableHtml5 = false;
		$viewAssign['allowfullscreen'] = '';
		//html5
		if(($this->settings[flex_html5] == -1 && $this->settings[html5] == 1) || $this->settings[flex_html5] == 1) {
			$playerParameters .= '&html5=1';
			$enableHtml5 = true;
		}
		//end (end time in seconds - only supported in flash-player)
		/*if(!$enableHtml5 && !empty($this->settings[end])) {
			if(strstr($this->settings[end], ':')) {
				$endArray = explode(':', $this->settings[end]);
				$endSeconds = ($endArray[0] * 60) + $endArray[1];
				$playerParameters .= '&end='.$endSeconds;
			} else {
				$playerParameters .= '&end='.$this->settings[end];
			}
		}*/
		//fs (fullscreen)
		if (!$enableHtml5 && !$this->settings[allowfullscreen]) {
			$playerParameters .= '&fs=0';
		} else if ($enableHtml5 && $this->settings[allowfullscreen]){
			$viewAssign['allowfullscreen'] = 'allowfullscreen';
		}
		//rel (related videos)
		if($this->settings[rel] == 0) {$playerParameters .= '&rel=0';}
		//start (start time in seconds)
		/*if(!empty($this->settings[start])) {
			if(strstr($this->settings[start], ':')) {
				$startArray = explode(':', $this->settings[start]);
				$startSeconds = ($startArray[0] * 60) + $startArray[1];
				$playerParameters .= '&start='.$startSeconds;
			} else {
				$playerParameters .= '&start='.$this->settings[start];
			}
		}*/
		//custom-parameters
		if(!empty($this->settings[flex_customParameters])) {
			if(substr($this->settings[flex_customParameters], 0, 1) == '&') {
				$playerParameters .= $this->settings[flex_customParameters];
			} else {
				$playerParameters .= '&'.$this->settings[flex_customParameters];
			}
		}

		if($playerParameters != '') {
			//remove first '&' and prepend '?'
			$viewAssign['playerParameters'] = '?' . substr($playerParameters, 1);
		}

		//assign array to fluid-template
		$this->view->assignMultiple($viewAssign);
	}

	private function wrapCssFile($cssFile) {
		$cssFile = \TYPO3\CMS\Core\Utility\GeneralUtility::resolveBackPath($cssFile);
		$cssFile = \TYPO3\CMS\Core\Utility\GeneralUtility::createVersionNumberedFilename($cssFile);
		return '<link rel="stylesheet" type="text/css" href="'.htmlspecialchars($cssFile).'" media="screen" />';
	}

}
?>
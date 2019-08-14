<?php
/**
* 
*/
class Rvtech_Starshipquote_Block_Dhlform extends Mage_Core_Block_Template
{
	
	public function getFormSubmitUrl() {

		return Mage::getUrl('starshipquote/dhlquote/quote');

	}

}


?>
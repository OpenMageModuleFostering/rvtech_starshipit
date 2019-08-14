<?php
/**
* 
*/
class Rvtech_Starshipquote_DhlquoteController extends Mage_Core_Controller_Front_Action
{

	public function quoteAction(){

		$params = $this->getRequest()->getParams();		
		$result = Mage::helper('starshipquote/dhl')->getQuoteFromDhl($params);
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		return;
	}
}

?>
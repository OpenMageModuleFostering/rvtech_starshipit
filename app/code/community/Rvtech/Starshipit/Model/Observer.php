<?php

class Rvtech_Starshipit_Model_Observer {
	

	protected $_noticeTitle = 'Starship Automatic Synchronization';

	protected $_noticeStatus; 

	public function syncOrdersNow() {

		$noticeMsg = '';		
        $helper = Mage::helper('starshipit/starship');
        
        $orderObj = Mage::getModel('starshipit/orders');

        //get the configuration array (username, apikey etc.)
        $para = $orderObj->getDataForExistingOredrs();


        //get existing orders from Starship
        $existingOrderRes      = $helper->getExistingOrders($para);

        if(empty($existingOrderRes->GetExistingResult->ErrorMessage)){
            $resOrders      =   $existingOrderRes
                                ->GetExistingResult
                                ->Orders;
            $odersArr = array();
            try{
                if(isset($resOrders->ExistingOrder)){
                    $resExistingOrder = $resOrders->ExistingOrder;
                    $odersArr = $helper->finalOrderArrOverStarShip($resExistingOrder);
                }                    
            }catch(Exception $e){
                
                $this->_noticeStatus = 3;
                $noticeMsg .= $e->getMessage();
            }

            if(!empty($odersArr)) {
	            //get Orders array to sync with StarShip
	            $ordersToPass = $orderObj->prepareOrderToPass($odersArr);

	            if(empty($ordersToPass['Orders'])) {	               
	            	$noticeMsg .= 'No order found for sync to Starship';
	            	$this->_noticeStatus = 0;
	            }else{            
	                
	            	$noticeMsg .= 'Orders sync to Starship';
	            	$this->_noticeStatus = 1;	                
	            }

	            //Sync orders and store resonpse
	            $resShipSync = $helper->addShipment($ordersToPass);      

	            //Change Order State and add Track Info 
	            if($helper->checkCondForMagentoWritebacks($resShipSync)) {
	                $resWriteBack = $helper->getMagentoWritebacks(); 
	                //$this->getResponse()->setBody(print_r($resWriteBack));
	                if(isset($resWriteBack->GetMagentoWritebacksResult->WritebackStruct)){                
	                    $isTackadded = $orderObj->addTrackingInfo($resWriteBack);
	                    if($isTackadded) {
	                
			            	$noticeMsg .= 'Tracking Info SAVED';
			            	$this->_noticeStatus += 1;           
	                    }
	                }else{
	                
						$noticeMsg .= 'No tracking info found on Starship';
			            $this->_noticeStatus += 0;
	                }            
	            }else{
	            	if($this->_noticeStatus){
	            		$this->_noticeStatus += 1;
	            	}	
	            }
            }            
        }else{

            $this->_noticeStatus = 3;
			$noticeMsg .= $existingOrderRes->GetExistingResult->ErrorMessage;

        }

        $this->_addNotice($noticeMsg);

	}

	protected function _addNotice($msg)
	{
		$notice = Mage::getModel('adminNotification/inbox');

		switch ($this->_noticeStatus) {
			case 0:
				$notice->add(2,$this->_noticeTitle,$msg);
				break;
			case 1:
				$notice->add(3,$this->_noticeTitle,$msg);
				break;
			case 3:
				$notice->add(1,$this->_noticeTitle,$msg);
				break;

			default:
				$notice->add(4,$this->_noticeTitle,$msg);
				break;
		}

	}
}
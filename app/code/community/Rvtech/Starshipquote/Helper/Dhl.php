<?php
class Rvtech_Starshipquote_Helper_Dhl extends Mage_Core_Helper_Abstract
{


    public $_wsdl = 'http://dhl.starshipit.com/OrdersService.svc?singleWsdl';    

    /**
     * 
     * @return php soap client object
     */

    protected function _soapClient() {

        $wsdl = $this->_wsdl;
        return new SoapClient($wsdl, array('trace' => 1));
    }

    protected function _getQuote($info = array()){
        $soap = $this->_soapClient();
        $result = $soap->__call('GetDHLQuote',array(
            'GetDHLQuote' => array(
                
                'accountId' => 403,
                'info' => $info
            )                               

        ));

        return $result;

    }

    public function getQuoteFromDhl($params = array()) {

        return $this->_getQuote($params);

    }


}
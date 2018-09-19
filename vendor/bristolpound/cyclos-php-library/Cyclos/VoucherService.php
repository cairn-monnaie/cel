<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherService.html
 * 
 * Generated with Cyclos 4.8.1
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class VoucherService extends Service {

    function __construct() {
        parent::__construct('voucherService');
    }
    
    /**
     * @param params Java type: org.cyclos.model.banking.vouchers.VoucherBarcodeParams
     * @return Java type: org.cyclos.server.utils.SerializableInputStream
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherService.html#barcode(org.cyclos.model.banking.vouchers.VoucherBarcodeParams)
     */
    public function barcode($params) {
        return $this->__run('barcode', array($params));
    }
    
    /**
     * @param params Java type: org.cyclos.model.banking.vouchers.BuyVouchersDTO
     * @return Java type: org.cyclos.model.banking.vouchers.VoucherPackWithIdsVO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherService.html#buy(org.cyclos.model.banking.vouchers.BuyVouchersDTO)
     */
    public function buy($params) {
        return $this->__run('buy', array($params));
    }
    
    /**
     * @param params Java type: org.cyclos.model.banking.vouchers.CancelVouchersDTO
     * @return Java type: int
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherService.html#cancel(org.cyclos.model.banking.vouchers.CancelVouchersDTO)
     */
    public function cancel($params) {
        return $this->__run('cancel', array($params));
    }
    
    /**
     * @param params Java type: org.cyclos.model.banking.vouchers.ChangeVoucherExpirationDateDTO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherService.html#changeExpirationDate(org.cyclos.model.banking.vouchers.ChangeVoucherExpirationDateDTO)
     */
    public function changeExpirationDate($params) {
        $this->__run('changeExpirationDate', array($params));
    }
    
    /**
     * @param params Java type: org.cyclos.model.banking.vouchers.VoucherQuery     * @param markAsPrinted Java type: boolean
     * @return Java type: org.cyclos.server.utils.SerializableInputStream
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherService.html#exportToCSV(org.cyclos.model.banking.vouchers.VoucherQuery,%20boolean)
     */
    public function exportToCSV($params, $markAsPrinted) {
        return $this->__run('exportToCSV', array($params, $markAsPrinted));
    }
    
    /**
     * @param params Java type: org.cyclos.model.banking.vouchers.GenerateVouchersDTO
     * @return Java type: org.cyclos.model.banking.vouchers.VoucherPackWithIdsVO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherService.html#generate(org.cyclos.model.banking.vouchers.GenerateVouchersDTO)
     */
    public function generate($params) {
        return $this->__run('generate', array($params));
    }
    
    /**
     * @param params Java type: org.cyclos.model.banking.vouchers.BuyVoucherDataParams
     * @return Java type: org.cyclos.model.banking.vouchers.BuyVoucherData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherService.html#getBuyData(org.cyclos.model.banking.vouchers.BuyVoucherDataParams)
     */
    public function getBuyData($params) {
        return $this->__run('getBuyData', array($params));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.model.banking.vouchers.VoucherData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherService.html#getData(java.lang.Long)
     */
    public function getData($id) {
        return $this->__run('getData', array($id));
    }
    
    /**
     * @param voucherType Java type: org.cyclos.model.banking.vouchertypes.VoucherTypeVO
     * @return Java type: org.cyclos.model.banking.vouchers.GenerateVouchersData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherService.html#getGenerateData(org.cyclos.model.banking.vouchertypes.VoucherTypeVO)
     */
    public function getGenerateData($voucherType) {
        return $this->__run('getGenerateData', array($voucherType));
    }
    
    /**
     * @param params Java type: org.cyclos.model.banking.vouchers.RedeemVoucherDataParams
     * @return Java type: org.cyclos.model.banking.vouchers.RedeemVoucherData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherService.html#getRedeemData(org.cyclos.model.banking.vouchers.RedeemVoucherDataParams)
     */
    public function getRedeemData($params) {
        return $this->__run('getRedeemData', array($params));
    }
    
    /**
     * @param user Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: org.cyclos.model.banking.vouchers.InitialRedeemVoucherData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherService.html#getRedeemInitialData(org.cyclos.model.users.users.UserLocatorVO)
     */
    public function getRedeemInitialData($user) {
        return $this->__run('getRedeemInitialData', array($user));
    }
    
    /**
     * @param context Java type: org.cyclos.model.banking.vouchers.VoucherSearchContext     * @param user Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: org.cyclos.model.banking.vouchers.VouchersSearchData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherService.html#getSearchData(org.cyclos.model.banking.vouchers.VoucherSearchContext,%20org.cyclos.model.users.users.UserLocatorVO)
     */
    public function getSearchData($context, $user) {
        return $this->__run('getSearchData', array($context, $user));
    }
    
    /**
     * @param forUser Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherService.html#listTypesForBuy(org.cyclos.model.users.users.UserLocatorVO)
     */
    public function listTypesForBuy($forUser) {
        return $this->__run('listTypesForBuy', array($forUser));
    }
    
    /**

     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherService.html#listTypesForGenerate()
     */
    public function listTypesForGenerate() {
        return $this->__run('listTypesForGenerate', array());
    }
    
    /**
     * @param token Java type: java.lang.String
     * @return Java type: org.cyclos.model.banking.vouchers.VoucherVO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherService.html#loadByToken(java.lang.String)
     */
    public function loadByToken($token) {
        return $this->__run('loadByToken', array($token));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.server.utils.SerializableInputStream
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherService.html#print(java.lang.Long)
     */
    public function _print($id) {
        return $this->__run('print', array($id));
    }
    
    /**
     * @param query Java type: org.cyclos.model.banking.vouchers.VoucherQuery     * @param markAsPrinted Java type: boolean
     * @return Java type: org.cyclos.server.utils.SerializableInputStream
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherService.html#printAll(org.cyclos.model.banking.vouchers.VoucherQuery,%20boolean)
     */
    public function printAll($query, $markAsPrinted) {
        return $this->__run('printAll', array($query, $markAsPrinted));
    }
    
    /**
     * @param params Java type: org.cyclos.model.banking.vouchers.RedeemVoucherDTO
     * @return Java type: org.cyclos.model.banking.vouchers.RedeemVoucherResult
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherService.html#redeem(org.cyclos.model.banking.vouchers.RedeemVoucherDTO)
     */
    public function redeem($params) {
        return $this->__run('redeem', array($params));
    }
    
    /**
     * @param params Java type: org.cyclos.model.banking.vouchers.VoucherQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherService.html#search(org.cyclos.model.banking.vouchers.VoucherQuery)
     */
    public function search($params) {
        return $this->__run('search', array($params));
    }
    
    /**
     * @param params Java type: org.cyclos.model.banking.vouchers.VoucherQuery
     * @return Java type: org.cyclos.model.banking.vouchers.VouchersResultsWithSummaryVO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherService.html#searchWithSummary(org.cyclos.model.banking.vouchers.VoucherQuery)
     */
    public function searchWithSummary($params) {
        return $this->__run('searchWithSummary', array($params));
    }
    
}

?>
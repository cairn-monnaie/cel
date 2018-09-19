<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/TransactionFeedbackService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class TransactionFeedbackService extends Service {

    function __construct() {
        parent::__construct('transactionFeedbackService');
    }
    
    /**
     * @param transactionId Java type: java.lang.Long     * @param level Java type: org.cyclos.model.users.references.ReferenceLevel     * @param comment Java type: java.lang.String
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/TransactionFeedbackService.html#comment(java.lang.Long,%20org.cyclos.model.users.references.ReferenceLevel,%20java.lang.String)
     */
    public function comment($transactionId, $level, $comment) {
        return $this->__run('comment', array($transactionId, $level, $comment));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/TransactionFeedbackService.html#getData(java.lang.Long)
     */
    public function getData($id) {
        return $this->__run('getData', array($id));
    }
    
    /**
     * @param params Java type: DP
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/TransactionFeedbackService.html#getDataForNew(DP)
     */
    public function getDataForNew($params) {
        return $this->__run('getDataForNew', array($params));
    }
    
    /**
     * @param locator Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: org.cyclos.model.users.references.TransactionFeedbackSearchData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/TransactionFeedbackService.html#getSearchData(org.cyclos.model.users.users.UserLocatorVO)
     */
    public function getSearchData($locator) {
        return $this->__run('getSearchData', array($locator));
    }
    
    /**
     * @param query Java type: org.cyclos.model.users.references.TransactionFeedbackQuery
     * @return Java type: org.cyclos.model.users.references.ReferenceStatisticsVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/TransactionFeedbackService.html#getStatistics(org.cyclos.model.users.references.TransactionFeedbackQuery)
     */
    public function getStatistics($query) {
        return $this->__run('getStatistics', array($query));
    }
    
    /**
     * @param transactionId Java type: java.lang.Long
     * @return Java type: org.cyclos.model.users.references.TransactionFeedbackVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/TransactionFeedbackService.html#getTransactionFeedback(java.lang.Long)
     */
    public function getTransactionFeedback($transactionId) {
        return $this->__run('getTransactionFeedback', array($transactionId));
    }
    
    /**

     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/TransactionFeedbackService.html#listIgnoredUsers()
     */
    public function listIgnoredUsers() {
        return $this->__run('listIgnoredUsers', array());
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: DTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/TransactionFeedbackService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
    /**
     * @param locator Java type: org.cyclos.model.users.users.UserLocatorVO     * @param ignored Java type: boolean
     * @return Java type: boolean
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/TransactionFeedbackService.html#markAsIgnored(org.cyclos.model.users.users.UserLocatorVO,%20boolean)
     */
    public function markAsIgnored($locator, $ignored) {
        return $this->__run('markAsIgnored', array($locator, $ignored));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/TransactionFeedbackService.html#remove(java.lang.Long)
     */
    public function remove($id) {
        $this->__run('remove', array($id));
    }
    
    /**
     * @param ids Java type: java.util.Collection
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/TransactionFeedbackService.html#removeAll(java.util.Collection)
     */
    public function removeAll($ids) {
        $this->__run('removeAll', array($ids));
    }
    
    /**
     * @param transactionId Java type: java.lang.Long     * @param replyComments Java type: java.lang.String
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/TransactionFeedbackService.html#reply(java.lang.Long,%20java.lang.String)
     */
    public function reply($transactionId, $replyComments) {
        return $this->__run('reply', array($transactionId, $replyComments));
    }
    
    /**
     * @param object Java type: DTO
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/TransactionFeedbackService.html#save(DTO)
     */
    public function save($object) {
        return $this->__run('save', array($object));
    }
    
    /**
     * @param query Java type: org.cyclos.model.users.references.TransactionFeedbackQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/TransactionFeedbackService.html#search(org.cyclos.model.users.references.TransactionFeedbackQuery)
     */
    public function search($query) {
        return $this->__run('search', array($query));
    }
    
    /**
     * @param transactionAwaitingFeedbackQuery Java type: org.cyclos.model.users.references.TransactionsAwaitingFeedbackQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/TransactionFeedbackService.html#searchTransactionsAwaitingFeedback(org.cyclos.model.users.references.TransactionsAwaitingFeedbackQuery)
     */
    public function searchTransactionsAwaitingFeedback($transactionAwaitingFeedbackQuery) {
        return $this->__run('searchTransactionsAwaitingFeedback', array($transactionAwaitingFeedbackQuery));
    }
    
}

?>
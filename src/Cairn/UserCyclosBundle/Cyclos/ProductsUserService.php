<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/ProductsUserService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class ProductsUserService extends Service {

    function __construct() {
        parent::__construct('productsUserService');
    }
    
    /**
     * @param product Java type: org.cyclos.model.users.products.ProductVO     * @param owner Java type: VO
     * @return Java type: boolean
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/ProductsUserService.html#assign(org.cyclos.model.users.products.ProductVO,%20VO)
     */
    public function assign($product, $owner) {
        return $this->__run('assign', array($product, $owner));
    }
    
    /**
     * @param owner Java type: VO     * @param channel Java type: org.cyclos.model.access.channels.ChannelVO     * @param principalType Java type: org.cyclos.model.access.principaltypes.PrincipalTypeVO
     * @return Java type: org.cyclos.model.users.products.ActiveProductsData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/ProductsUserService.html#getActiveProducts(VO,%20org.cyclos.model.access.channels.ChannelVO,%20org.cyclos.model.access.principaltypes.PrincipalTypeVO)
     */
    public function getActiveProducts($owner, $channel, $principalType) {
        return $this->__run('getActiveProducts', array($owner, $channel, $principalType));
    }
    
    /**
     * @param locator Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/ProductsUserService.html#getUserProductLogs(org.cyclos.model.users.users.UserLocatorVO)
     */
    public function getUserProductLogs($locator) {
        return $this->__run('getUserProductLogs', array($locator));
    }
    
    /**
     * @param product Java type: org.cyclos.model.users.products.ProductVO     * @param ownerId Java type: VO
     * @return Java type: boolean
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/ProductsUserService.html#unassign(org.cyclos.model.users.products.ProductVO,%20VO)
     */
    public function unassign($product, $ownerId) {
        return $this->__run('unassign', array($product, $ownerId));
    }
    
}

?>
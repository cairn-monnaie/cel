<?php
// src/Cairn/UserCyclosBundle/Service/ProductInfo.php

namespace Cairn\UserCyclosBundle\Service;

//manage Cyclos configuration file                                             
use Cyclos;

/**
 *This class contains getters related to products in Cyclos
 *                                                                             
 */
class ProductInfo
{

    /**
     * Deals with all products management.
     *
     * This attribute is an instance of a Cyclos Service class proposed in the Cyclos WebServices PHP API.
     *@var Cyclos\ProductService $productService                                            
     */
    private $productService;

     /**                                                                        
     * Deals with management of all products assigned to groups
     *
     * This attribute is an instance of a Cyclos Service class proposed in the Cyclos WebServices PHP API.
     *@var Cyclos\ProductsGroupService $productsGroupService                                            
     */
    private $productsGroupService;

    /**                                                                        
     * Deals with all individually assigned products management
     *
     * This attribute is an instance of a Cyclos Service class proposed in the Cyclos WebServices PHP API.
     *@var Cyclos\ProductsUserService $productsUserService                                            
     */
    private $productsUserService;

    public function __construct()
    {
        $this->productService = new Cyclos\ProductService();
        $this->productsGroupService = new Cyclos\ProductsGroupService();
        $this->productsUserService = new Cyclos\ProductsUserService();
    }


    public function getProductDTOByID($id)
    {
        return $this->productService->load($id);
    }

    //DO NOT USE THIS FUNCTION : cannot get productVO from corresponding productDTO : id's don't match
    //    public function getProductVO($productDTO)
    //    {
    //        $listProducts = $this->getListProductsVO($productDTO->nature);
    //
    //        foreach($listProducts as $productVO){
    //            var_dump($productVO->id);
    //            if($productVO->id == $productDTO->id){
    //                return $productVO;
    //            }
    //        }      
    //        return NULL;
    //    }


    public function getProductVOByGroup($groupVO)
    {
        $query = new \stdClass();
        //        $query->groups = array($groupVO);
        $query->natures = 'ADMIN';
        return $this->productService->search($query)->pageItems;
        //        return $this->productsGroupService->getActiveProducts($groupVO,NULL,NULL);

    }    

    /*
     *returns the product with name $name. It must be unique. Otherwise : config issue
     *@param string $name
     *@return ProductVO
     *@throws Exception Searching a product by its name does not provide an unique object 
     */ 
    public function getProductVOByName($name)
    {
        $query = new \stdClass();
        $query->name = $name;
        $list = $this->productService->search($query)->pageItems;
        $nbProducts = count($list);
        if($nbProducts != 1){
            $analysis = 'Analyse : Cela signifie que la condition "Chaque type de compte utilisateur est associé à un unique produit du même nom" ou "Chaque produit Système a le même nom que son groupe d\'administrateurs associés" n\'est pas respectée.';
            $tip =  'Aide : Il s\'agit d\'un problème de configuration. Vérifiez les noms des AccountType, des Product associés et des groupes administrateurs.';

            if($nbProducts == 0){
                $problem = 'Problème : Product de nom ' .$name. ' introuvable. \n';
            }
            else{
                $problem = 'Problème : Plusieurs produits de nom ' .$name. ' trouvés. \n' ;
            }
            throw new \Exception($problem . $analysis . $tip);
        }
        else{
            return $list[0];
        }
    }

    /*
     * Returns a ProductVO object from an Account Type object
     *
     * As the ProductDTO ID and its corresponding ProductVO ID are not the same, we need to use the accountType associated to the product
     *
     * @param object $accountTypeDTO
     * @return ProductVO
     */
    public function getAccountProductVO($accountTypeDTO)
    {
        $listProducts = $this->getListProductsVO($accountTypeDTO->nature);

        foreach($listProducts as $productVO){
            $productDTO = $this->productService->load($productVO->id);
            if($productDTO->userAccount->id == $accountTypeDTO->id){
                return $productVO;
            }
        }
        return NULL;
    }

    /*
     * Returns a ProductDTO object from an Account Type object
     *
     * As the ProductDTO ID and its corresponding ProductVO ID are not the same, we need to use the accountType associated to the product
     *
     * @param object $accountTypeDTO
     * @return ProductDTO
     */
    public function getAccountProductDTO($accountTypeDTO)
    {
        $nature = $accountTypeDTO->nature;
        $listProducts = $this->getListProductsVO($nature);

        foreach($listProducts as $product){
            $productDTO = $this->productService->load($product->id);
            if($productDTO->userAccount->id == $accountTypeDTO->id){
                return $productDTO;
            }
        }
        return NULL;
    }

    /**
     * Gets a list of ProductDTO objects by nature
     *
     *@param string $nature Java type : org.cyclos.model.banking.accounttypes.AccountTypeNature 
     */
    public function getListProductsDTO($nature)
    {
        $res = array();
        $listVO = $this->getListProductsVO($nature);
        foreach($listVO as $VO){
            $DTO = $this->productService->load($VO->id);
            $res[] = $DTO;
        }

        return $res;
    }

     /**
     * Gets a list of ProductVO objects by nature
     *
     *@param string $nature Java type : org.cyclos.model.banking.accounttypes.AccountTypeNature 
     */
   public function getListProductsVO($accountTypeNature)
    {
        $query = new \stdClass();
        if($accountTypeNature == 'USER'){
            $query->natures = array('MEMBER','BROKER');
        }else{
            $query->natures = 'ADMIN';
        }

        $res = $this->productService->search($query);
        return $res->pageItems;
    }


}

<?php
namespace Cairn\UserCyclosBundle\Entity;

//manage Cyclos configuration file                                             
use Cyclos;

class ProductManager
{
    private $productService;
    private $productsUserService;
    private $productsGroupService;

    public function __construct()
    {
        $this->productService = new Cyclos\ProductService();
        $this->productsUserService = new Cyclos\ProductsUserService();
        $this->productsGroupService = new Cyclos\ProductsGroupService();

    }

    public function unassignToGroup($productVO,$groupID)
    {
        return $this->productsGroupService->unassign($productVO,$groupID);
    } 

    public function assignToGroup($productVO,$groupID)
    {
        return $this->productsGroupService->assign($productVO,$groupID);
    } 

    public function editProduct($productDTO){
        return $this->productService->save($productDTO);
    }

    public function removeProduct($id){
        return $this->productService->remove($id);}

}

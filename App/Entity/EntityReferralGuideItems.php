<?php

namespace App\Entity;

final class EntityReferralGuideItems {
    
    private $id;
    private $id_referral_guide;
    private $ProductID;
    private $ProductName;
    private $QuantityProduct;
    private $unitCode;
    private $user_create;
    private $date_create;
    private $user_upgrade;
    private $date_upgrade;
    private $estado;
	private $ProductCode;
	
	function getProductCode(): string {
        return $this->ProductCode;
    }
    
    function getEstado(): int {
        return $this->estado;
    }

        
    function getId(): int {
        return $this->id;
    }

    function getId_referral_guide(): int {
        return $this->id_referral_guide;
    }

    function getProductID(): string {
        return $this->ProductID;
    }

    function getProductName(): string {
        return $this->ProductName;
    }

    function getQuantityProduct(): int {
        return $this->QuantityProduct;
    }

    function getUnitCode(): string {
        return $this->unitCode;
    }

    function getUser_create(): int {
        return $this->user_create;
    }

    function getDate_create(): string {
        return $this->date_create;
    }

    function getUser_upgrade(): int {
        return $this->user_upgrade;
    }

    function getDate_upgrade(): string {
        return $this->date_upgrade;
    }

    //***************************************
    
    function setId(int $id) {
        $this->id = $id;
    }

    function setId_referral_guide(int $id_referral_guide) {
        $this->id_referral_guide = $id_referral_guide;
    }

    function setProductID(string $ProductID) {
        $this->ProductID = $ProductID;
    }

    function setProductName(string $ProductName) {
        $this->ProductName = $ProductName;
    }

    function setQuantityProduct(int $QuantityProduct) {
        $this->QuantityProduct = $QuantityProduct;
    }

    function setUnitCode(string $unitCode) {
        $this->unitCode = $unitCode;
    }

    function setUser_create(int $user_create) {
        $this->user_create = $user_create;
    }

    function setDate_create(string $date_create) {
        $this->date_create = $date_create;
    }

    function setUser_upgrade(int $user_upgrade) {
        $this->user_upgrade = $user_upgrade;
    }

    function setDate_upgrade(string $date_upgrade) {
        $this->date_upgrade = $date_upgrade;
    }

    function setEstado(int $estado) {
        $this->estado = $estado;
    }
	
	function setProductCode(string $ProductCode) {
        $this->ProductCode = $ProductCode;
    }


}

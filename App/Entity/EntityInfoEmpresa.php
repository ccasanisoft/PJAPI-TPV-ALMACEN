<?php

namespace App\Entity;

final class EntityInfoEmpresa{
    
    private $id;
    private $ruc;
    private $razonSocial;
    private $usuSol;
    private $passFirma;
    private $passSol;
    private $dbhost;
    private $dbmane;
    private $dbusername;
    private $dbpassword;
    
    function getId(): int {
        return $this->id;
    }

    function getRuc(): string {
        return $this->ruc;
    }

    function getRazonSocial(): string {
        return $this->razonSocial;
    }

    function getUsuSol(): string {
        return $this->usuSol;
    }

    function getPassFirma(): string {
        return $this->passFirma;
    }

    function getPassSol(): string {
        return $this->passSol;
    }

    function getDbhost(): string {
        return $this->dbhost;
    }

    function getDbmane(): string {
        return $this->dbmane;
    }

    function getDbusername(): string {
        return $this->dbusername;
    }

    function getDbpassword(): string {
        return $this->dbpassword;
    }

//******************************************
    
    function setId(int $id) {
        $this->id = $id;
    }

    function setRuc(string $ruc) {
        $this->ruc = $ruc;
    }

    function setRazonSocial(string $razonSocial) {
        $this->razonSocial = $razonSocial;
    }

    function setUsuSol(string $usuSol) {
        $this->usuSol = $usuSol;
    }

    function setPassFirma(string $passFirma) {
        $this->passFirma = $passFirma;
    }

    function setPassSol(string $passSol) {
        $this->passSol = $passSol;
    }

    function setDbhost(string $dbhost) {
        $this->dbhost = $dbhost;
    }

    function setDbmane(string $dbmane) {
        $this->dbmane = $dbmane;
    }

    function setDbusername(string $dbusername) {
        $this->dbusername = $dbusername;
    }

    function setDbpassword(string $dbpassword) {
        $this->dbpassword = $dbpassword;
    }


}


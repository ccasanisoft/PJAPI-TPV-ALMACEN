<?php

namespace App\Entity;

final class EntityReferralGuide {

    
    private $id;
    private $serieNumero;
    private $IssueDate;
    private $Note;
    private $DescriptionReasonTransfer;
    private $TotalGrossWeightGRE;
    private $NumberPackages;
    private $unitCodeGrossWeightGRE;
    private $TypeDocumenttransmitter;
    private $transmitterName;
    private $addresseeID;
    private $TypeDocumentaddressee;
    private $addresseeName;
    private $motivemovedCode;
    private $transfermobility;
    private $LicensePlateID;
    private $DriverPersonID;
    private $DriverPersonDocumentType;
    private $movedstartdate;
    private $DeliveryUbi;
    private $Delivery;
    private $OriginAddressUbi;
    private $OriginAddress;
    private $user_create;
    private $date_create;
    private $user_upgrade;
    private $date_upgrade;
    private $flg_response;
    private $error_code;
    private $response_descrip;
    private $digest_value;
	private $nameTransportista;
    private $estado;
    
	function getNameTransportista(): string {
        return $this->nameTransportista;
    }
	
    function getEstado(): int {
        return $this->estado;
    }

        
    function getFlg_response(): int {
        return $this->flg_responseP;
    }

    function getError_code(): string {
        return $this->error_codeP;
    }

    function getResponse_descrip(): string {
        return $this->response_descripP;
    }

    function getDigest_value(): string {
        return $this->digest_valueP;
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

        
    function getId(): int {
        return $this->id;
    }

    function getSerieNumero(): string {
        return $this->serieNumero;
    }

    function getIssueDate(): string {
        return $this->IssueDate;
    }

    function getNote(): string {
        return $this->Note;
    }

    function getDescriptionReasonTransfer(): string {
        return $this->DescriptionReasonTransfer;
    }

    function getTotalGrossWeightGRE(): string {
        return $this->TotalGrossWeightGRE;
    }

    function getNumberPackages(): int {
        return $this->NumberPackages;
    }

    function getUnitCodeGrossWeightGRE(): string {
        return $this->unitCodeGrossWeightGRE;
    }

    function getTypeDocumenttransmitter(): int {
        return $this->TypeDocumenttransmitter;
    }

    function getTransmitterName(): string {
        return $this->transmitterName;
    }

    function getAddresseeID(): string {
        return $this->addresseeID;
    }

    function getTypeDocumentaddressee(): int {
        return $this->TypeDocumentaddressee;
    }

    function getAddresseeName(): string {
        return $this->addresseeName;
    }

    function getMotivemovedCode(): string {
        return $this->motivemovedCode;
    }

    function getTransfermobility(): int {
        return $this->transfermobility;
    }

    function getLicensePlateID(): string {
        return $this->LicensePlateID;
    }

    function getDriverPersonID(): string {
        return $this->DriverPersonID;
    }

    function getDriverPersonDocumentType(): int {
        return $this->DriverPersonDocumentType;
    }

    function getMovedstartdate(): string {
        return $this->movedstartdate;
    }

    function getDeliveryUbi(): string {
        return $this->DeliveryUbi;
    }

    function getDelivery(): string {
        return $this->Delivery;
    }

    function getOriginAddressUbi(): string {
        return $this->OriginAddressUbi;
    }

    function getOriginAddress(): string {
        return $this->OriginAddress;
    }
//**********************************************************
    function setId(int $id) {
        $this->id = $id;
    }

    function setSerieNumero(string $serieNumero) {
        $this->serieNumero = $serieNumero;
    }

    function setIssueDate(string $IssueDate) {
        $this->IssueDate = $IssueDate;
    }

    function setNote(string $Note) {
        $this->Note = $Note;
    }

    function setDescriptionReasonTransfer(string $DescriptionReasonTransfer) {
        $this->DescriptionReasonTransfer = $DescriptionReasonTransfer;
    }

    function setTotalGrossWeightGRE(string $TotalGrossWeightGRE) {
        $this->TotalGrossWeightGRE = $TotalGrossWeightGRE;
    }

    function setNumberPackages(int $NumberPackages) {
        $this->NumberPackages = $NumberPackages;
    }

    function setUnitCodeGrossWeightGRE(string $unitCodeGrossWeightGRE) {
        $this->unitCodeGrossWeightGRE = $unitCodeGrossWeightGRE;
    }

    function setTypeDocumenttransmitter(int $TypeDocumenttransmitter) {
        $this->TypeDocumenttransmitter = $TypeDocumenttransmitter;
    }

    function setTransmitterName(string $transmitterName) {
        $this->transmitterName = $transmitterName;
    }

    function setAddresseeID(string $addresseeID) {
        $this->addresseeID = $addresseeID;
    }

    function setTypeDocumentaddressee(int $TypeDocumentaddressee) {
        $this->TypeDocumentaddressee = $TypeDocumentaddressee;
    }

    function setAddresseeName(string $addresseeName) {
        $this->addresseeName = $addresseeName;
    }

    function setMotivemovedCode(string $motivemovedCode) {
        $this->motivemovedCode = $motivemovedCode;
    }

    function setTransfermobility(int $transfermobility) {
        $this->transfermobility = $transfermobility;
    }

    function setLicensePlateID(string $LicensePlateID) {
        $this->LicensePlateID = $LicensePlateID;
    }

    function setDriverPersonID(string $DriverPersonID) {
        $this->DriverPersonID = $DriverPersonID;
    }

    function setDriverPersonDocumentType(int $DriverPersonDocumentType) {
        $this->DriverPersonDocumentType = $DriverPersonDocumentType;
    }

    function setMovedstartdate(string $movedstartdate) {
        $this->movedstartdate = $movedstartdate;
    }

    function setDeliveryUbi(string $DeliveryUbi) {
        $this->DeliveryUbi = $DeliveryUbi;
    }

    function setDelivery(string $Delivery) {
        $this->Delivery = $Delivery;
    }

    function setOriginAddressUbi(string $OriginAddressUbi) {
        $this->OriginAddressUbi = $OriginAddressUbi;
    }

    function setOriginAddress(string $OriginAddress) {
        $this->OriginAddress = $OriginAddress;
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
    
    function setFlg_response(int $flg_responseP) {
        $this->flg_responseP = $flg_responseP;
    }

    function setError_code(string $error_codeP) {
        $this->error_codeP = $error_codeP;
    }

    function setResponse_descrip(string $response_descripP) {
        $this->response_descripP = $response_descripP;
    }

    function setDigest_value(string $digest_valueP) {
        $this->digest_valueP = $digest_valueP;
    }

    function setEstado(int $estado) {
        $this->estado = $estado;
    }
	
	function setNameTransportista(string $nameTransportista) {
        $this->nameTransportista = $nameTransportista;
    }

        
    //************************************************************************
}

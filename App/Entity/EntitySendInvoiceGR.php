<?php

namespace App\Entity;

final class EntitySendInvoiceGR{
    
    private $id;
    private $referral_guide_id;
    private $issue_date;
    private $file_name;
    private $flg_response;
    private $error_code;
    private $response_descrip;
    private $observations;
    private $status;
    private $user_Create;
    private $date_Create;
    private $user_upgrade;
    private $date_upgrade;
    private $stado;
    
    function getId(): int {
        return $this->id;
    }

    function getReferral_guide_id(): int {
        return $this->referral_guide_id;
    }

    function getIssue_date(): string {
        return $this->issue_date;
    }

    function getFile_name(): string {
        return $this->file_name;
    }

    function getFlg_response(): int {
        return $this->flg_response;
    }

    function getError_code(): string {
        return $this->error_code;
    }

    function getResponse_descrip(): string {
        return $this->response_descrip;
    }

    function getObservations(): string {
        return $this->observations;
    }

    function getStatus(): int {
        return $this->status;
    }

    function getUser_Create(): int {
        return $this->user_Create;
    }

    function getDate_Create(): string {
        return $this->date_Create;
    }

    function getUser_upgrade(): int {
        return $this->user_upgrade;
    }

    function getDate_upgrade(): string {
        return $this->date_upgrade;
    }

    function getStado(): int {
        return $this->stado;
    }
    
    //*****************************************
    
    function setId(int $id) {
        $this->id = $id;
    }

    function setReferral_guide_id(int $referral_guide_id) {
        $this->referral_guide_id = $referral_guide_id;
    }

    function setIssue_date(string $issue_date) {
        $this->issue_date = $issue_date;
    }

    function setFile_name(string $file_name) {
        $this->file_name = $file_name;
    }

    function setFlg_response(int $flg_response) {
        $this->flg_response = $flg_response;
    }

    function setError_code(string $error_code) {
        $this->error_code = $error_code;
    }

    function setResponse_descrip(string $response_descrip) {
        $this->response_descrip = $response_descrip;
    }

    function setObservations(string $observations) {
        $this->observations = $observations;
    }

    function setStatus(int $status) {
        $this->status = $status;
    }

    function setUser_Create(int $user_Create) {
        $this->user_Create = $user_Create;
    }

    function setDate_Create(string $date_Create) {
        $this->date_Create = $date_Create;
    }

    function setUser_upgrade(int $user_upgrade) {
        $this->user_upgrade = $user_upgrade;
    }

    function setDate_upgrade(string $date_upgrade) {
        $this->date_upgrade = $date_upgrade;
    }

    function setStado(int $stado) {
        $this->stado = $stado;
    }

    
}



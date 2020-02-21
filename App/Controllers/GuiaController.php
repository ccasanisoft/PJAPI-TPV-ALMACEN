<?php

namespace App\Controllers;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use PDO;
use App\Models\GuiaModel;
/*use Firebase\JWT\JWT;
use App\Controllers\Auth;*/
use App\Entity\EntityReferralGuide;
use App\Entity\EntityReferralGuideItems;
use App\Entity\EntityInfoEmpresa;
use App\Entity\EntitySendInvoiceGR;


class GuiaController {

 protected $container;


 public function __construct($container){
      $this->container = $container;
 }  

 public function __get($property){
    if ($this->container->{$property}) {
        return $this->container->{$property};
    }
 }


public function create_guia_r($request, $response){
	$data = $request->getParsedBody();
	$ruc = $data['header']['ruc'];
    $db = new GuiaModel();
     
    $valor = $this->conexionBDgeneral($ruc);//$db->getEmpresasRuc($ruc);
	
	$empresa = new EntityInfoEmpresa();
	
	$empresa->setruc($ruc);
	
	//foreach($sqlruc as $valor){
		$empresa->setusuSol($valor["usuario_sol"]);
		$empresa->setpassFirma($valor["password_firma"]);
		$empresa->setpassSol($valor["clave_secundario"]);
		$empresa->setrazonSocial($valor["razon_social"]);
		$empresa->setdbhost($valor["host_BD"]);
		$empresa->setdbmane($valor["BD_sistema"]);
		$empresa->setdbusername($valor["usuario_BD"]);
		$empresa->setdbpassword(strval($valor["password_BD"]));

	//}
	
	$entRefGuide = new EntityReferralGuide();
    
    $entRefGuide->setIssueDate($data['header']['IssueDate']);
	$entRefGuide->setNote($data['header']['Note']);
	$entRefGuide->setDescriptionReasonTransfer($data['header']['DescriptionReasonTransfer']);
	$entRefGuide->setTotalGrossWeightGRE($data['header']['TotalGrossWeightGRE']);
	$entRefGuide->setNumberPackages($data['header']['NumberPackages']);
	
	$entRefGuide->setUnitCodeGrossWeightGRE($data['header']['unitCodeGrossWeightGRE']);
	$entRefGuide->setTypeDocumenttransmitter($data['header']['TypeDocumenttransmitter']);
	$entRefGuide->setTransmitterName($empresa->getrazonSocial());
	$entRefGuide->setAddresseeID($data['header']['addresseeID']);
	$entRefGuide->setTypeDocumentaddressee($data['header']['TypeDocumentaddressee']);
	
	$entRefGuide->setAddresseeName($data['header']['addresseeName']);
	$entRefGuide->setMotivemovedCode($data['header']['motivemovedCode']);
	$entRefGuide->setTransfermobility($data['header']['transfermobility']);
	$entRefGuide->setLicensePlateID($data['header']['LicensePlateID']);
	$entRefGuide->setDriverPersonID($data['header']['DriverPersonID']);
	
	$entRefGuide->setDriverPersonDocumentType($data['header']['DriverPersonDocumentType']);
	$entRefGuide->setMovedstartdate($data['header']['movedstartdate']);
	$entRefGuide->setDeliveryUbi($data['header']['DeliveryUbi']);
	$entRefGuide->setDelivery($data['header']['Delivery']);
	
	$entRefGuide->setOriginAddressUbi($data['header']['OriginAddressUbi']);
	$entRefGuide->setOriginAddress($data['header']['OriginAddress']);
	$entRefGuide->setUser_create($data['header']['user_create']);
	$entRefGuide->setEstado("1");
	$entRefGuide->setNameTransportista($data['header']['nameTransportista']);
	
	$result = $db->registerGuiaR($entRefGuide, $data['item'], $empresa);
	
	$entRefGuide->setId($result[1]);
	$entRefGuide->setserieNumero($result[0]);
	$entRefGuide->setDate_create($result[2]);
	
	$datoBeta = $db->valid_beta($empresa);
	
	$result_createXML = $this->create_xml($entRefGuide, $data['item'], $empresa, $datoBeta[0]);
	
	$entRefGuide->setFlg_response($result_createXML['flg_response']);
	$entRefGuide->setError_code($result_createXML['error_code']);
	$entRefGuide->setResponse_descrip($result_createXML['response_descrip']);
	$entRefGuide->setDigest_value($result_createXML['digest_value']);
	
	$db->update_GR($empresa, $entRefGuide);
	
	$result_envio = $this->enviarXML_GR($entRefGuide, $empresa, $datoBeta[0]);
	
	$send_invoice_gr = new EntitySendInvoiceGR();
	
	$send_invoice_gr->setReferral_guide_id($entRefGuide->getId());
	$send_invoice_gr->setIssue_date($entRefGuide->getIssueDate());
	$send_invoice_gr->setFile_name($result_envio['file_name']);
	$send_invoice_gr->setFlg_response($result_envio['flg_response']);
	$send_invoice_gr->setError_code($result_envio['error_code']);
	$send_invoice_gr->setResponse_descrip($result_envio['response_descrip']);
	$send_invoice_gr->setStatus($result_envio['status']);
	$send_invoice_gr->setUser_Create($entRefGuide->getUser_create());
	$send_invoice_gr->setDate_Create($entRefGuide->getDate_create());
	$send_invoice_gr->setStado("1");
	
	$db->send_invoice_GR($empresa, $send_invoice_gr);
	
	
    return $this->response->withJson($result);
}

public function create_xml(EntityReferralGuide $headerGR, array $items, EntityInfoEmpresa $empresa, $datoBeta) {
		
		$header= array(
			  "supplierID"=> (string)($empresa->getruc()),
			  "usuSol"=> (string)($empresa->getusuSol()),//"MODDATOS",
			  "passFirma"=> (string)($empresa->getpassFirma()),//"123456",
			  "serieNumero"=> (string)($headerGR->getserieNumero()),//"T001-00000008",
			  "invoiceTypeCode"=> (string)("09"),
			  "IssueDate"=> (string)($headerGR->getIssueDate()),
			  "Note"=> (string)($headerGR->getNote()),
			  "DescriptionReasonTransfer"=> (string)($headerGR->getDescriptionReasonTransfer()),
			  "TotalGrossWeightGRE"=> (string)($headerGR->getTotalGrossWeightGRE()),
			  "NumberPackages"=> (string)($headerGR->getNumberPackages()),
			  "unitCodeGrossWeightGRE"=> (string)($headerGR->getunitCodeGrossWeightGRE()),
			  "TypeDocumenttransmitter"=> (string)($headerGR->getTypeDocumenttransmitter()),
			  "transmitterName"=> (string)($headerGR->gettransmitterName()),
			  "addresseeID"=> (string)($headerGR->getaddresseeID()),
			  "TypeDocumentaddressee"=> (string)($headerGR->getTypeDocumentaddressee()),
			  "addresseeName"=> (string)($headerGR->getaddresseeName()),
			  "motivemovedCode"=> (string)($headerGR->getmotivemovedCode()),
			  "transfermobility"=> (string)($headerGR->gettransfermobility()),
			  "LicensePlateID"=> (string)($headerGR->getLicensePlateID()),
			  "DriverPersonID"=> (string)($headerGR->getDriverPersonID()),
			  "DriverPersonDocumentType"=> (string)($headerGR->getDriverPersonDocumentType()),
			  "movedstartdate"=> (string)($headerGR->getmovedstartdate()),
			  "DeliveryUbi"=> (string)($headerGR->getDeliveryUbi()),
			  "Delivery"=> (string)($headerGR->getDelivery()),
			  "OriginAddressUbi"=> (string)($headerGR->getOriginAddressUbi()),
			  "OriginAddress"=> (string)($headerGR->getOriginAddress()),
			  "user_create"=> (string)($headerGR->getuser_create())
			);
			
						
		for($contador=1; $contador < count($items) + 1; $contador++){
									
				$item[$contador]= array(
					"ProductID"=> (string)($items[$contador]['ProductCode']),
					"ProductName"=> (string)($items[$contador]['ProductName']),
					"QuantityProduct"=> (string)($items[$contador]['QuantityProduct']),
					"unitCode"=> (string)($items[$contador]['unitCode']),
					);
		}

		$postData["header"] = $header;
		$postData["item"] = $item;
		
		if ($datoBeta == 0) {
			$urlDoc = "post/xml";
		} else {
			$urlDoc = "beta/post/xml";
		}
								
		$context = stream_context_create(array(
			'http' => array(
			'method' => 'POST',
			'header' => "Authorization: application/json\r\n" .
			"Content-Type: application/json\r\n",
			'content' => json_encode($postData)
			)
		));
								
		$url = getenv('WSurl_AWS');
		$response = file_get_contents($url . $urlDoc, false, $context);
								
		if ($response) {
			$res = json_decode($response, true);

			$NC_data = array(
							'flg_response' => $res[0],
							'error_code' => $res[1],
							'response_descrip' => $res[2],
							'digest_value' => $res[4]
			);

		
		} else {
			$NC_data = array(
			'flg_response' => "0",
			'error_code' => "0",
			'response_descrip' => "error al conectarse a AWS",
			'digest_value' => ""
			);

		}
		
		
		return $NC_data ;
		
		
	}
	
public function enviarXML_GR(EntityReferralGuide $headerGR, EntityInfoEmpresa $empresa, $datoBeta): array {
		
		if ($datoBeta == 0){
			$urlDoc = "post/envio";
		} else {
			$urlDoc = "beta/post/envio";
		}

		$supplierID = $empresa->getruc();
		$invoiceTypeCode = "09";
		$serieNumero = $headerGR->getserieNumero();

		$file_name = $supplierID . "-" . $invoiceTypeCode . "-" . $serieNumero;

		$usuSol = $empresa->getusuSol();
		$passSol = $empresa->getpassSol();

		$postData = array(
			'ruc' => $supplierID,
			'typeCode' => $invoiceTypeCode,
			'serieNumero' => $serieNumero,
			'usuSol' => $usuSol,
			'passSol' => $passSol
		);

		// Create the context for the request
		$context = stream_context_create(array(
			'http' => array(
				'method' => 'POST',
				'header' => "Authorization: application/json\r\n" .
					"Content-Type: application/json\r\n",
				'content' => json_encode($postData)
			)
		));

        // Send the request
		$url = getenv('WSurl_AWS');//'http://localhost:8090/FEApi/api/';
		$response = file_get_contents($url . $urlDoc, false, $context);

		if ($response) {
			$res = json_decode($response, true);

			$tipo = "RF";

			if ($res[1] != 0) {
				$status = 3;
			} else {
				if ($res[0] == 1) {
					$status = 4;
				} else {
					$status = 1;
				}
			}
			
			$NC_data = array(
							'flg_response' => $res[0],
							'error_code' => $res[1],
							'response_descrip' => $res[3] . ": " . $res[2],
							'status' => $status,
							'file_name' => $file_name
			);
			
			
		} else {
			$NC_data = array(
			'flg_response' => "0",
			'error_code' => "0",
			'response_descrip' => "error al conectarse a AWS",
			'status' => "1",
			'file_name' => $file_name
			);

		//return $NC_data;
		}
		
		return $NC_data;
		
	}

public function list_motiveGR($request, $response){
	
	$data = $request->getParsedBody();
	$ruc = $data['info_envio']['ruc'];

    $db = new GuiaModel();

     
    $valor = $this->conexionBDgeneral($ruc);//$db->getEmpresasRuc($ruc);
	$empresa = new EntityInfoEmpresa();
	
	$empresa->setruc($ruc);

	
	//foreach($sqlruc as $valor){
		$empresa->setusuSol($valor["usuario_sol"]);
		$empresa->setpassFirma($valor["password_firma"]);
		$empresa->setpassSol($valor["clave_secundario"]);
		$empresa->setrazonSocial($valor["razon_social"]);
		$empresa->setdbhost($valor["host_BD"]);
		$empresa->setdbmane($valor["BD_sistema"]);
		$empresa->setdbusername($valor["usuario_BD"]);
		$empresa->setdbpassword(strval($valor["password_BD"]));

	//}

	$motive_list = $db->motive_list($empresa);
	
	return $this->response->withJson($motive_list);
	
}

public function search_product($request, $response){
	
	$data = $request->getParsedBody();
    $producto = $data['producto'];
	$ruc = $data['ruc'];
    $db = new GuiaModel();

    $valor = $this->conexionBDgeneral($ruc);//$db->getEmpresasRuc($ruc);

	$empresa = new EntityInfoEmpresa();
	
	$empresa->setruc($ruc);
	
	//foreach($sqlruc as $valor){
		$empresa->setdbhost($valor["host_BD"]);
		$empresa->setdbmane($valor["BD_sistema"]);
		$empresa->setdbusername($valor["usuario_BD"]);
		$empresa->setdbpassword(strval($valor["password_BD"]));

	//}
	 
	
	$bd=new GuiaModel();
	
	$list_product=$bd->list_product($producto, $empresa);

	foreach($list_product as $result){
		$result_mostrar[]=$result['productos'];
	}
	
	return $this->response->withJson($result_mostrar);
	
}

public function list_items_sale($request, $response){
	$data = $request->getParsedBody();
    $venta = $data['venta'];
	$ruc = $data['ruc'];
    $db = new GuiaModel();
     
    $valor = $this->conexionBDgeneral($ruc);//$db->getEmpresasRuc($ruc);
	
	$empresa = new EntityInfoEmpresa();
	
	$empresa->setruc($ruc);
	
	//foreach($sqlruc as $valor){
		$empresa->setdbhost($valor["host_BD"]);
		$empresa->setdbmane($valor["BD_sistema"]);
		$empresa->setdbusername($valor["usuario_BD"]);
		$empresa->setdbpassword(strval($valor["password_BD"]));

	//}
	
	$bd=new GuiaModel();
	
	$list_items=$bd->list_items($venta, $empresa);
	
	return $this->response->withJson($list_items);
}

public function customer_sale($request, $response){
	
	$data = $request->getParsedBody();
    $id_sale = $data['id_sale'];
	$ruc = $data['ruc'];
    $db = new GuiaModel();
     
    $valor = $this->conexionBDgeneral($ruc);//$db->getEmpresasRuc($ruc);
	
	$empresa = new EntityInfoEmpresa();
	
	$empresa->setruc($ruc);
	
	//foreach($sqlruc as $valor){
		$empresa->setdbhost($valor["host_BD"]);
		$empresa->setdbmane($valor["BD_sistema"]);
		$empresa->setdbusername($valor["usuario_BD"]);
		$empresa->setdbpassword(strval($valor["password_BD"]));

	//}
	
	$bd=new GuiaModel();
	
	$list_customer=$bd->list_customer($id_sale, $empresa);
	
	return $this->response->withJson($list_customer);
	
}

public function search_header_guia($request, $response){
	
	$data = $request->getParsedBody();
    $id_referral_g = $data['id_referral_g'];
	$ruc = $data['ruc'];
    $db = new GuiaModel();
     
    $valor = $this->conexionBDgeneral($ruc);//$db->getEmpresasRuc($ruc);
	
	$empresa = new EntityInfoEmpresa();
	
	$empresa->setruc($ruc);
	
	//foreach($sqlruc as $valor){
		$empresa->setdbhost($valor["host_BD"]);
		$empresa->setdbmane($valor["BD_sistema"]);
		$empresa->setdbusername($valor["usuario_BD"]);
		$empresa->setdbpassword(strval($valor["password_BD"]));

	//}
	
	$bd=new GuiaModel();
	
	$header_referral_guide=$bd->header_referral_guide($id_referral_g, $empresa);
	
	foreach ($header_referral_guide as $header){
		$referral_g['serieNumero']=$header['serieNumero'];
		$referral_g['IssueDate']=$header['IssueDate'];
		$referral_g['Note']=$header['Note'];
		$referral_g['DescriptionReasonTransfer']=$header['DescriptionReasonTransfer'];
		$referral_g['TotalGrossWeightGRE']=$header['TotalGrossWeightGRE'];
		$referral_g['NumberPackages']=$header['NumberPackages'];
		$referral_g['unitCodeGrossWeightGRE']=$header['unitCodeGrossWeightGRE'];
		$referral_g['addresseeID']=$header['addresseeID'];
		$referral_g['TypeDocumentaddressee']=$header['TypeDocumentaddressee'];
		$referral_g['addresseeName']=$header['addresseeName'];
		$referral_g['motivemovedCode']=$header['motivemovedCode'];
		$referral_g['transfermobility']=$header['transfermobility'];
		$referral_g['LicensePlateID']=$header['LicensePlateID'];
		$referral_g['DriverPersonID']=$header['DriverPersonID'];
		$referral_g['DriverPersonDocumentType']=$header['DriverPersonDocumentType'];
		$referral_g['movedstartdate']=$header['movedstartdate'];
		$referral_g['Delivery']=$header['Delivery'];
		$referral_g['DeliveryUbi']=$header['DeliveryUbi'];
		$referral_g['OriginAddressUbi']=$header['OriginAddressUbi'];
		$referral_g['OriginAddress']=$header['OriginAddress'];
		$referral_g['user_create']=$header['user_create'];
		$referral_g['nameTransportista']=$header['nameTransportista'];
	}
	
	return $this->response->withJson($referral_g);
	
}

public function search_items_guia($request, $response){
	
	$data = $request->getParsedBody();
    $id_referral_g = $data['id_referral_g'];
	$ruc = $data['ruc'];
    $db = new GuiaModel();
     
    $valor = $this->conexionBDgeneral($ruc);//$db->getEmpresasRuc($ruc);
	
	$empresa = new EntityInfoEmpresa();
	
	$empresa->setruc($ruc);
	
	//foreach($sqlruc as $valor){
		$empresa->setdbhost($valor["host_BD"]);
		$empresa->setdbmane($valor["BD_sistema"]);
		$empresa->setdbusername($valor["usuario_BD"]);
		$empresa->setdbpassword(strval($valor["password_BD"]));

	//}
	
	$bd=new GuiaModel();
	
	$referral_g_items=$bd->referral_g_items($id_referral_g, $empresa);
	
	return $this->response->withJson($referral_g_items);
	
}

public function search_hash($request, $response){
	
	$data = $request->getParsedBody();
    $id_referral_g = $data['id_referral_g'];
	$ruc = $data['ruc'];
    $db = new GuiaModel();
     
    $valor = $this->conexionBDgeneral($ruc);//$db->getEmpresasRuc($ruc);
	
	$empresa = new EntityInfoEmpresa();
	
	$empresa->setruc($ruc);
	
	//foreach($sqlruc as $valor){
		$empresa->setdbhost($valor["host_BD"]);
		$empresa->setdbmane($valor["BD_sistema"]);
		$empresa->setdbusername($valor["usuario_BD"]);
		$empresa->setdbpassword(strval($valor["password_BD"]));

	//}
	
	$bd=new GuiaModel();
	
	$consul_hast=$bd->consul_hast($id_referral_g, $empresa);
	
	foreach ($consul_hast as $hash){
		$result['hash']=$hash['digest_value'];
	}
	
	return $this->response->withJson($result);
	
}

public function pdfFile_consult_list($request, $response){
	
	$data = $request->getParsedBody();
    $id_referral_g = $data['id_referral_g'];
	$ruc = $data['ruc'];
    $db = new GuiaModel();
     
    $valor = $this->conexionBDgeneral($ruc);//$db->getEmpresasRuc($ruc);
	
	$empresa = new EntityInfoEmpresa();
	
	$empresa->setruc($ruc);
	
	//foreach($sqlruc as $valor){
		$empresa->setdbhost($valor["host_BD"]);
		$empresa->setdbmane($valor["BD_sistema"]);
		$empresa->setdbusername($valor["usuario_BD"]);
		$empresa->setdbpassword(strval($valor["password_BD"]));

	//}
	
	$bd=new GuiaModel();
	
	$consult_filePDF=$bd->consult_filePDF($id_referral_g, $empresa);
	
	foreach ($consult_filePDF as $pdf){
		$result['file_name']=$pdf['file_name'];
	}
	
	return $this->response->withJson($result);
	
}

public function pdfFile_insert($request, $response){
	
	$data = $request->getParsedBody();
    $id_referral_g = $data['id_referral_g'];
	$file_name = $data['file_name'];
	$ruc = $data['ruc'];
    $db = new GuiaModel();
     
    $valor = $this->conexionBDgeneral($ruc);//$db->getEmpresasRuc($ruc);
	
	$empresa = new EntityInfoEmpresa();
	
	$empresa->setruc($ruc);
	
	//foreach($sqlruc as $valor){
		$empresa->setdbhost($valor["host_BD"]);
		$empresa->setdbmane($valor["BD_sistema"]);
		$empresa->setdbusername($valor["usuario_BD"]);
		$empresa->setdbpassword(strval($valor["password_BD"]));

	//}
	
	$bd=new GuiaModel();
	
	$insert_filePDF=$bd->insert_filePDF($file_name, $id_referral_g, $empresa);
	/*
	foreach ($consult_filePDF as $hash){
		$result['hash']=$hash['digest_value'];
	}*/
	
	return $this->response->withJson($insert_filePDF);
}

public function product_consult($request, $response){
	
	$data = $request->getParsedBody();
    $codeP = $data['codeP'];
	$nameP = $data['nameP'];
	$ruc = $data['ruc'];
    $db = new GuiaModel();
     
    $valor = $this->conexionBDgeneral($ruc);//$db->getEmpresasRuc($ruc);
	
	$empresa = new EntityInfoEmpresa();
	
	$empresa->setruc($ruc);
	
	//foreach($sqlruc as $valor){
		$empresa->setdbhost($valor["host_BD"]);
		$empresa->setdbmane($valor["BD_sistema"]);
		$empresa->setdbusername($valor["usuario_BD"]);
		$empresa->setdbpassword(strval($valor["password_BD"]));

	//}
	
	$bd=new GuiaModel();
	
	$consult_product=$bd->consult_product($codeP, $nameP, $empresa);
	
	foreach($consult_product as $result){
		$result_mostrar['id']=$result['id'];
		$result_mostrar['code']=$result['code'];
		$result_mostrar['name']=$result['name'];
	}
	
	return $this->response->withJson($result_mostrar);
	
}

public function conexionBDgeneral($ruc){
	
	$postData['ruc']=$ruc;
	
	$context = stream_context_create(array(
			'http' => array(
			'method' => 'POST',
			'header' => "Authorization: application/json\r\n" .
			"Content-Type: application/json\r\n",
			'content' => json_encode($postData)
			)
		));
								
	$url = getenv('WSurl_general');
	$urlDoc="v1/Empresa/datos";
	$response = file_get_contents($url . $urlDoc, false, $context);
	$res = json_decode($response, true);
	
	$datosEmp['ruc']=$res['ruc'];
	$datosEmp['razon_social']=$res['razon_social'];
	$datosEmp['usuario_sol']=$res['usuario_sol'];
	
	$datosEmp['clave_secundario']=$res['clave_secundario'];
	$datosEmp['password_firma']=$res['password_firma'];
	$datosEmp['host_BD']=$res['host_BD'];
	
	$datosEmp['BD_sistema']=$res['BD_sistema'];
	$datosEmp['usuario_BD']=$res['usuario_BD'];
	$datosEmp['password_BD']=$res['password_BD'];
	
	
	return $datosEmp;
	die();
}

public function xml_reenvio($request, $response){
	
	$data = $request->getParsedBody();
    $id_referral_g = $data['id_referral_g'];
	$ruc = $data['ruc'];
    $db = new GuiaModel();
     
    $valor = $this->conexionBDgeneral($ruc);
	
	$empresa = new EntityInfoEmpresa();
	
	$empresa->setruc($ruc);
	
		$empresa->setusuSol($valor["usuario_sol"]);
		$empresa->setpassFirma($valor["password_firma"]);
		$empresa->setpassSol($valor["clave_secundario"]);
		$empresa->setrazonSocial($valor["razon_social"]);
		$empresa->setdbhost($valor["host_BD"]);
		$empresa->setdbmane($valor["BD_sistema"]);
		$empresa->setdbusername($valor["usuario_BD"]);
		$empresa->setdbpassword(strval($valor["password_BD"]));

	$bd=new GuiaModel();
	
	$consult_GR_status=$bd->consult_estado_sendGR($id_referral_g, $empresa);
	
	foreach($consult_GR_status as $result_status){
		$status_result=$result_status['status'];
	}
	
	$consult_GR_header=$bd->header_referral_guide($id_referral_g, $empresa);
	$consult_GR_items=$bd->referral_g_items($id_referral_g, $empresa);
		
	$entRefGuide = new EntityReferralGuide();
	$entRefGuide->setId($data['id_referral_g']);
		
	foreach ($consult_GR_header as $header){
			
		$entRefGuide->setserieNumero($header['serieNumero']);
		$entRefGuide->setDate_create($header['date_create']);
			
		$entRefGuide->setIssueDate($header['IssueDate']);
		$entRefGuide->setNote($header['Note']);
		$entRefGuide->setDescriptionReasonTransfer($header['DescriptionReasonTransfer']);
		$entRefGuide->setTotalGrossWeightGRE($header['TotalGrossWeightGRE']);
		$entRefGuide->setNumberPackages($header['NumberPackages']);
			
		$entRefGuide->setUnitCodeGrossWeightGRE($header['unitCodeGrossWeightGRE']);
		$entRefGuide->setTypeDocumenttransmitter($header['TypeDocumenttransmitter']);
		$entRefGuide->setTransmitterName($empresa->getrazonSocial());
		$entRefGuide->setAddresseeID($header['addresseeID']);
		$entRefGuide->setTypeDocumentaddressee($header['TypeDocumentaddressee']);
			
		$entRefGuide->setAddresseeName($header['addresseeName']);
		$entRefGuide->setMotivemovedCode($header['motivemovedCode']);
		$entRefGuide->setTransfermobility($header['transfermobility']);
		$entRefGuide->setLicensePlateID($header['LicensePlateID']);
		$entRefGuide->setDriverPersonID($header['DriverPersonID']);
			
		$entRefGuide->setDriverPersonDocumentType($header['DriverPersonDocumentType']);
		$entRefGuide->setMovedstartdate($header['movedstartdate']);
		$entRefGuide->setDeliveryUbi($header['DeliveryUbi']);
		$entRefGuide->setDelivery($header['Delivery']);
			
		$entRefGuide->setOriginAddressUbi($header['OriginAddressUbi']);
		$entRefGuide->setOriginAddress($header['OriginAddress']);
		$entRefGuide->setUser_create($data['user_create']);
		$entRefGuide->setNameTransportista($header['nameTransportista']);
	}
	
	$count=1;
		
	foreach ($consult_GR_items as $item){
		$referral_g_item[$count]['ProductID']=$item['ProductID'];
		$referral_g_item[$count]['ProductName']=$item['ProductName'];
		$referral_g_item[$count]['QuantityProduct']=$item['QuantityProduct'];
		$referral_g_item[$count]['unitCode']=$item['unitCode'];
		$referral_g_item[$count]['ProductCode']=$item['ProductCode'];
		$count++;
	}
		
	if($status_result == 1  || $status_result == 0){
		
		$datoBeta = $db->valid_beta($empresa);
		
		$result_createXML = $this->create_xml($entRefGuide, $referral_g_item, $empresa, $datoBeta[0]);
		
		$entRefGuide->setFlg_response($result_createXML['flg_response']);
		$entRefGuide->setError_code($result_createXML['error_code']);
		$entRefGuide->setResponse_descrip($result_createXML['response_descrip']);
		$entRefGuide->setDigest_value($result_createXML['digest_value']);
		
		$db->update_GR($empresa, $entRefGuide);
		
		$result_envio = $this->enviarXML_GR($entRefGuide, $empresa, $datoBeta[0]);
		
		$send_invoice_gr = new EntitySendInvoiceGR();
		
		$send_invoice_gr->setReferral_guide_id($entRefGuide->getId());
		$send_invoice_gr->setIssue_date($entRefGuide->getIssueDate());
		$send_invoice_gr->setFile_name($result_envio['file_name']);
		$send_invoice_gr->setFlg_response($result_envio['flg_response']);
		$send_invoice_gr->setError_code($result_envio['error_code']);
		$send_invoice_gr->setResponse_descrip($result_envio['response_descrip']);
		$send_invoice_gr->setStatus($result_envio['status']);
		$send_invoice_gr->setUser_Create($entRefGuide->getUser_create());
		$send_invoice_gr->setDate_Create($entRefGuide->getDate_create());
		$send_invoice_gr->setStado("1");
		
		$db->update_send_invoice_GR_estado($empresa, $entRefGuide);
		$db->send_invoice_GR($empresa, $send_invoice_gr);
		
		$result[0]=$result_envio['status'];
		$result[1]=$entRefGuide->getserieNumero();
	
	}else{
		$result[0]=0;
		$result[1]=$entRefGuide->getserieNumero();
	}
	
	
	
	return $this->response->withJson($result);
	
}

}
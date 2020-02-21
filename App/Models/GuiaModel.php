<?php 


namespace App\Models;

use App\Entity\EntityReferralGuide;
use App\Entity\EntityReferralGuideItems;
use App\Entity\EntityInfoEmpresa;
use App\Entity\EntitySendInvoiceGR;

class GuiaModel extends Db
{
    



/*consultar ubigeo x distrito*/
public function getUbigeoDistrito($district){

        //$con=Db::conectarBd("mysql5022.site4now.net","db_a4d0c2_actecpe","a4d0c2_actecpe","ActecPeru123");
		$con=Db::conectarBd(getenv('localhost'),getenv('dbname'),getenv('db_user_name'),getenv('db_password'));
        $sth = $con->prepare("CALL sp_ubigeoDistrito(?)");
        $sth->execute(array($district));
        $rs  = $sth->fetchAll();
        if(empty($rs)){

            $statusError = [
            "rs" => "no data was found for the district -> ".$district,
            "status" => 404,
            ];
            
            return $statusError;
        }else{
            return $rs;
        }
        
}


/*consultar ubigeo x idubigeo*/
public function getUbigeoCodeUbigeo($code){

        //$con=Db::conectarBd("mysql5022.site4now.net","db_a4d0c2_actecpe","a4d0c2_actecpe","ActecPeru123");
		$con=Db::conectarBd(getenv('localhost'),getenv('dbname'),getenv('db_user_name'),getenv('db_password'));
        $sth = $con->prepare("CALL sp_ubigeoxcode(?)");
        $sth->execute(array($code));
        $rs  = $sth->fetchAll();
        if(empty($rs)){

            $statusError = [
            "rs" => "no data was found for the code -> ".$code,
            "status" => 404,
            ];
            
            return $statusError;
        }else{
            return $rs;
        }
        
}


public function registrar($task){
        $db= Db::conectarBd("localhost","api","root","");
        $sql = "INSERT INTO tasks (task) VALUES (?)";
        $sth = $db->prepare($sql);
        $sth->execute(array($task));
        $input = $db->lastInsertId();
	    return $input;
   
}


//*******************************************************
	
	public function registerGuiaR(EntityReferralGuide $entRefGuide, array $items, EntityInfoEmpresa $empresa){

        
		$con=Db::conectarBd($empresa->getdbhost(),
							$empresa->getdbmane(),
							$empresa->getdbusername(),
							$empresa->getdbpassword());
        
		try{
		
			$statement = $con->prepare('CALL `sp_alm_guiaremision_registrar`(
				:IssueDate,
				:Note,
				:DescriptionReasonTransfer,
				:TotalGrossWeightGRE,
				:NumberPackages,
				:unitCodeGrossWeightGRE,
				:TypeDocumenttransmitter,
				:transmitterName,
				:addresseeID,
				:TypeDocumentaddressee,
				:addresseeName,
				:motivemovedCode,
				:transfermobility,			
				:LicensePlateID,
				:DriverPersonID,
				:DriverPersonDocumentType,
				:movedstartdate,
				:DeliveryUbi,
				:Delivery,
				:OriginAddressUbi,
				:OriginAddress,
				@result_id,
				@result_invoice,
				:usercreate,
				@fechaRegister,
				:estado,
				:nameTransportista
			);');
			
			$statement->execute([
				'IssueDate' => $entRefGuide->getIssueDate(),
				'Note' => $entRefGuide->getNote(),
				'DescriptionReasonTransfer' => $entRefGuide->getDescriptionReasonTransfer(),
				'TotalGrossWeightGRE' => $entRefGuide->getTotalGrossWeightGRE(),
				'NumberPackages' => $entRefGuide->getNumberPackages(),
				'unitCodeGrossWeightGRE' => $entRefGuide->getunitCodeGrossWeightGRE(),
				'TypeDocumenttransmitter' => $entRefGuide->getTypeDocumenttransmitter(),
				'transmitterName' => $entRefGuide->gettransmitterName(),
				'addresseeID' => $entRefGuide->getaddresseeID(),
				'TypeDocumentaddressee' => $entRefGuide->getTypeDocumentaddressee(),
				'addresseeName' => $entRefGuide->getaddresseeName(),
				'motivemovedCode' => $entRefGuide->getmotivemovedCode(),
				'transfermobility' => $entRefGuide->gettransfermobility(),			
				'LicensePlateID' => $entRefGuide->getLicensePlateID(),
				'DriverPersonID' => $entRefGuide->getDriverPersonID(),
				'DriverPersonDocumentType' => $entRefGuide->getDriverPersonDocumentType(),
				'movedstartdate' => $entRefGuide->getmovedstartdate(),
				'DeliveryUbi' => $entRefGuide->getDeliveryUbi(),
				'Delivery' => $entRefGuide->getDelivery(),
				'OriginAddressUbi' => $entRefGuide->getOriginAddressUbi(),
				'OriginAddress' => $entRefGuide->getOriginAddress(),
				'usercreate' => $entRefGuide->getUser_create(),
				'estado' => $entRefGuide->getEstado(),
				'nameTransportista' => $entRefGuide->getNameTransportista()
			]);
			
			$statement->closeCursor();
			$row = $con->query("SELECT @result_id AS result_id, @result_invoice AS result_invoice, @fechaRegister AS registerDate")->fetch(\PDO::FETCH_ASSOC);
			
			$g_referral[0] =$row['result_invoice'];
			$g_referral[1] =$row['result_id'];
			$g_referral[2] =$row['registerDate'];
			
		}catch(PDOException $e){
			$g_referral=0;
		}
		
		if($g_referral[1]> 0){
			
			$guia_remision_item = new EntityReferralGuideItems();
			
				for($contador=1; $contador < count($items) + 1; $contador++){
				
					$guia_remision_item->setId_referral_guide($g_referral[1]);
					$guia_remision_item->setProductID($items[$contador]['ProductID']);
					$guia_remision_item->setProductCode($items[$contador]['ProductCode']);
					$guia_remision_item->setProductName($items[$contador]['ProductName']);
					$guia_remision_item->setQuantityProduct($items[$contador]['QuantityProduct']);
					$guia_remision_item->setUnitCode($items[$contador]['unitCode']);
					$guia_remision_item->setUser_create($entRefGuide->getUser_create());
					$guia_remision_item->setDate_create($g_referral[2]);
					$guia_remision_item->setEstado("1");
					
					$this->regist_GRitems($guia_remision_item, $empresa);
				}
		}
		
		return $g_referral;
        
	}

	public function regist_GRitems(EntityReferralGuideItems $guia_remision_items, EntityInfoEmpresa $empresa){
		
		$con=Db::conectarBd($empresa->getdbhost(),
							$empresa->getdbmane(),
							$empresa->getdbusername(),
							$empresa->getdbpassword());
							
		$statement = $con
		->prepare('INSERT INTO  tec_referral_guide_items VALUES(
		null,
		:id_referral_guide,
		:ProductID,
		:ProductCode,
		:ProductName,
		:QuantityProduct,
		:unitCode,
		:user_create,
		:date_create,
		null,
		null,
		:estado
		);');
		
		$statement->execute([
			'id_referral_guide' => $guia_remision_items->getId_referral_guide(),
			'ProductID' => $guia_remision_items->getProductID(),
			'ProductCode' => $guia_remision_items->getProductCode(),
			'ProductName' => $guia_remision_items->getProductName(),
			'QuantityProduct' => $guia_remision_items->getQuantityProduct(),
			'unitCode' => $guia_remision_items->getUnitCode(),
			'user_create' => $guia_remision_items->getUser_create(),
			'date_create' => $guia_remision_items->getDate_create(),
			'estado' => $guia_remision_items->getEstado()

		]);
		
		
	}

	public function valid_beta(EntityInfoEmpresa $empresa): array
	{
		
		try{
			$con=Db::conectarBd($empresa->getdbhost(),
							$empresa->getdbmane(),
							$empresa->getdbusername(),
							$empresa->getdbpassword());
			$statement = $con->prepare('CALL `validate_beta`(
			);');
			
			$statement->execute([
			]);
			
			$row = $statement->fetchAll(\PDO::FETCH_ASSOC);
			$statement->closeCursor();
			return $row;
			
		}catch(PDOException $e){
			$row[0] =0;
			
			return $row;
		}
	}
	
	public function update_GR(EntityInfoEmpresa $empresa, EntityReferralGuide $entRefGuide)
	{
		$con=Db::conectarBd($empresa->getdbhost(),
							$empresa->getdbmane(),
							$empresa->getdbusername(),
							$empresa->getdbpassword());
		
		try{
		
			$statement = $con->prepare('CALL `sp_alm_guiaremision_modificar`(
				:idP,
				:flg_responseP,
				:error_codeP,
				:response_descripP,
				:digest_valueP

			);');
			
			$statement->execute([
				'idP' => $entRefGuide->getId(),
				'flg_responseP' => $entRefGuide->getFlg_response(),
				'error_codeP' => $entRefGuide->getError_code(),
				'response_descripP' => $entRefGuide->getResponse_descrip(),
				'digest_valueP' => $entRefGuide->getDigest_value()

			]);
			
			
		}catch(PDOException $e){
			
		}
		
	}
	
	public function send_invoice_GR(EntityInfoEmpresa $empresa, EntitySendInvoiceGR $sendInvoice)
	{
		$con=Db::conectarBd($empresa->getdbhost(),
							$empresa->getdbmane(),
							$empresa->getdbusername(),
							$empresa->getdbpassword());
		
		try{
		
			$statement = $con->prepare('CALL `sp_alm_envioguiaremision_registrar`(
				:referral_guide_idP,
				:issue_dateP,
				:file_nameP,
				:flg_responseP,
				:error_codeP,
				:response_descripP,
				:status_grP,
				:user_CreateP,
				:date_CreateP,
				:estadoP

			);');
			
			$statement->execute([
				'referral_guide_idP' => $sendInvoice->getReferral_guide_id(),
				'issue_dateP' => $sendInvoice->getIssue_date(),
				'file_nameP' => $sendInvoice->getFile_name(),
				'flg_responseP' => $sendInvoice->getFlg_response(),
				'error_codeP' => $sendInvoice->getError_code(),
				'response_descripP' => $sendInvoice->getResponse_descrip(),
				'status_grP' => $sendInvoice->getStatus(),
				'user_CreateP' => $sendInvoice->getUser_Create(),
				'date_CreateP' => $sendInvoice->getDate_Create(),
				'estadoP' => $sendInvoice->getStado(),

			]);
			
			
		}catch(PDOException $e){
			
		}
		
	}
	
	public function motive_list(EntityInfoEmpresa $empresa){
		try{
			$con=Db::conectarBd($empresa->getdbhost(),
							$empresa->getdbmane(),
							$empresa->getdbusername(),
							$empresa->getdbpassword());
			$statement = $con->prepare('CALL `sp_alm_motivoguiaremision_list`(
			);');
			
			$statement->execute([
			]);
			
			$row = $statement->fetchAll(\PDO::FETCH_ASSOC);
			$statement->closeCursor();
			return $row;
			
		}catch(PDOException $e){
			$row[0] =0;
			
			return $row;
		}
	}
	
	public function list_product($producto, EntityInfoEmpresa $empresa){
		try{
				$con=Db::conectarBd($empresa->getdbhost(),
							$empresa->getdbmane(),
							$empresa->getdbusername(),
							$empresa->getdbpassword());
				$statement = $con->prepare('CALL sp_alm_producto_list(
				:produc
				);');
				$statement->execute([
				'produc' => $producto
				]);
				
				$row = $statement->fetchAll(\PDO::FETCH_ASSOC);
				$statement->closeCursor();
				return $row;
				
			}catch(PDOException $e){
				$row[0] =0;
				
				return $row;
			}
	}

	public function list_items($venta, EntityInfoEmpresa $empresa){
		try{

				$con=Db::conectarBd($empresa->getdbhost(),
							$empresa->getdbmane(),
							$empresa->getdbusername(),
							$empresa->getdbpassword());
				$statement = $con->prepare('CALL `sp_alm_itemsSale_list` (
				:venta
				);');
				$statement->execute([
				'venta' => $venta
				]);
				
				$row = $statement->fetchAll(\PDO::FETCH_ASSOC);
				$statement->closeCursor();
				
				return $row;
				
				
			}catch(PDOException $e){
				$row[0] =0;
				
				return $row;
			}
	}
	
	public function list_customer($id_sale, EntityInfoEmpresa $empresa){
		
		try{
				$con=Db::conectarBd($empresa->getdbhost(),
							$empresa->getdbmane(),
							$empresa->getdbusername(),
							$empresa->getdbpassword());
				$statement = $con->prepare('CALL sp_alm_customer_list(
				:id_sale
				);');
				$statement->execute([
				'id_sale' => $id_sale
				]);
				
				$row = $statement->fetchAll(\PDO::FETCH_ASSOC);
				$statement->closeCursor();
				return $row;
				
			}catch(PDOException $e){
				$row[0] =0;
				
				return $row;
			}
		
	}
	
	public function header_referral_guide($guia, EntityInfoEmpresa $empresa){
		
		try{
				$con=Db::conectarBd($empresa->getdbhost(),
							$empresa->getdbmane(),
							$empresa->getdbusername(),
							$empresa->getdbpassword());
				$statement = $con->prepare('CALL sp_alm_headerGuiaRemision_list(
				:Rguia
				);');
				$statement->execute([
				'Rguia' => $guia
				]);
				
				$row = $statement->fetchAll(\PDO::FETCH_ASSOC);
				$statement->closeCursor();
				return $row;
				
			}catch(PDOException $e){
				$row[0] =0;
				
				return $row;
			}
		
	}
	
	public function referral_g_items($guia, EntityInfoEmpresa $empresa){
		
		try{
				$con=Db::conectarBd($empresa->getdbhost(),
							$empresa->getdbmane(),
							$empresa->getdbusername(),
							$empresa->getdbpassword());
				$statement = $con->prepare('CALL sp_alm_ItemsGuiaRemision_list(
				:Rguia
				);');
				$statement->execute([
				'Rguia' => $guia
				]);
				
				$row = $statement->fetchAll(\PDO::FETCH_ASSOC);
				$statement->closeCursor();
				return $row;
				
			}catch(PDOException $e){
				$row[0] =0;
				
				return $row;
			}
		
	}
	
	public function consul_hast($guia, EntityInfoEmpresa $empresa){
		
		try{
				$con=Db::conectarBd($empresa->getdbhost(),
							$empresa->getdbmane(),
							$empresa->getdbusername(),
							$empresa->getdbpassword());
				$statement = $con->prepare('CALL sp_alm_GuiaRemisionHast_list(
				:Rguia
				);');
				$statement->execute([
				'Rguia' => $guia
				]);
				
				$row = $statement->fetchAll(\PDO::FETCH_ASSOC);
				$statement->closeCursor();
				
				return $row;
				
			}catch(PDOException $e){
				$row[0] =0;
				
				return $row;
			}
		
	}
	
	public function consult_filePDF($guia, EntityInfoEmpresa $empresa){
		try{
				$con=Db::conectarBd($empresa->getdbhost(),
							$empresa->getdbmane(),
							$empresa->getdbusername(),
							$empresa->getdbpassword());
				$statement = $con->prepare('CALL sp_alm_GuiaRemisionFilePDF_consult(
				:id_GR
				);');
				$statement->execute([
				'id_GR' => $guia
				]);
				
				$row = $statement->fetchAll(\PDO::FETCH_ASSOC);
				$statement->closeCursor();
				
				return $row;
				
			}catch(PDOException $e){
				$row[0] =0;
				
				return $row;
			}
	}
	
	public function insert_filePDF($pdf, $guia, EntityInfoEmpresa $empresa){
		try{
				$con=Db::conectarBd($empresa->getdbhost(),
							$empresa->getdbmane(),
							$empresa->getdbusername(),
							$empresa->getdbpassword());
				$statement = $con->prepare('CALL sp_alm_GuiaRemisionFilePDF_register(
				:id_GR,
				:pdf
				);');
				$statement->execute([
				'id_GR' => $guia,
				'pdf' => $pdf
				]);
				
				return true;
				
			}catch(PDOException $e){

				return false;
			}
	}
	
	public function consult_product($code, $name_product, EntityInfoEmpresa $empresa){
		try{

				$con=Db::conectarBd($empresa->getdbhost(),
							$empresa->getdbmane(),
							$empresa->getdbusername(),
							$empresa->getdbpassword());
				$statement = $con->prepare('CALL sp_alm_producto_consul(
				:id_code
				);');
				$statement->execute([
				'id_code' => $code
				]);
				
				$row = $statement->fetchAll(\PDO::FETCH_ASSOC);
				$statement->closeCursor();
				return $row;
				
			}catch(PDOException $e){
				$row[0] =0;
				
				return $row;
			}
	}
	
	public function update_send_invoice_GR_estado(EntityInfoEmpresa $empresa, EntityReferralGuide $entRefGuide){
		
		$con=Db::conectarBd($empresa->getdbhost(),
							$empresa->getdbmane(),
							$empresa->getdbusername(),
							$empresa->getdbpassword());
		
		try{
		
			$statement = $con->prepare('CALL `sp_alm_sendGuiaRemision_modificar`(
				:idP

			);');
			
			$statement->execute([
				'idP' => $entRefGuide->getId()

			]);
			
			
		}catch(PDOException $e){
			
		}
		
	}

	public function consult_estado_sendGR($codeGR, EntityInfoEmpresa $empresa){
		
		try{

				$con=Db::conectarBd($empresa->getdbhost(),
							$empresa->getdbmane(),
							$empresa->getdbusername(),
							$empresa->getdbpassword());
				$statement = $con->prepare('CALL sp_alm_sendGuiaRemision_consult(
				:idP
				);');
				$statement->execute([
				'idP' => $codeGR
				]);
				
				$row = $statement->fetchAll(\PDO::FETCH_ASSOC);
				$statement->closeCursor();
				return $row;
				
			}catch(PDOException $e){
				$row[0] =0;
				
				return $row;
			}
		
	}
	
	
}


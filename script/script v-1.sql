ALTER TABLE `tec_settings` ADD `referral_guide_format` VARCHAR(50) NOT NULL AFTER `note_credit_bill_number`, ADD `referral_guide_number` INT NOT NULL AFTER `referral_guide_format`;

UPDATE `tec_settings` SET `referral_guide_format` = 'T001-{0000000}' WHERE `tec_settings`.`setting_id` = 1;

-- **************************************************

CREATE TABLE `tec_referral_guide_items` (
  `id` int(11) NOT NULL,
  `id_referral_guide` int(11) NOT NULL,
  `ProductID` varchar(20) NOT NULL,
  `ProductCode` varchar(50) NOT NULL,
  `ProductName` varchar(100) NOT NULL,
  `QuantityProduct` int(11) NOT NULL,
  `unitCode` varchar(10) NOT NULL,
  `user_create` int(11) NOT NULL,
  `date_create` datetime NOT NULL,
  `user_upgrade` int(11) DEFAULT NULL,
  `date_upgrade` datetime DEFAULT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `tec_referral_guide_items`  ADD PRIMARY KEY (`id`);
  
ALTER TABLE `tec_referral_guide_items`  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- **************************************************

CREATE TABLE `tec_referral_guide_files` (
  `id` int(11) NOT NULL,
  `referral_guide_id` int(11) NOT NULL,
  `file_name` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `tec_referral_guide_files`  ADD PRIMARY KEY (`id`);

ALTER TABLE `tec_referral_guide_files`  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- **************************************************

CREATE TABLE `tec_referral_guide` (
  `id` int(11) NOT NULL,
  `serieNumero` varchar(15) NOT NULL,
  `IssueDate` varchar(10) NOT NULL,
  `Note` varchar(200) NOT NULL,
  `DescriptionReasonTransfer` varchar(100) NOT NULL,
  `TotalGrossWeightGRE` varchar(20) NOT NULL,
  `NumberPackages` int(11) NOT NULL,
  `unitCodeGrossWeightGRE` varchar(10) NOT NULL,
  `TypeDocumenttransmitter` int(11) NOT NULL,
  `transmitterName` varchar(100) NOT NULL,
  `addresseeID` varchar(20) NOT NULL,
  `TypeDocumentaddressee` int(11) NOT NULL,
  `addresseeName` varchar(200) NOT NULL,
  `motivemovedCode` varchar(2) NOT NULL,
  `transfermobility` int(11) NOT NULL,
  `LicensePlateID` varchar(10) NOT NULL,
  `DriverPersonID` varchar(12) NOT NULL,
  `DriverPersonDocumentType` int(11) NOT NULL,
  `nameTransportista` varchar(200) NOT NULL,
  `movedstartdate` varchar(11) NOT NULL,
  `DeliveryUbi` varchar(10) NOT NULL,
  `Delivery` varchar(200) NOT NULL,
  `OriginAddressUbi` varchar(200) NOT NULL,
  `OriginAddress` varchar(200) NOT NULL,
  `flg_response` int(11) DEFAULT NULL,
  `error_code` varchar(15) DEFAULT NULL,
  `response_descrip` varchar(2000) DEFAULT NULL,
  `digest_value` varchar(250) DEFAULT NULL,
  `user_create` int(11) NOT NULL,
  `date_create` datetime NOT NULL,
  `user_upgrade` int(11) DEFAULT NULL,
  `date_upgrade` datetime DEFAULT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `tec_referral_guide`  ADD PRIMARY KEY (`id`);

ALTER TABLE `tec_referral_guide`  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- **********************************************************

CREATE TABLE `tec_motive_referral_guide` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `tec_motive_referral_guide` (`id`, `code`, `description`) VALUES
(1, '01', 'VENTA'),
(2, '14', 'VENTA SUJETA A CONFIRMACION DEL COMPRADOR'),
(3, '02', 'COMPRA'),
(4, '04', 'TRASLADO ENTRE ESTABLECIMIENTOS DE LA MISMA EMPRESA'),
(5, '18', 'TRASLADO EMISOR ITINERANTE CP'),
(6, '08', 'IMPORTACION'),
(7, '09', 'EXPORTACION'),
(8, '19', 'TRASLADO A ZONA PRIMARIA'),
(9, '13', 'OTROS');

ALTER TABLE `tec_motive_referral_guide`  ADD PRIMARY KEY (`id`);

ALTER TABLE `tec_motive_referral_guide`  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

-- **********************************************************

CREATE TABLE `tec_send_invoice_gr` (
  `id` int(11) NOT NULL,
  `referral_guide_id` int(11) NOT NULL,
  `issue_date` date NOT NULL,
  `file_name` varchar(50) NOT NULL,
  `flg_response` int(11) NOT NULL,
  `error_code` varchar(15) NOT NULL,
  `response_descrip` varchar(1000) NOT NULL,
  `observations` text,
  `status` int(11) NOT NULL,
  `user_Create` int(11) NOT NULL,
  `date_Create` datetime NOT NULL,
  `user_upgrade` int(11) DEFAULT NULL,
  `date_upgrade` datetime DEFAULT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `tec_send_invoice_gr`  ADD PRIMARY KEY (`id`);

ALTER TABLE `tec_send_invoice_gr`  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- ************************* STORE PROCEDURE ********************************


DELIMITER $$
CREATE  PROCEDURE `sp_alm_reporteguiaremision_list`()
    NO SQL
BEGIN

SELECT id as pid, date_create, addresseeName, if(transfermobility=1, 'Transporte publico', 'Transporte privado') as tipo_transporte , DriverPersonID, serieNumero, LicensePlateID, DescriptionReasonTransfer, if(T2.estadoSend = 1, 'Error de envio', if(T2.estadoSend = 2, 'Enviado', if(T2.estadoSend = 3, 'Rechazado', if(T2.estadoSend = 4, 'Procesado', '')))) as estado_envio
FROM tec_referral_guide
LEFT JOIN (SELECT referral_guide_id, status as estadoSend FROM tec_send_invoice_gr WHERE estado=1) as T2 ON T2.referral_guide_id=tec_referral_guide.id;

END$$
DELIMITER ;

-- **********************************************************

DELIMITER $$
CREATE  PROCEDURE `sp_alm_producto_list`(IN `produc` VARCHAR(100))
    NO SQL
BEGIN

SELECT CONCAT(name, "(",code,")") as productos, id 
FROM tec_products 
WHERE ((name like concat('%',produc,'%')) or (code like concat('%',produc,'%'))) and estado = 1
limit 100;

END$$
DELIMITER ;

-- ************************************************************

DELIMITER $$
CREATE  PROCEDURE `sp_alm_producto_consul`(IN `id_code` VARCHAR(50))
    NO SQL
BEGIN
-- select if(concat("a", code)=id_code, "si","no"), concat("a", code)
-- from tec_products;
SELECT id, code, name 
FROM tec_products 
WHERE code = concat(id_code, '') and estado = 1;

END$$
DELIMITER ;

-- ************************************************************

DELIMITER $$
CREATE  PROCEDURE `sp_alm_producto_consul`(IN `id_code` VARCHAR(50))
    NO SQL
BEGIN
-- select if(concat("a", code)=id_code, "si","no"), concat("a", code)
-- from tec_products;
SELECT id, code, name 
FROM tec_products 
WHERE code = concat(id_code, '') and estado = 1;

END$$
DELIMITER ;

-- ***********************************************************

DELIMITER $$
CREATE  PROCEDURE `sp_alm_motivoguiaremision_list`()
    NO SQL
BEGIN

SELECT code, description FROM tec_motive_referral_guide;

END$$
DELIMITER ;

-- *************************************************************


DELIMITER $$
CREATE  PROCEDURE `sp_alm_itemsSale_list`(IN `venta` INT)
    NO SQL
BEGIN

SELECT t.product_id as id, t.code as code, t.name as name, t.quantity as quantity
FROM tec_sale_items t
LEFT JOIN tec_sales s ON s.id = t.sale_id
WHERE s.id=venta ;


END$$
DELIMITER ;

-- *******************************************************************

DELIMITER $$
CREATE  PROCEDURE `sp_alm_headerGuiaRemision_list`(IN `Rguia` INT)
    NO SQL
BEGIN

SELECT serieNumero, IssueDate, Note, DescriptionReasonTransfer, TotalGrossWeightGRE, unitCodeGrossWeightGRE, addresseeID, addresseeName, transfermobility, LicensePlateID, DriverPersonID, movedstartdate, Delivery, DeliveryUbi, OriginAddressUbi, OriginAddress, digest_value FROM `tec_referral_guide` WHERE `id` = Rguia ;

END$$
DELIMITER ;

-- *****************************************************************

DELIMITER $$
CREATE  PROCEDURE `sp_alm_guiaremision_registrar`(IN `IssueDate` VARCHAR(10), IN `Note` VARCHAR(200), IN `DescriptionReasonTransfer` VARCHAR(100), IN `TotalGrossWeightGRE` VARCHAR(20), IN `NumberPackages` INT, IN `unitCodeGrossWeightGRE` VARCHAR(10), IN `TypeDocumenttransmitter` INT, IN `transmitterName` VARCHAR(100), IN `addresseeID` VARCHAR(20), IN `TypeDocumentaddressee` INT, IN `addresseeName` VARCHAR(200), IN `motivemovedCode` VARCHAR(2), IN `transfermobility` INT, IN `LicensePlateID` VARCHAR(10), IN `DriverPersonID` VARCHAR(12), IN `DriverPersonDocumentType` INT, IN `movedstartdate` VARCHAR(11), IN `DeliveryUbi` VARCHAR(10), IN `Delivery` VARCHAR(200), IN `OriginAddressUbi` VARCHAR(200), IN `OriginAddress` VARCHAR(200), OUT `result_id` INT, OUT `result_invoice` VARCHAR(50), IN `usercreate` INT, OUT `fechaRegister` VARCHAR(50), IN `estado` INT, IN `nameTransportista` VARCHAR(200))
    NO SQL
BEGIN
	DECLARE correlativo VARCHAR(100);
    DECLARE fecharegistro DATETIME;
    
    SET correlativo := (SELECT CONCAT(SUBSTRING(referral_guide_format,1,5), REPEAT( '0', 7 - LENGTH( referral_guide_number + 1) ) , referral_guide_number + 1)  FROM tec_settings);
    
    SET fecharegistro := (select now());

	INSERT INTO `tec_referral_guide`(`serieNumero`, `IssueDate`, 
                                     `Note`, `DescriptionReasonTransfer`, `TotalGrossWeightGRE`, 
                                     `NumberPackages`, `unitCodeGrossWeightGRE`, `TypeDocumenttransmitter`, 
                                     `transmitterName`, `addresseeID`, `TypeDocumentaddressee`, 
                                     `addresseeName`, `motivemovedCode`, `transfermobility`, 
                                     `LicensePlateID`, `DriverPersonID`, `DriverPersonDocumentType`, 
                                     `movedstartdate`, `DeliveryUbi`, 
                                     `Delivery`, `OriginAddressUbi`, `OriginAddress`, `user_create`, `date_create`, `estado`,
                                     `nameTransportista`) 
                                     VALUES (correlativo, IssueDate, 
                                     Note, DescriptionReasonTransfer, TotalGrossWeightGRE, 
                                     NumberPackages, unitCodeGrossWeightGRE, TypeDocumenttransmitter, 
                                     transmitterName, addresseeID, TypeDocumentaddressee, 
                                     addresseeName, motivemovedCode, transfermobility, 
                                     LicensePlateID, DriverPersonID, DriverPersonDocumentType, 
                                     movedstartdate, DeliveryUbi, 
                                     Delivery, OriginAddressUbi, OriginAddress,usercreate, fecharegistro, estado, 
                                     nameTransportista);

    
    
    UPDATE tec_settings SET referral_guide_number = referral_guide_number + 1;

    SET result_id :=  (SELECT LAST_INSERT_ID());
    SET result_invoice := (correlativo);
    SET fechaRegister := (fecharegistro);

END$$
DELIMITER ;

-- ********************************************************************

DELIMITER $$
CREATE  PROCEDURE `sp_alm_guiaremision_modificar`(IN `idP` INT, IN `flg_responseP` INT, IN `error_codeP` VARCHAR(15), IN `response_descripP` VARCHAR(2000), IN `digest_valueP` VARCHAR(250))
    NO SQL
BEGIN

update `tec_referral_guide`
SET 
flg_response = flg_responseP,
error_code = error_codeP, 
response_descrip = response_descripP,
digest_value = digest_valueP
where id = idP;

END$$
DELIMITER ;

-- ********************************************************************

DELIMITER $$
CREATE  PROCEDURE `sp_alm_envioguiaremision_registrar`(IN `referral_guide_idP` INT, IN `issue_dateP` VARCHAR(50), IN `file_nameP` VARCHAR(50), IN `flg_responseP` INT, IN `error_codeP` VARCHAR(50), IN `response_descripP` VARCHAR(1000), IN `status_grP` INT, IN `user_CreateP` INT, IN `date_CreateP` VARCHAR(50), IN `estadoP` INT)
    NO SQL
BEGIN

INSERT INTO tec_send_invoice_gr (referral_guide_id, issue_date, 
                                  file_name, flg_response, error_code, 
                                  response_descrip, status, 
                                  user_Create, date_Create, estado) VALUES (referral_guide_idP, issue_dateP, file_nameP, 
                                  flg_responseP, error_codeP, response_descripP, 
                                  status_grP, user_CreateP, date_CreateP, 
                                  estadoP
                                  );

END$$
DELIMITER ;

-- *******************************************************************

DELIMITER $$
CREATE  PROCEDURE `sp_alm_customer_list`(IN `id_sale` VARCHAR(50))
    NO SQL
BEGIN

SELECT name, cf1, cf2, customers_type_id
FROM tec_sales s
LEFT JOIN tec_customers c ON c.id = s.customer_id
WHERE s.id = id_sale;

END$$
DELIMITER ;

-- ******************************************************************

DELIMITER $$
CREATE  PROCEDURE `sp_alm_ItemsGuiaRemision_list`(IN `Rguia` INT)
    NO SQL
BEGIN

SELECT ProductID, ProductName, QuantityProduct, unitCode FROM `tec_referral_guide_items` WHERE id_referral_guide = Rguia;

END$$
DELIMITER ;

-- *****************************************************************

DELIMITER $$
CREATE  PROCEDURE `sp_alm_GuiaRemisionHast_list`(IN `Rguia` INT)
    NO SQL
BEGIN

SELECT digest_value FROM `tec_referral_guide` WHERE id = Rguia;

END$$
DELIMITER ;

-- ******************************************************************

DELIMITER $$
CREATE  PROCEDURE `sp_alm_GuiaRemisionFilePDF_register`(IN `id_GR` INT, IN `pdf` VARCHAR(300))
    NO SQL
BEGIN

INSERT INTO `tec_referral_guide_files`(`referral_guide_id`, `file_name`) VALUES (id_GR, pdf);

END$$
DELIMITER ;

-- ****************************************************************************

DELIMITER $$
CREATE  PROCEDURE `sp_alm_GuiaRemisionFilePDF_consult`(IN `id_GR` INT)
    NO SQL
BEGIN

SELECT file_name FROM `tec_referral_guide_files` WHERE referral_guide_id = id_GR;

END$$
DELIMITER ;

-- *****************************************************************************

UPDATE tec_settings set version='2.3.10';



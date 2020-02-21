DELIMITER $$
CREATE PROCEDURE `sp_alm_sendGuiaRemision_modificar`(IN `idP` INT)
    NO SQL
BEGIN

UPDATE tec_send_invoice_gr SET estado = 0 WHERE referral_guide_id = idP;

END$$
DELIMITER ;

-- ******************************************************************

DELIMITER $$
CREATE PROCEDURE `sp_alm_headerGuiaRemision_list`(IN `Rguia` INT)
    NO SQL
BEGIN

SELECT serieNumero, IssueDate, Note, DescriptionReasonTransfer, TotalGrossWeightGRE, unitCodeGrossWeightGRE, addresseeID, addresseeName, transfermobility, LicensePlateID, DriverPersonID, movedstartdate, Delivery, DeliveryUbi, OriginAddressUbi, OriginAddress, NumberPackages, TypeDocumenttransmitter, transmitterName, TypeDocumentaddressee, motivemovedCode, DriverPersonDocumentType, nameTransportista, date_create, user_create
FROM `tec_referral_guide` WHERE `id` = Rguia ;

END$$
DELIMITER ;


-- *******************************************************************

DELIMITER $$
CREATE PROCEDURE `sp_alm_ItemsGuiaRemision_list`(IN `Rguia` INT)
    NO SQL
BEGIN

SELECT ProductID, ProductName, QuantityProduct, unitCode, ProductCode FROM `tec_referral_guide_items` WHERE id_referral_guide = Rguia;

END$$
DELIMITER ;

-- *******************************************************************

DELIMITER $$
CREATE PROCEDURE `sp_alm_sendGuiaRemision_consult`(IN `idP` INT)
    NO SQL
BEGIN

-- SELECT status 
-- FROM tec_send_invoice_gr 
-- WHERE referral_guide_id = idP and estado= 1;
SELECT status 
FROM tec_send_invoice_gr 
WHERE referral_guide_id = idP and estado= 1
UNION
SELECT 0 as status
FROM DUAL
WHERE NOT EXISTS (
SELECT status 
FROM tec_send_invoice_gr 
WHERE referral_guide_id = idP and estado= 1
    );


END$$
DELIMITER ;




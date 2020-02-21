<?php


$app->get('/', function ($request, $response, $args) {
  return $this->renderer->render($response, 'index.phtml', $args);
});



$app->group('/api/v1',function()use($app){
   
    $app->group('/guia',function()use($app){
	$app->post('/register','App\Controllers\GuiaController:create_guia_r');
	$app->post('/searchProduc','App\Controllers\GuiaController:search_product');
	$app->get('/header','App\Controllers\GuiaController:search_header_guia');
	$app->get('/items','App\Controllers\GuiaController:search_items_guia');
	$app->get('/hash','App\Controllers\GuiaController:search_hash');
	$app->get('/pdfFile_consult','App\Controllers\GuiaController:pdfFile_consult_list');
	$app->post('/pdfFile_create','App\Controllers\GuiaController:pdfFile_insert');
	$app->post('/product','App\Controllers\GuiaController:product_consult');
	$app->post('/ReenviarXml','App\Controllers\GuiaController:xml_reenvio');
    });

	$app->get('/motive_guia_R','App\Controllers\GuiaController:list_motiveGR');
	
	$app->post('/sale_items','App\Controllers\GuiaController:list_items_sale');
	$app->post('/customer','App\Controllers\GuiaController:customer_sale');
	

    
});
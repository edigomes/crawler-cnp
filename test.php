<?php

// Autoload
require './vendor/autoload.php';

// Namespace
use APICNP\CNP;

$CNP = new CNP("xxx");
//sleep(3);
//$CNP->login("xxxx", "xxxx");
//$CNP->getProducts();
//$res = $CNP->getProductInfo("7908242600015");
//
//$res = $CNP->getFromSegmentId("50000000");
//$res = $CNP->searchInSegment("mesa", "75000000");
$res = $CNP->getSegments();

/*$res = $CNP->storeProduct([
    "indicadorgdsn" => 0,
    "heightmeasurementunitcode" => "MMT",
    "depthmeasurementunitcode" => "MMT",
    "widthmeasurementunitcode" => "MMT",
    "netcontentmeasurementunitcode" => "",
    "netweightmeasurementunitcode" => "KGM",
    "grossweightmeasurementunitcode" => "KGM",
    "tradeitemcountryoforigin" => 76,
    "countrycode" => 76,
    "codigolingua" => 348,
    "globaltradeitemnumber" => null,
    "acceptconditions" => true,
    "informationprovider" => "",
    "codigostatusgtin" => 5,
    "codigotipogtin" => 3, // EAN-13
    "codesegment" => 75000000,
    "codefamily" => 75010000,
    "codeclass" => 75010300,
    "codebrick" => 10002205,
    "productdescription" => "APARADOR MET FER GALV MED",
    "brandname" => "EASY MÓDULOS",
    "codigointerno" => [
        "alternateitemidentificationid" => "EASYM01"
    ],
    "codigotipoproduto" => "2",
    "grossweight" => 15, // PESO BRUTO
    "netweight" => 15, // PESO LÍQUIDO
    "height" => 850, // ALTURA
    "width" => 999.99, // LARGURA
    "depth" => 350, // PROFUNDIDADE
    "isdangeroussubstanceindicated" => false,
    "importclassificationvalue" => "9403.20.00", // NCM
    //"ncmdescricao" => "Outros móveis de metal",
    //"cestnumber" => "28.061.00",
    //"cestdescricao" => "Artigos de casa",
    //"codigocest" => 1110
    "cestnumber" => "",
    "cestdescricao" => "",
    "codigocest" => "",
    "minimumtradeitemlifespanfromtimeofproduction" => "",
    "quantityoflayersperpallet" => "",
    "quantityoftradeitemsperpalletlayer" => "",
    "quantityoftradeitemcontainedinacompletelayer" => "",
    "quantityofcompletelayerscontainedinatradeitem" => "",
    "orderquantitymultiple" => "",
    "orderquantityminimum" => "",
    "totalquantityofnextlowerleveltradeitem" => "",
    "produtopossuiimagem" => true,
    "produtopossuipesobruto" => true,
    "urls" => [
        [
            "nome" => "Principal",
            "url" => "http://easymodulos.com.br/images/products/easy-produto-4-2.jpg",
            "codigotipourl" => "1",
            "codigo" => 1,
            "status" => "novo"
        ]
    ],
    "informacoesNutricionais" => [
        "produtoNutrientDetail" => []
    ],
    "alergenos" => [],
    "tipoFuncionalidade" => "GerarGTIN"
]);*/

//$CNP->requestAccessToken("e3aab56a-c809-3a6f-bf8b-88d80482baed", "488e24bb-f1de-3561-9b21-4756e7870ffc");
//$CNP->getProductInfo("7898566783603");
//$CNP->storeProduct();

print_r($res);

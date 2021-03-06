<?php

namespace APICNP;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Cookie\SessionCookieJar;
use GuzzleHttp\Cookie\FileCookieJar;
use Exception;
use GuzzleHttp\Exception\RequestException;

/**
 * Description of newPHPClass
 *
 * @author edigo
 */
class CNP {

    private $cad;
    private $client;
    private $environment = 0;
    private $grant_type = 'password';
    private $client_id;
    private $username;
    private $password;
    private $cpfCnpj;
    private $cookieJar;
    private $headers = [
        'Pragma'=>'no-cache',
        'Origin'=>'https://cnp.gs1br.org',
        'Accept-Encoding'=>'gzip, deflate, br',
        'Accept-Language'=>'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7,fr;q=0.6,tr;q=0.5',
        'User-Agent'=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.119 Safari/537.36',
        'Content-Type'=>'application/json;charset=UTF-8',
        'Accept'=>'application/json, text/plain, */*',
        'Cache-Control'=>'no-cache',
        'Referer'=>'https://cnp.gs1br.org/backoffice/produtos/index',
        'Connection'=>'keep-alive',
    ];

    function __construct($cpfCnpj, $usuarioId) {

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $this->cpfCnpj = $cpfCnpj;

        $this->cookieJar = new SessionCookieJar("cnp:{$this->cpfCnpj}", true);
        //$this->cookieJar->clear();

        // file to store cookie data
        //$cookieFile = 'cookie_jar.txt';
        //$this->cookieJar = new FileCookieJar($cookieFile, true);

        $this->client = new Client([
            'base_uri' => $this->getApiUrl(),
            'cookies' => $this->cookieJar,
            'debug' => fopen('php://stderr', 'w'),
        ]);
        
        $this->cookieJar->save();
        $this->usuarioId = $usuarioId;
        
    }

    public function login($username, $password) {

        if ($this->verificaUsuarioLogado()) {
            return true;
        }
        
        $response = $this->client->request('POST', '/handlers/requisicao.ashx/LoginBO/Logar', [
            RequestOptions::JSON => [
                "where" => [
                    "login" => $username,
                    "senha" => $password,
                    "cpfcnpj" => null,
                    "challenge" => null,
                    "response" => null
                ]
            ],
            'headers' => $this->headers
        ]);

        $body = $response->getBody();
        $content = $body->getContents();

        //$arrResponse = json_decode($content, true);
        $stdResponse = json_decode($content);

        $this->cookieJar->save();

        // Carrega usuário no GS1
        $this->buscarAssociadosUsuario($this->usuarioId);
        
        return $stdResponse;
        
    }
    
    public function getProducts() {

        try {
            
            $response = $this->client->request('POST', '/handlers/requisicao.ashx/ProdutoBO/BuscarProdutos', [
                "json" => [
                   "campos" => null,
                   "ordenacao" => "dataalteracaodatetime desc",
                   "paginaAtual" => 1,
                   "registroPorPagina" => 25,
                   "tipoCampo" => "LISTA",
                   
                ],
                "headers" => $this->headers,
            ]);
            
            $body = $response->getBody();
            $content = $body->getContents();

            //var_dump($response->getBody()->getContents());
            //$arrResponse = json_decode($content, true);
            
            $stdResponse = json_decode($content);
            
            return $stdResponse;
            
        } catch (RequestException $ex) {
            var_dump($ex->getMessage());
        }

    }
    
    public function storeProductMock($data) {
        $stdResponse = json_decode(file_get_contents( dirname(__FILE__) . DIRECTORY_SEPARATOR . "requests" . DIRECTORY_SEPARATOR . "cadastrar-produto-response.json"));
        return $stdResponse;
    }
    
    public function storeProduct($data) {

        try {

            $response = $this->client->request('POST', '/handlers/requisicao.ashx/ProdutoBO/CadastrarProduto', [
                "json" => [
                    "campos"=> $data
                ],
                "headers" => $this->headers
            ]);
            
            $body = $response->getBody();
            $content = $body->getContents();
            $stdResponse = json_decode($content);
            
            return $stdResponse;
            
        } catch (RequestException $ex) {
            $content = $ex->getResponse()->getBody()->getContents();
            $stdResponse = json_decode($content);
            return $stdResponse;
        }
        
    }

    public function buscarAssociadosUsuario($usuarioId) {

        try {
            
            $response = $this->client->request('POST', '/handlers/requisicao.ashx/LoginBO/BuscarAssociadosUsuario', [
                "json" => [
                    "where" => [
                        "usuarioId" => $usuarioId
                    ]
                ],
                "headers" => $this->headers
            ]);

            $body = $response->getBody();
            $content = $body->getContents();
            $stdResponse = json_decode($content);

            return $stdResponse[0];
            
        } catch (RequestException $ex) {
            $content = $ex->getResponse()->getBody()->getContents();
            exit($content);
            $stdResponse = json_decode($content);
            return $stdResponse;
        }
        
    }
    
    public function verificaUsuarioLogado() {

        try {
            
            $response = $this->client->request('POST', '/handlers/requisicao.ashx/LoginBO/VerificaUsuarioLogado', [
                "headers" => $this->headers
            ]);

            $body = $response->getBody();
            $content = $body->getContents();
            $stdResponse = json_decode($content);

            return $stdResponse;
            
        } catch (RequestException $ex) {
            $content = $ex->getResponse()->getBody()->getContents();
            $stdResponse = json_decode($content);
            return $stdResponse;
        }
        
    }
    
    public function getProductInfo($gtin) {
        
        try {
            
            $response = $this->client->request('POST', '/handlers/requisicao.ashx/ProdutoBO/PesquisaProdutos', [
                "json" => [
                   "campos" => ["globaltradeitemnumber" => $gtin],
                   "ordenacao" => "dataalteracaodatetime desc",
                   "paginaAtual" => 1,
                   "registroPorPagina" => 25,
                   "tipoCampo" => "CLONE"
                ],
                "headers" => $this->headers
            ]);
            
            $body = $response->getBody();
            $content = $body->getContents();
            $stdResponse = json_decode($content);

            return $stdResponse[0];
            
        } catch (RequestException $ex) {
            var_dump($ex->getMessage());
        }
        
    }
    
    public function getFromSegmentId($segmentId) {
        
        $classes = collect(json_decode(file_get_contents( dirname(__FILE__) . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "classes.json") , true));
        
        return $classes->where("segmentId", $segmentId);
        
    }
    
    public function getSegments() {
        
        $classes = collect(json_decode(file_get_contents( dirname(__FILE__) . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "segmentos.json") , true));
        
        return $classes->values();
        
    }
    
    public function searchInSegment($keyword, $segmentId) {
        
        $classes = collect(json_decode(file_get_contents( dirname(__FILE__) . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "classes.json") ));
        
        $results = collect($classes->where("segmentId", $segmentId))->filter(function ($item) use ($keyword) {
            // replace stristr with your choice of matching function
            //var_dump($item);exit;
            return false !== stristr($item->brick, $keyword);
        });
        
        return $results->values();
        
    }

    private function getApiUrl() {
        return 'https://cnp.gs1br.org';
    }

}

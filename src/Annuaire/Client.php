<?php
namespace Sion\Annuaire;
use \Curl\Curl;
class Client{
    
    private static $api_endpoint = "http://localhost:3000/";
    private $client_id;
    private $client_secret;
    private $curl;
    private $credential;
    private $access_token;
    private $refresh_token;
    
    public function __construct($client_id,$client_secret,$params=array()){
        $this->client_id = $client_id;
        $this->client_secret = $client_secret; 
        $this->credential = base64_encode($this->client_id.":".$this->client_secret);
        $this->curl = new Curl();
        $this->hydrate((object)$params);
    }
    public function usePasswordGrant($username,$password)
    {
        $this->curl->setHeader("Authorization","Basic ".$this->credential);
        $this->curl->post(self::$api_endpoint."oauth/token",array(
            "grant_type"=>"password",
            "password"=>$password,
            "username"=>$username,
        ));
        $this->hydrate($this->curl->response);
        return  $this->curl->response;
    }
    public function useRefreshTokenGrant()
    {
        $this->curl->setHeader("Authorization","Basic ".$this->credential);
        $this->curl->post(self::$api_endpoint."oauth/token",array(
            "grant_type"=>"refresh_token",
            "refresh_token" => $this->refresh_token
        ));
        $this->hydrate($this->curl->response);
        return $this->curl->response;
    }
    public function checkToken()
    {
        $this->curl->setHeader("Authorization","Bearer ".$this->access_token);
        $this->curl->get(self::$api_endpoint."secret");
        return $this->curl->response;
    }
    private function hydrate($array)
    {
        if(isset($array->refresh_token))
        {
             $this->refresh_token = $array->refresh_token; 
        }
        if(isset($array->access_token))
        {
            $this->access_token = $array->access_token;
        }
    }
}
<?php
namespace Sion\Annuaire;
use \Curl\Curl;
class Client{
    
    private $api_endpoint = "http://localhost:3000/";
    private $allowed_hydrate = array("access_token","refresh_token","api_endpoint");
    
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
    public function call($method,$url,$data)
    {
        $this->curl->setHeader("Authorization","Bearer ".$this->access_token);
        switch(strtolower($method))
        {
            case "put":
                $method = "put";
                break;
            case "post":
                $method = "post";
                break;
            case "delete":
                $method = "delete";
                break;
            default:
                $method = "get";
            break;
        }
        $this->curl->{$method}($this->api_endpoint.$url,$data);
        return (object)$this->curl->response;
    }
    public function usePasswordGrant($username,$password)
    {
        $this->curl->setHeader("Authorization","Basic ".$this->credential);
        $this->curl->post($this->api_endpoint."oauth/token",array(
            "grant_type"=>"password",
            "password"=>$password,
            "username"=>$username,
        ));
        $this->hydrate($this->curl->response);
        return (object)$this->curl->response;
    }
    public function useRefreshTokenGrant()
    {
        $this->curl->setHeader("Authorization","Basic ".$this->credential);
        $this->curl->post($this->api_endpoint."oauth/token",array(
            "grant_type"=>"refresh_token",
            "refresh_token" => $this->refresh_token
        ));
        $this->hydrate($this->curl->response);
        return (object)$this->curl->response;
    }
    public function checkToken()
    {
        $this->curl->setHeader("Authorization","Bearer ".$this->access_token);
        $this->curl->get($this->api_endpoint."secret");
        return (object)$this->curl->response;
    }
    private function hydrate($array)
    {
        foreach($this->allowed_hydrate as  $option)
        {
            if(isset($array->{$option}))
            {
                $this->{$option} = $array->{$option};
            }
        }
    }
}
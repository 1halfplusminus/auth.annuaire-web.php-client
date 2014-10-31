<?php
session_start();
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);
require __DIR__."/../vendor/autoload.php";

class ClientTest extends PHPUnit_Framework_TestCase
{
    protected static $client;
    protected static $access_token;
    protected static $refresh_token;
    
    public static function setUpBeforeClass()
    {
        self::$client = new Sion\Annuaire\Client("test","f5003cbd-5c40-4c3d-b83e-29623c292b9d");
    }
    public static function tearDownAfterClass()
    {
        self::$client = NULL;
    }
    public function testUsePasswordGrantWithBadPasswordAndUsername()
    {
       $response = self::$client->usePasswordGrant("Googl","dartuchiwa");
       $this->assertEquals(400,$response->code);
    }
     /**
     * @depends testUsePasswordGrantWithBadPasswordAndUsername
     */
    public function testUsePasswordGrantWithGoodPasswordAndUsername()
    {
       $response = self::$client->usePasswordGrant("Google","dartuchiwa");
       $this->assertNull(@$response->error);
    }
     /**
     * @depends testUsePasswordGrantWithGoodPasswordAndUsername
     */
    public function testUseRefreshTokenGrant()
    {
        $response = self::$client->useRefreshTokenGrant();
        $this->assertNull(@$response->error);
        self::$access_token = $response->access_token;
        self::$refresh_token = $response->refresh_token;
    }
    /**
     * @depends testUseRefreshTokenGrant
     */
    public function testCheckToken()
    {
        $response = self::$client->checkToken();
        $this->assertFalse(isset($response->code));
    }
    /**
     * @depends testUseRefreshTokenGrant
     */
    public function testCheckTokenSession()
    {
        $client = new Sion\Annuaire\Client("test","f5003cbd-5c40-4c3d-b83e-29623c292b9d",array(
            "refresh_token" => self::$refresh_token,
            "access_token"=> self::$access_token
        ));
        $response = $client->checkToken();
        $this->assertFalse(isset($response->code));
        
    }
}
?>
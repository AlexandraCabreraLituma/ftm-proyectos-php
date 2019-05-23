<?php
/**
 * Created by PhpStorm.
 * User: Ale
 * Date: 23/5/2019
 * Time: 14:52
 */

namespace App\Tests\Controller;

use App\Controller\ApiProfileController;
use PHPUnit\Framework\TestCase;

use App\Controller\ApiUserController;
use App\Controller\ApiProjectController;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
/**
 * Class ApiProfileControllerTest
 *
 * @package App\Tests\Controller
 *
 * @coversDefaultClass \App\Controller\ApiProfileController
 */
class ApiProfileControllerTest extends WebTestCase
{

    /**
     * @var Client $client
     */
    private static $client;

    public static function setUpBeforeClass()
    {
        self::$client= static::createClient();

    }
    /**
     * @covers ::getCProfile
     */
    public function testGetCProfile200():void
    {
        self::$client->request(Request::METHOD_GET, ApiProfileController::PROFILE_API_PATH);
        $cuerpo= self::$client->getResponse()->getContent();
        self::assertJson($cuerpo);
        /**
         * @var array $datos
         */
        $datos= json_decode($cuerpo,true);
        self::assertArrayHasKey("profiles",$datos);

    }
    /**
     * Implements getCProfile404
     * @covers ::getCProfile
     */
    public function testGetCProfile404()
    {
        self::$client->request(
            request::METHOD_GET,
            ApiProjectController::PROJECT_API_PATH . "/profi"
        );
        /** @var Response $response */
        $response = self::$client->getResponse();
        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * Implements testGetProfileUnique200
     * @param array $profile
     * @return int
     *
     * @covers ::getProfileUnique
     * @depends  testPostProfile201
     */
    public function testGetProfileUnique200(array $profile): int
    {
        $id=$profile['id'];
        self::$client->request(
            Request::METHOD_GET,
            ApiProfileController::PROFILE_API_PATH . '/' . $id
        );
        self::assertEquals(
            Response::HTTP_OK,
            self::$client->getResponse()->getStatusCode()
        );
        $cuerpo = self::$client->getResponse()->getContent();
        self::assertJson($cuerpo);
        /** @var array $datos */
        $datos = json_decode($cuerpo, true);
        self::assertArrayHasKey('profile', $datos);
        self::assertEquals($id, $datos['profile']['id']);

        return $id;
    }


    /**
     * Implements testGetProfileUser200
     * @param array $profile
     *
     * @covers ::getCProfileUser
     * @depends  testPostResult201
     */
    public function testGetProfileUser200(array $profile): void
    {

        $user_id=$$profile['user']['id'];
        self::$client->request(
            Request::METHOD_GET,
            ApiProfileController::PROFILE_API_PATH . ApiProfileController::USERS .  '/'. $user_id
        );
        self::assertEquals(
            Response::HTTP_OK,
            self::$client->getResponse()->getStatusCode()
        );
        $cuerpo = self::$client->getResponse()->getContent();
        self::assertJson($cuerpo);
        /** @var array $datos */
        $datos = json_decode($cuerpo, true);
        self::assertArrayHasKey('profiles', $datos);
    }
    /**
     * Implements testGetProjectUser400
     * @param string $user
     *
     * @covers ::getCProfileUser
     * @dataProvider providerDataNotOk
     */
    public function testGetProfileUser400(string $user): void
    {
        self::$client->request(
            Request::METHOD_GET,
            ApiProfileController::PROFILE_API_PATH . ApiProfileController::USERS .  '/'. $user
        );
        self::assertEquals(
            Response::HTTP_BAD_REQUEST,
            self::$client->getResponse()->getStatusCode()
        );

    }


    /*** @return array */
    public function providerDataNotOk():array {
        $randomico=random_int(1000,20000);
        $datanotOk1="valor 1".$randomico;
        $datanotOk2="valor 2".$randomico;
        return [
            "datanotOk1"=>[$datanotOk1],
            "datanotOk2"=>[$datanotOk2]
        ];
    }

}

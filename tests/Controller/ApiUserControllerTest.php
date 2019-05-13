<?php
/**
 * Created by PhpStorm.
 * User: Ale
 * Date: 29/4/2019
 * Time: 13:36
 */

namespace App\Tests\Controller;



use App\Controller\ApiUserController;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
/**
 * Class ApiUserControllerTest
 *
 * @package App\Tests\Controller
 *
 * @coversDefaultClass \App\Controller\ApiUserController
 */

class ApiUserControllerTest extends WebTestCase
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
     * Implements testPostUser201
     * @throws \Exception
     * @return array
     * @covers ::postUsers
     */
    public function testPostUser201(): array
    {
        $randomico=random_int(1000,20000);
        $username = 'NuevoNombre ' .$randomico;
        $password= 'pas' .$randomico;
        $email= 'NuevoEmail' .$randomico;
        $orcid= 'orcid'.$randomico;
        $firstname= 'firstname'.$randomico;
        $lastname='lastname'.$randomico;
        $phone=$randomico;
        $address='address'.$randomico;


        $datos = [
            'username' => $username,
            'password' => $password,
            'email' => $email. '@example.com',
            'orcid'=>$orcid,
            'firstname' =>$firstname,
            'lastname'=>$lastname,
            'phone' => $phone,
            'address'=>$address
        ];
        self::$client->request(
            Request::METHOD_POST,
            ApiUserController::USER_API_PATH,
            [], [], [], json_encode($datos)
        );
        self::assertEquals(
            Response::HTTP_CREATED,
            self::$client->getResponse()->getStatusCode()
        );
        $cuerpo = self::$client->getResponse()->getContent();
        $datosUser = json_decode($cuerpo, true);
        return $datosUser['user'];
    }


    /**
     * Implements testPostUser400 (los datos del usario ya existe)
     * (volvemos a intentar insertar los datos del usario existen, verificado el username, email y orcid)
     * @return void
     *
     * @covers ::postUsers
     */
    public function testPostUser400(): void
    {
        $cuerpo = self::$client->getResponse()->getContent();
        $datosUser = json_decode($cuerpo, true);

        $userUser= [
            'username' => $datosUser['user']['username'],
            'password' => 'prueba',
            'email'    => $datosUser['user']['email'],
            'orcid'    => $datosUser['user']['orcid'],
            'firstname'=>'prueba',
            'lastname' =>'prueba',
            'phone'    => 123456,
            'address'  =>'prueba'
        ];

        self::$client->request(
            Request::METHOD_POST,
            ApiUserController::USER_API_PATH,
            [], [], [], json_encode($userUser)
        );
        self::assertEquals(
            Response::HTTP_BAD_REQUEST,
            self::$client->getResponse()->getStatusCode()
        );
    }
    /**
     * Implements testPostUser422 (cuando manda el username, email,orcid, password en blanco )
     * @return void
     *
     * @covers ::postUsers
     */
    public function testPostUser422(): void
    {

        $user= [
            'username' => '',
            'password' => '',
            'email' =>  '',
            'orcid'=> '',
            'firstname' =>'prueba',
            'lastname'=>'prueba',
            'phone' => 123456,
            'address'=>'prueba'
        ];
        self::$client->request(
            Request::METHOD_POST,
            ApiUserController::USER_API_PATH,
            [], [], [], json_encode($user)
        );
        self::assertEquals(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            self::$client->getResponse()->getStatusCode()
        );
    }

    /**
     * @covers ::getCUser
     */
    public function testGetCUser200():void
    {
        self::$client->request(Request::METHOD_GET, ApiUserController::USER_API_PATH);
        $cuerpo= self::$client->getResponse()->getContent();
        self::assertJson($cuerpo);
        /**
         * @var array $datos
         */
        $datos= json_decode($cuerpo,true);
        self::assertArrayHasKey("users",$datos);

    }

    /**
     * Implements testGetUserUniqueUserName200
     * @param array $user
     * @return string
     *
     * @covers ::getUserName
     * @depends  testPostUser201
     */
    public function testGetUserUniqueUserName200(array $user): string
    {
        $username=$user['username'];
        self::$client->request(
            Request::METHOD_GET,
            ApiUserController::USER_API_PATH . '/'.$username
        );
        self::assertEquals(
            Response::HTTP_OK,
            self::$client->getResponse()->getStatusCode()
        );
        $cuerpo = self::$client->getResponse()->getContent();
        self::assertJson($cuerpo);
        /** @var array $datos */
        $datos = json_decode($cuerpo, true);
        self::assertArrayHasKey('user', $datos);
        self::assertEquals($username, $datos['user']['username']);

        return $username;
    }

    /**
     * Implements testGetUser404
     * @param string $username
     *
     * @covers ::getUserName
     * @dataProvider providerDataNotOk
     */
    public function testGetUserUniqueUserName404(string $username): void
    {
        self::$client->request(
            Request::METHOD_GET,
            ApiUserController::USER_API_PATH . '/' . $username
        );
        self::assertEquals(
            Response::HTTP_NOT_FOUND,
            self::$client->getResponse()->getStatusCode()
        );

    }

    /**
     * Implements testGetUserUniqueUserName200
     * @param array $user
     * @return string
     *
     * @covers ::getUserEmail
     * @depends  testPostUser201
     */
    public function testGetUserUniqueEmail200(array $user): string
    {
        $email=$user['email'];
        self::$client->request(
            Request::METHOD_GET,
            ApiUserController::USER_API_PATH . ApiUserController::EMAIL .'/'.$email
        );
        self::assertEquals(
            Response::HTTP_OK,
            self::$client->getResponse()->getStatusCode()
        );
        $cuerpo = self::$client->getResponse()->getContent();
        self::assertJson($cuerpo);
        /** @var array $datos */
        $datos = json_decode($cuerpo, true);
        self::assertArrayHasKey('user', $datos);
        self::assertEquals($email, $datos['user']['email']);

        return $email;
    }

    /**
     * Implements testGetUser404
     * @param string $email
     *
     * @covers ::getUserEmail
     * @dataProvider providerDataNotOk
     */
    public function testGetUserUniqueEmail404(string $email): void
    {
        self::$client->request(
            Request::METHOD_GET,
            ApiUserController::USER_API_PATH . ApiUserController::EMAIL .'/'.$email
        );
        self::assertEquals(
            Response::HTTP_NOT_FOUND,
            self::$client->getResponse()->getStatusCode()
        );

    }
    /**
     * Implements testGetUserUniqueUserName200
     * @param array $user
     * @return string
     * @covers ::getUserOrcid
     * @depends  testPostUser201
     */
    public function testGetUserUniqueOrcid200(array $user): string
    {
        $orcid=$user['orcid'];
        self::$client->request(
            Request::METHOD_GET,
            ApiUserController::USER_API_PATH . ApiUserController::ORCID .'/'.$orcid
        );
        self::assertEquals(
            Response::HTTP_OK,
            self::$client->getResponse()->getStatusCode()
        );
        $cuerpo = self::$client->getResponse()->getContent();
        self::assertJson($cuerpo);
        /** @var array $datos */
        $datos = json_decode($cuerpo, true);
        self::assertArrayHasKey('user', $datos);
        self::assertEquals($orcid, $datos['user']['orcid']);

        return $orcid;
    }
    /**
     * Implements testGetUser404
     * @param string $orcid
     * @covers ::getUserOrcid
     * @dataProvider providerDataNotOk
     */
    public function testGetUserUniqueOrcid404(string $orcid): void
    {
        self::$client->request(
            Request::METHOD_GET,
            ApiUserController::USER_API_PATH . ApiUserController::ORCID .'/'.$orcid
        );
        self::assertEquals(
            Response::HTTP_NOT_FOUND,
            self::$client->getResponse()->getStatusCode()
        );

    }
    /**
     * Implements testLogin201
     * @throws \Exception
     * @covers ::login
     * @return int
     */
    public function testLogin201(): int
    {
        $datos = [
            'username' => 'jason',
            'password' => 'dedek454'
        ];
        self::$client->request(
            Request::METHOD_POST,
            ApiUserController::USER_API_PATH.ApiUserController::LOGIN,
            [], [], [], json_encode($datos)
        );
        self::assertEquals(
            Response::HTTP_OK,
            self::$client->getResponse()->getStatusCode()
        );
        $cuerpo = self::$client->getResponse()->getContent();
        $datosUser = json_decode($cuerpo, true);
        return $datosUser['userid'];

    }

    /**
     * Implements testLogin404
     * @throws \Exception
     * @covers ::login
     */
    public function testLogin404(): void
    {
        $datos = [
            'username' => 'jasrrtyon',
            'password' => 'dedek454'
        ];
        self::$client->request(
            Request::METHOD_POST,
            ApiUserController::USER_API_PATH.ApiUserController::LOGIN,
            [], [], [], json_encode($datos)
        );
        self::assertEquals(
            Response::HTTP_NOT_FOUND,
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

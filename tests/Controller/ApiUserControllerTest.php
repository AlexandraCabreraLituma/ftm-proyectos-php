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
     * @return int
     * @covers ::postUsers
     */
    public function testPostUser201(): int
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
        return $datosUser['user']['id'];
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
            'email' =>  $datosUser['user']['email'],
            'orcid'=> $datosUser['user']['orcid'],
            'firstname' =>'prueba',
            'lastname'=>'prueba',
            'phone' => 123456,
            'address'=>'prueba'
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
}

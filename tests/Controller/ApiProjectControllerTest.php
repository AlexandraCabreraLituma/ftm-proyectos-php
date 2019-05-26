<?php
/**
 * Created by PhpStorm.
 * User: Ale
 * Date: 13/5/2019
 * Time: 15:24
 */

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;

use App\Controller\ApiUserController;
use App\Controller\ApiProjectController;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
/**
 * Class ApiProjectControllerTest
 *
 * @package App\Tests\Controller
 *
 * @coversDefaultClass \App\Controller\ApiProjectController
 */
class ApiProjectControllerTest extends WebTestCase
{
    /**
     * @var Client $client
     */
    private static $client;

    public static function setUpBeforeClass()
    {
        self::$client= static::createClient();

    }
    /**-------- crear un usuario
     * Implements testPostUserAux
     * @throws \Exception
     * @return int
     * @covers ::postUsers
     */
    public function testPostUserAux(): int
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
     * Implements testPostResult201
     * @throws \Exception
     * @return array
     * @covers ::postProject
     */

    public function testPostResult201(): array
    {
        $randomico=random_int(100,1000);
        $title ='title '.$randomico;
        $description='description '.$randomico;
        $key_words= 'key_words '.$randomico;
        $user=$this->testPostUserAux();
        $datos = [
            'title' => $title,
            'description'=>$description,
            'key_words'=>$key_words,
            'initial_date'=> '',
            'final_date'=> '',
            'enabled'=> true,
            'user_id' => $user
        ];

        self::$client->request(
            Request::METHOD_POST,
            ApiProjectController::PROJECT_API_PATH,
            [], [], [], json_encode($datos)
        );
        self::assertEquals(
            Response::HTTP_CREATED,
            self::$client->getResponse()->getStatusCode()
        );
        $cuerpo = self::$client->getResponse()->getContent();
        $datosProject = json_decode($cuerpo, true);
        return $datosProject['project'];

    }

    /**
     * Implements testPostResult422
     * @throws \Exception
     * @covers ::postProject
     */

    public function testPostResult422(): void
    {

        $datos = [
            'title' => '',
            'description'=>'',
            'key_words'=>'',
            'initial_date'=> '',
            'final_date'=> '',
            'enabled'=> '',
            'user_id' => ''
        ];

        self::$client->request(
            Request::METHOD_POST,
            ApiProjectController::PROJECT_API_PATH,
            [], [], [], json_encode($datos)
        );
        self::assertEquals(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            self::$client->getResponse()->getStatusCode()
        );

    }

    /**
     * Implements testPostResult400
     * @throws \Exception
     * @covers ::postProject
     */

    public function testPostResult400(): void
    {

        $randomico=random_int(100,1000);
        $title ='title '.$randomico;
        $description='description '.$randomico;
        $key_words= 'key_words '.$randomico;
        $userNoExiste=$randomico;
        $datos = [
            'title' => $title,
            'description'=>$description,
            'key_words'=>$key_words,
            'initial_date'=> '',
            'final_date'=> '',
            'enabled'=> true,
            'user_id' => $userNoExiste
        ];

        self::$client->request(
            Request::METHOD_POST,
            ApiProjectController::PROJECT_API_PATH,
            [], [], [], json_encode($datos)
        );
        self::assertEquals(
            Response::HTTP_BAD_REQUEST,
            self::$client->getResponse()->getStatusCode()
        );

    }

    /**
     * Implements testGetCProject200
     * @covers ::getCProject
     */
    public function testGetCProject200():void
    {
        self::$client->request(Request::METHOD_GET, ApiProjectController::PROJECT_API_PATH);
        $cuerpo= self::$client->getResponse()->getContent();
        self::assertJson($cuerpo);
        /**
         * @var array $datos
         */
        $datos= json_decode($cuerpo,true);
        self::assertArrayHasKey("projects",$datos);

    }
    /**
     * Implements getCProject404
     * @covers ::getCProject
     */
    public function testGetCProject404()
    {
        self::$client->request(
            request::METHOD_GET,
            ApiProjectController::PROJECT_API_PATH . "/projec"
        );
        /** @var Response $response */
        $response = self::$client->getResponse();
        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * Implements testGetProjectUnique200
     * @param array $project
     * @return int
     *
     * @covers ::getProjectUnique
     * @depends  testPostResult201
     */
    public function testGetProjectUnique200(array $project): int
    {
        $id=$project['id'];
        self::$client->request(
            Request::METHOD_GET,
            ApiProjectController::PROJECT_API_PATH . '/' . $id
        );
        self::assertEquals(
            Response::HTTP_OK,
            self::$client->getResponse()->getStatusCode()
        );
        $cuerpo = self::$client->getResponse()->getContent();
        self::assertJson($cuerpo);
        /** @var array $datos */
        $datos = json_decode($cuerpo, true);
        self::assertArrayHasKey('project', $datos);
        self::assertEquals($id, $datos['project']['id']);

        return $id;
    }
    /**
     * Implements testGetProject404
     *
     * @covers ::getProjectUnique
     */
    public function testGetProject404(): void
    {
        $id=random_int(3000,50000);
        self::$client->request(
            Request::METHOD_GET,
            ApiProjectController::PROJECT_API_PATH . '/' . $id
        );
        self::assertEquals(
            Response::HTTP_NOT_FOUND,
            self::$client->getResponse()->getStatusCode()
        );

    }

    /**
     * Implements testGetCProjectEnabled200
     * @covers ::getProjectEnabled
     */
    public function testGetCProjectEnabled200():void
    {
        self::$client->request(Request::METHOD_GET,
                                ApiProjectController::PROJECT_API_PATH. ApiProjectController::ENABLED . '/'. 1);
        $cuerpo= self::$client->getResponse()->getContent();
        self::assertJson($cuerpo);
        /**  * @var array $datos */
        $datos= json_decode($cuerpo,true);
        self::assertArrayHasKey("projects",$datos);
    }
    /**
     * Implements testGetProjectUser200
     * @param array $project
     *
     * @covers ::getCProjectUser
     * @depends  testPostResult201
     */
    public function testGetProjectUser200(array $project): void
    {
        $id=$project['id'];
        $user_id=$project['user']['id'];
        self::$client->request(
            Request::METHOD_GET,
            ApiProjectController::PROJECT_API_PATH . ApiProjectController::USERS .  '/'. $user_id
        );
        self::assertEquals(
            Response::HTTP_OK,
            self::$client->getResponse()->getStatusCode()
        );
        $cuerpo = self::$client->getResponse()->getContent();
        self::assertJson($cuerpo);
        /** @var array $datos */
        $datos = json_decode($cuerpo, true);
        self::assertArrayHasKey('projects', $datos);
    }
    /**
     * Implements testGetProjectUser400
     * @param string $user
     *
     * @covers ::getCProjectUser
     * @dataProvider providerDataNotOk
     */
    public function testGetProjectUser400(string $user): void
    {
        self::$client->request(
            Request::METHOD_GET,
            ApiProjectController::PROJECT_API_PATH . ApiProjectController::USERS .  '/'. $user
        );
        self::assertEquals(
            Response::HTTP_BAD_REQUEST,
            self::$client->getResponse()->getStatusCode()
        );

    }
    /**
     * Implements testGetProjectUser404
     * @covers ::getCProjectUser
     */
    public function testGetProjectUser404(): void
    {
        $user=$this->testPostUserAux();
        self::$client->request(
            Request::METHOD_GET,
            ApiProjectController::PROJECT_API_PATH . ApiProjectController::USERS .  '/'. $user
        );
        self::assertEquals(
            Response::HTTP_NOT_FOUND,
            self::$client->getResponse()->getStatusCode()
        );

    }

    /**
     * Implements testGetProjectUserEnabled200
     * @param array $project
     *
     * @covers ::getCProjectUserEnabled
     * @depends  testPostResult201
     */
    public function testGetProjectUserEnabled200(array $project): void
    {
        $user_id=$project['user']['id'];
        self::$client->request(
            Request::METHOD_GET,
            ApiProjectController::PROJECT_API_PATH . ApiProjectController::USERS . ApiProjectController::ENABLED .  '/'. $user_id
        );
        self::assertEquals(
            Response::HTTP_OK,
            self::$client->getResponse()->getStatusCode()
        );
        $cuerpo = self::$client->getResponse()->getContent();
        self::assertJson($cuerpo);
        /** @var array $datos */
        $datos = json_decode($cuerpo, true);
        self::assertArrayHasKey('projects', $datos);
    }
    /**
     * Implements testGetProjectUserEnabled400
     *
     * @covers ::getCProjectUserEnabled
     */
    public function testGetProjectUserEnabled400(): void
    {
        $user_id=random_int(3000,50000);
        self::$client->request(
            Request::METHOD_GET,
            ApiProjectController::PROJECT_API_PATH . ApiProjectController::USERS . ApiProjectController::ENABLED .  '/'. $user_id
        );
        self::assertEquals(
            Response::HTTP_BAD_REQUEST,
            self::$client->getResponse()->getStatusCode()
        );

    }
    /**
     * Implements testGetProjectUserEnabled404
     * @covers ::getCProjectUserEnabled
     */
    public function testGetProjectUserEnabled404(): void
    {
        $user=$this->testPostUserAux();
        self::$client->request(
            Request::METHOD_GET,
            ApiProjectController::PROJECT_API_PATH . ApiProjectController::USERS . ApiProjectController::ENABLED .  '/'. $user
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

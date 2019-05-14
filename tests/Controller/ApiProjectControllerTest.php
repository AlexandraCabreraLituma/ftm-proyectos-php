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
     * @return int
     * @covers ::postProject
     */

    public function testPostResult201(): int
    {
        $randomico=random_int(100,1000);
        $title ='title '.$randomico;
        $description='description '.$randomico;
        $specific_objectives= 'specific_objectives '.$randomico;
        $category= 'category '.$randomico;
        $user=$this->testPostUserAux();
        $datos = [
            'title' => $title,
            'description'=>$description,
            'specific_objectives'=>$specific_objectives,
            'initial_date'=> '',
            'final_date'=> '',
            'enabled'=> false,
            'category'=> $category,
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
        return $datosProject['project']['id'];
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
            'specific_objectives'=>'',
            'initial_date'=> '',
            'final_date'=> '',
            'enabled'=> '',
            'category'=> '',
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
        $specific_objectives= 'specific_objectives '.$randomico;
        $category= 'category '.$randomico;
        $userNoExiste=$randomico;
        $datos = [
            'title' => $title,
            'description'=>$description,
            'specific_objectives'=>$specific_objectives,
            'initial_date'=> '',
            'final_date'=> '',
            'enabled'=> true,
            'category'=> $category,
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


}

<?php
/**
 * Created by PhpStorm.
 * User: Ale
 * Date: 28/5/2019
 * Time: 12:45
 */

namespace App\Tests\Controller;

use App\Controller\ApiProjectProFileController;
use PHPUnit\Framework\TestCase;

use App\Controller\ApiUserController;
use App\Controller\ApiProjectController;

use App\Controller\ApiProfileController;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
/**
 * Class ApiProjecTProfilesControllerTest
 *
 * @package App\Tests\Controller
 *
 * @coversDefaultClass \App\Controller\ApiProjectProFileController
 */
class ApiProjecTProfilesControllerTest extends WebTestCase
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
     * Implements testPostProjectAux
     * @throws \Exception
     * @return array
     * @covers ::postProject
     */
    public function PostProjectAux($datosUser): array
    {
        $randomico=random_int(100,1000);
        $title ='title '.$randomico;
        $description='description '.$randomico;
        $key_words= 'key_words '.$randomico;
        $user=$datosUser;
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
     * Implements PostProfileAux
     * @throws \Exception
     * @return array
     * @covers ::postProfile
      */
    public function PostProfileAux($datosUser): array
    {
        $randomico=random_int(100,1000);
        $name ='name '.$randomico;
        $description='description '.$randomico;
        $working_day= 'working '.$randomico;
        $nivel= 'nivel '.$randomico;
        $user=$datosUser;
        $datos = [
            'name' => $name,
            'description'=>$description,
            'working_day'=>$working_day,
            'nivel'=> $nivel,
            'user_id' => $user
        ];

        self::$client->request(
            Request::METHOD_POST,
            ApiProfileController::PROFILE_API_PATH,
            [], [], [], json_encode($datos)
        );
        self::assertEquals(
            Response::HTTP_CREATED,
            self::$client->getResponse()->getStatusCode()
        );
        $cuerpo = self::$client->getResponse()->getContent();
        $datosProject = json_decode($cuerpo, true);
        return $datosProject['profile'];

    }
    /**
     * Implements testPostProjectProfile201
     * @throws \Exception
     * @return array
     * @covers ::postProjectProfile
     */
    public function testPostProjectProfile201(): array
    {
        $user=$this->testPostUserAux();
        /** @var array $project */
        $project=$this->PostProjectAux($user);
        /** @var array $profile  */
        $profile=$this->PostProfileAux($user);
        $datos = [
            'project_id'=>$project['id'],
            'profile_id'=>$profile['id'],
            'state'=>true
        ];

        self::$client->request(
            Request::METHOD_POST,
            ApiProjectProFileController::PROJECT_PROFILE_API_PATH,
            [], [], [], json_encode($datos)
        );
        self::assertEquals(
            Response::HTTP_CREATED,
            self::$client->getResponse()->getStatusCode()
        );
        $cuerpo = self::$client->getResponse()->getContent();
        $datosProject = json_decode($cuerpo, true);
        return $datosProject['projectprofile'];

    }



    /**
     * Implements testPostProjectProfile422
     * @throws \Exception
     * @covers ::postProjectProfile
     */

    public function testPostProjectProfile422(): void
    {
        $datos = [
            'project_id'=>'',
            'profile_id'=>'',
            'state'=>true
        ];
        self::$client->request(
            Request::METHOD_POST,
            ApiProjectProFileController::PROJECT_PROFILE_API_PATH,
            [], [], [], json_encode($datos)
        );
        self::assertEquals(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            self::$client->getResponse()->getStatusCode()
        );

    }

    /**
     * Implements testPostProjectProfile400
     * @throws \Exception
     * @covers ::postProjectProfile
     */

    public function testPostProjectProfile400(): void
    {

        $randomico=random_int(100,1000);
        $project_id ='project '.$randomico;
        $profile_id='profile '.$randomico;
        $datos = [
            'project_id'=>$project_id,
            'profile_id'=>$profile_id,
            'state'=>true
        ];

        self::$client->request(
            Request::METHOD_POST,
            ApiProjectProFileController::PROJECT_PROFILE_API_PATH,
            [], [], [], json_encode($datos)
        );
        self::assertEquals(
            Response::HTTP_BAD_REQUEST,
            self::$client->getResponse()->getStatusCode()
        );

    }
    /**
     * Implements testPostProjectProfile403
     * @throws \Exception
     * @covers ::postProjectProfile
     */

    public function testPostProjectProfile403(): void
    {
        $user=$this->testPostUserAux();
        $user2=$this->testPostUserAux();
        /** @var array $project */
        $project=$this->PostProjectAux($user);
        /** @var array $profile  */
        $profile=$this->PostProfileAux($user2);
        $datos = [
            'project_id'=>$project['id'],
            'profile_id'=>$profile['id'],
            'state'=>true
        ];

        self::$client->request(
            Request::METHOD_POST,
            ApiProjectProFileController::PROJECT_PROFILE_API_PATH,
            [], [], [], json_encode($datos)
        );
        self::assertEquals(
            Response::HTTP_FORBIDDEN,
            self::$client->getResponse()->getStatusCode()
        );

    }

    /**
     * Implements testPostProjectProfile409
     * @throws \Exception
     *
     * @covers ::postProjectProfile
     * @param array $projectprofile
     * @depends  testPostProjectProfile201
     */
    public function testPostProjectProfile409(array $projectprofile): void
    {

        $datos= [
            'project_id'=>$projectprofile['project']['id'],
            'profile_id'=>$projectprofile['profile']['id'],
            'state'=>true
        ];

        self::$client->request(
            Request::METHOD_POST,
            ApiProjectProFileController::PROJECT_PROFILE_API_PATH,
            [], [], [], json_encode($datos)
        );
        self::assertEquals(
            Response::HTTP_CONFLICT,
            self::$client->getResponse()->getStatusCode()
        );
    }




}

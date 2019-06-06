<?php
/**
 * Created by PhpStorm.
 * User: Ale
 * Date: 31/5/2019
 * Time: 17:32
 */

namespace App\Tests\Controller;

use App\Controller\ApiProjectProFileController;
use PHPUnit\Framework\TestCase;

use App\Controller\ApiUserController;
use App\Controller\ApiProjectController;

use App\Controller\ApiNominationController;

use App\Controller\ApiProfileController;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
/**
 * Class ApiNominationControllerTest
 *
 * @package App\Tests\Controller
 *
 * @coversDefaultClass \App\Controller\ApiNominationController
 */
class ApiNominationControllerTest extends WebTestCase
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
     * Implements postUserAux
     * @throws \Exception
     * @return int
     * @covers ::postUsers
     */
    public function postUserAux(): int
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
     * Implements postProjectProfileAux
     * @throws \Exception
     * @return array
     * @covers ::postProjectProfile
     */
    public function postProjectProfileAux(): array
    {
        $user=$this->postUserAux();
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
     * Implements testPostNomination201
     * @throws \Exception
     * @return array
     * @covers ::postNominations
     */
    public function testPostNomination201(): array
    {
        /** @var array $user */
        $user=$this->postUserAux();
        /** @var array $projectprofile */
        $projectprofile=$this->postProjectProfileAux();

        $datos = [
            'project_profile_id'=>$projectprofile['id'],
            'user_id'=>$user
        ];

        self::$client->request(
            Request::METHOD_POST,
            ApiNominationController::NOMINATION_API_PATH,
            [], [], [], json_encode($datos)
        );
        self::assertEquals(
            Response::HTTP_CREATED,
            self::$client->getResponse()->getStatusCode()
        );
        $cuerpo = self::$client->getResponse()->getContent();
        $datosNomination = json_decode($cuerpo, true);
        return $datosNomination['nomination'];

    }

    /**
     * Implements testPostNomination422
     * @throws \Exception
     * @covers ::postNominations
     */
    public function testPostNomination422(): void
    {
        $datos = [
            'project_profile_id'=>'',
            'user_id'=>''
        ];

        self::$client->request(
            Request::METHOD_POST,
            ApiNominationController::NOMINATION_API_PATH,
            [], [], [], json_encode($datos)
        );
        self::assertEquals(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            self::$client->getResponse()->getStatusCode()
        );

    }

    /**
     * Implements testPostNomination400
     * @throws \Exception
     * @covers ::postNominations
     */

    public function testPostNomination400(): void
    {

        $randomico=random_int(100,1000);
        $project_profile_id ='project profile '.$randomico;
        $user_id='user '.$randomico;
        $datos = [
            'project_profile_id'=>$project_profile_id,
            'user_id'=>$user_id
        ];
        self::$client->request(
            Request::METHOD_POST,
            ApiNominationController::NOMINATION_API_PATH,
            [], [], [], json_encode($datos)
        );
        self::assertEquals(
            Response::HTTP_BAD_REQUEST,
            self::$client->getResponse()->getStatusCode()
        );

    }

    /**
     * Implements testPostProjectProfile409
     * @throws \Exception
     *
     * @covers ::postNominations
     * @param array $nomination
     * @depends  testPostNomination201
     */
    public function testPostNomination409(array $nomination): void
    {

        $datos = [
            'project_profile_id'=>$nomination['projectprofile']['id'],
            'user_id'=>$nomination['user']['id']
        ];

        self::$client->request(
            Request::METHOD_POST,
            ApiNominationController::NOMINATION_API_PATH,
            [], [], [], json_encode($datos)
        );
        self::assertEquals(
            Response::HTTP_CONFLICT,
            self::$client->getResponse()->getStatusCode()
        );
    }
    /**
     * Implements testGetNominationUnique200
     * @param array $nomination
     * @return int
     *
     * @covers ::getNominationUnique
     * @depends  testPostNomination201
     */
    public function testGetNominationUnique200(array $nomination): int
    {
        $id=$nomination['id'];
        self::$client->request(
            Request::METHOD_GET,
            ApiNominationController::NOMINATION_API_PATH . '/' . $id
        );
        self::assertEquals(
            Response::HTTP_OK,
            self::$client->getResponse()->getStatusCode()
        );
        $cuerpo = self::$client->getResponse()->getContent();
        self::assertJson($cuerpo);
        /** @var array $datos */
        $datos = json_decode($cuerpo, true);
        self::assertArrayHasKey('nomination', $datos);
        self::assertEquals($id, $datos['nomination']['id']);

        return $id;
    }
    /**
     * Implements testGetNominationUnique404
     *
     * @covers ::getNominationUnique
     */
    public function testGetNominationUnique404(): void
    {
        $id=random_int(3000,50000);
        self::$client->request(
            Request::METHOD_GET,
            ApiNominationController::NOMINATION_API_PATH .'/' . $id
        );
        self::assertEquals(
            Response::HTTP_NOT_FOUND,
            self::$client->getResponse()->getStatusCode()
        );

    }


    /**
     * Implements testGetProjectUser200
     * @param array $project
     *
     * @covers ::getCNominationUser
     * @depends  testPostNomination201
     */
    public function testGetNominationByUser200(array $project): void
    {

        $user_id=$project['user']['id'];
        self::$client->request(
            Request::METHOD_GET,
            ApiNominationController::NOMINATION_API_PATH . ApiNominationController::USERS .  '/'. $user_id
        );
        self::assertEquals(
            Response::HTTP_OK,
            self::$client->getResponse()->getStatusCode()
        );
        $cuerpo = self::$client->getResponse()->getContent();
        self::assertJson($cuerpo);
        /** @var array $datos */
        $datos = json_decode($cuerpo, true);
        self::assertArrayHasKey('nominations', $datos);
    }

    /**
     * Implements testGetNominationByProjectsProfile200
     * @param array $nomination
     *
     * @covers ::getCNominationByProjectsProfile
     * @depends  testPostNomination201
     */
    public function testGetNominationByProjectsProfile200(array $nomination): void
    {

        $projectprofile=$nomination['projectprofile']['id'];

        self::$client->request(
            Request::METHOD_GET,
            ApiNominationController::NOMINATION_API_PATH . ApiNominationController::PROJECTSPROFILES .  '/'. $projectprofile
        );
        self::assertEquals(
            Response::HTTP_OK,
            self::$client->getResponse()->getStatusCode()
        );
        $cuerpo = self::$client->getResponse()->getContent();
        self::assertJson($cuerpo);
        /** @var array $datos */
        $datos = json_decode($cuerpo, true);
        self::assertArrayHasKey('nominations', $datos);
    }

    /**
     * Implements testPutNomination202
     * @throws \Exception
     * @param array $nomination
     * @depends  testPostNomination201
     * @covers ::putNomination
     */
    public function testPutNomination202(array $nomination): void
    {
        $id=$nomination['id'];
        $projectprofile=$nomination['projectprofile']['id'];

        /** @var array $user */
        $user=$this->postUserAux();

        $datos = [
            'project_profile_id'=>$projectprofile,
            'user_id'=>$user,
            'state'=>'rejected'
        ];
        self::$client->request(
            Request::METHOD_PUT,
            ApiNominationController::NOMINATION_API_PATH. '/' . $id,
            [], [], [], json_encode($datos)
        );
        self::assertEquals(
            Response::HTTP_ACCEPTED,
            self::$client->getResponse()->getStatusCode()
        );

    }
    /**

     * @throws \Exception
     * @param array $nomination
     * @depends  testPostNomination201
     * @covers ::putNomination
     */
    public function testPutNomination400(array $nomination): void
    {
        $randomico=random_int(1000,20000);
        $id=$nomination['id'];
        $projectprofile=$randomico;
        $user=$randomico;

        $datos = [
            'project_profile_id'=>$projectprofile,
            'user_id'=>$user,
            'state'=>'rejected'
        ];
        self::$client->request(
            Request::METHOD_PUT,
            ApiNominationController::NOMINATION_API_PATH. '/' . $id,
            [], [], [], json_encode($datos)
        );
        self::assertEquals(
            Response::HTTP_BAD_REQUEST,
            self::$client->getResponse()->getStatusCode()
        );

    }
    /**

     * @throws \Exception
     * @param array $nomination
     * @depends  testPostNomination201
     * @covers ::putResult
     */
    public function testPutNomination422(array $nomination): void
    {
        $id=$nomination['id'];
        $datos = [
            'project_profile_id'=>'',
            'user_id'=>'',
            'state'=>''
        ];
        self::$client->request(
            Request::METHOD_PUT,
            ApiNominationController::NOMINATION_API_PATH. '/' . $id,
            [], [], [], json_encode($datos)
        );
        self::assertEquals(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            self::$client->getResponse()->getStatusCode()
        );

    }


    /**
     * Implements testDeleteNomination204
     * @param int $id
     * @return void
     *
     * @covers ::deleteNomination
     * @depends testGetNominationUnique200
     */
    public function testDeleteNomination204(int $id): void
    {
        self::$client->request(
            Request::METHOD_DELETE,
            ApiNominationController::NOMINATION_API_PATH. '/' . $id
        );
        self::assertEquals(
            Response::HTTP_NO_CONTENT,
            self::$client->getResponse()->getStatusCode()
        );

    }

    /**
     * @return void
     *
     * @covers ::deleteUser
     * @throws \Exception
     */
    public function testDeleteNomination404(): void
    {
        $id=random_int(100000,20000000);
        self::$client->request(
            Request::METHOD_DELETE,
            ApiNominationController::NOMINATION_API_PATH. '/' . $id
        );
        self::assertEquals(
            Response::HTTP_NOT_FOUND,
            self::$client->getResponse()->getStatusCode()
        );
    }
}

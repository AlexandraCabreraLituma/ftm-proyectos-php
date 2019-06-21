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

    /**
     * Implements testGetProjectProfileUnique200
     * @param array $projectProfile
     * @return int
     *
     * @covers ::getProjectProfileUnique
     * @depends  testPostProjectProfile201
     */
    public function testGetProjectProfileUnique200(array $projectProfile): int
    {
        $id=$projectProfile['id'];
        self::$client->request(
            Request::METHOD_GET,
            ApiProjectProFileController::PROJECT_PROFILE_API_PATH . '/' . $id
        );
        self::assertEquals(
            Response::HTTP_OK,
            self::$client->getResponse()->getStatusCode()
        );
        $cuerpo = self::$client->getResponse()->getContent();
        self::assertJson($cuerpo);
        /** @var array $datos */
        $datos = json_decode($cuerpo, true);
        self::assertArrayHasKey('projectprofile', $datos);
        self::assertEquals($id, $datos['projectprofile']['id']);

        return $id;
    }
    /**
     * Implements testGetProjectProfileUnique404
     *
     * @covers ::getProjectProfileUnique
     */
    public function testGetProjectProfileUnique404(): void
    {
        $id=random_int(3000,50000);
        self::$client->request(
            Request::METHOD_GET,
            ApiProjectProFileController::PROJECT_PROFILE_API_PATH . '/' . $id
        );
        self::assertEquals(
            Response::HTTP_NOT_FOUND,
            self::$client->getResponse()->getStatusCode()
        );

    }

    /**
     * Implements testGetCProjectEnabled200
     * @covers ::getProjectProfileState
     */
    public function testGetCProjectProfileSatate200():void
    {
        self::$client->request(Request::METHOD_GET,
            ApiProjectProFileController::PROJECT_PROFILE_API_PATH. ApiProjectProFileController::STATES . '/'. 1);
        $cuerpo= self::$client->getResponse()->getContent();
        self::assertJson($cuerpo);
        /**  * @var array $datos */
        $datos= json_decode($cuerpo,true);
        self::assertArrayHasKey("projectsprofiles",$datos);
    }

    /**
     * Implements testGetProjectProfileByProject200
     * @param array $project
     *
     * @covers ::getProjectProfileByProject
     * @depends  testPostProjectProfile201
     */
    public function testGetProjectProfileByProject200(array $projectProfile): void
    {

        $project_id=$projectProfile['project']['id'];
        self::$client->request(
            Request::METHOD_GET,
            ApiProjectProFileController::PROJECT_PROFILE_API_PATH.
                ApiProjectProFileController::PROJECTS .  '/'. $project_id
        );
        self::assertEquals(
            Response::HTTP_OK,
            self::$client->getResponse()->getStatusCode()
        );
        $cuerpo = self::$client->getResponse()->getContent();
        self::assertJson($cuerpo);
        /** @var array $datos */
        $datos = json_decode($cuerpo, true);
        self::assertArrayHasKey('projectsprofiles', $datos);
    }

    /**
     * Implements testGetProjectProfileByProject400
     *
     * @covers ::getProjectProfileByProject
     */
    public function testGetProjectProfileByProject400(): void
    {

        $project_id=random_int(100000,200000);
        self::$client->request(
            Request::METHOD_GET,
            ApiProjectProFileController::PROJECT_PROFILE_API_PATH.
            ApiProjectProFileController::PROJECTS .  '/'. $project_id
        );
        self::assertEquals(
            Response::HTTP_BAD_REQUEST,
            self::$client->getResponse()->getStatusCode()
        );

    }
    /**
     * Implements testGetProjectProfileByProject404
     *
     * @covers ::getProjectProfileByProject
     */
    public function testGetProjectProfileByProject404(): void
    {
        $user=$this->testPostUserAux();
        /** @var array $project */
        $project=$this->PostProjectAux($user);

        self::$client->request(
            Request::METHOD_GET,
            ApiProjectProFileController::PROJECT_PROFILE_API_PATH.
            ApiProjectProFileController::PROJECTS .  '/'. $project['id']
        );
        self::assertEquals(
            Response::HTTP_NOT_FOUND,
            self::$client->getResponse()->getStatusCode()
        );

    }

    /**
     * Implements testGetProjectProfileByProfile200
     * @param array $project
     *
     * @covers ::getProjectProfileByProfile
     * @depends  testPostProjectProfile201
     */
    public function testGetProjectProfileByProfile200(array $projectProfile): void
    {

        $profile_id=$projectProfile['profile']['id'];
        self::$client->request(
            Request::METHOD_GET,
            ApiProjectProFileController::PROJECT_PROFILE_API_PATH.
            ApiProjectProFileController::PROFILES .  '/'. $profile_id
        );
        self::assertEquals(
            Response::HTTP_OK,
            self::$client->getResponse()->getStatusCode()
        );
        $cuerpo = self::$client->getResponse()->getContent();
        self::assertJson($cuerpo);
        /** @var array $datos */
        $datos = json_decode($cuerpo, true);
        self::assertArrayHasKey('projectsprofiles', $datos);
    }

    /**
     * Implements testGetProjectProfileByProject400
     *
     * @covers ::getProjectProfileByProfile
     */
    public function testGetProjectProfileByProfile400(): void
    {

        $profile_id=random_int(100000,200000);
        self::$client->request(
            Request::METHOD_GET,
            ApiProjectProFileController::PROJECT_PROFILE_API_PATH.
            ApiProjectProFileController::PROFILES .  '/'. $profile_id
        );
        self::assertEquals(
            Response::HTTP_BAD_REQUEST,
            self::$client->getResponse()->getStatusCode()
        );

    }
    /**
     * Implements testGetProjectProfileByProfile404
     *
     * @covers ::getProjectProfileByProfile
     */
    public function testGetProjectProfileByProfile404(): void
    {
        $user=$this->testPostUserAux();
        /** @var array $project */
        $profile=$this->PostProfileAux($user);

        self::$client->request(
            Request::METHOD_GET,
            ApiProjectProFileController::PROJECT_PROFILE_API_PATH.
            ApiProjectProFileController::PROFILES .  '/'. $profile['id']
        );
        self::assertEquals(
            Response::HTTP_NOT_FOUND,
            self::$client->getResponse()->getStatusCode()
        );

    }


    /**
     * Implements testPutProjectProfile202
     * @throws \Exception
     * @param array $project_profile
     * @depends  testPostProjectProfile201
     * @covers ::putProjectProfile
     */
    public function testPutProjectProfile202(array $project_profile): void
    {
        $id=$project_profile['id'];

        $user=$this->testPostUserAux();
        /** @var array $project */
        $project=$this->PostProjectAux($user);
        /** @var array $profile  */
        $profile=$this->PostProfileAux($user);
        $datos = [
            'project_id'=>$project['id'],
            'profile_id'=>$profile['id'],
            'state'=>false
        ];

        self::$client->request(
            Request::METHOD_PUT,
            ApiProjectProFileController::PROJECT_PROFILE_API_PATH. '/' . $id,
            [], [], [], json_encode($datos)
        );
        self::assertEquals(
            Response::HTTP_ACCEPTED,
            self::$client->getResponse()->getStatusCode()
        );

    }
    /**

     * @throws \Exception
     * @param array $project_profile
     * @depends  testPostProjectProfile201
     * @covers ::putProjectProfile
     */
    public function testPutProjectProfile400(array $project_profile): void
    {
        $id=$project_profile['id'];

        $randomico=random_int(1000,20000);
        $project=$randomico;
        $profile=$randomico;

        $datos = [
            'project_id'=>$project,
            'profile_id'=>$profile,
            'state'=>true
        ];
        self::$client->request(
            Request::METHOD_PUT,
            ApiProjectProFileController::PROJECT_PROFILE_API_PATH. '/' . $id,
            [], [], [], json_encode($datos)
        );
        self::assertEquals(
            Response::HTTP_BAD_REQUEST,
            self::$client->getResponse()->getStatusCode()
        );

    }

    /**
     * Implements testPutProjectProfile202
     * @throws \Exception
     * @param array $project_profile
     * @depends  testPostProjectProfile201
     * @covers ::putProjectProfile
     */
    public function testPutProjectProfile403(array $project_profile): void
    {
        $id=$project_profile['id'];

        $user=$this->testPostUserAux();
        /** @var array $project */
        $project=$this->PostProjectAux($user);
        $user2=$this->testPostUserAux();
        /** @var array $profile  */
        $profile=$this->PostProfileAux($user2);
        $datos = [
            'project_id'=>$project['id'],
            'profile_id'=>$profile['id'],
            'state'=>false
        ];

        self::$client->request(
            Request::METHOD_PUT,
            ApiProjectProFileController::PROJECT_PROFILE_API_PATH. '/' . $id,
            [], [], [], json_encode($datos)
        );
        self::assertEquals(
            Response::HTTP_FORBIDDEN,
            self::$client->getResponse()->getStatusCode()
        );

    }

    /**
     * Implements testPutProjectProfile404
     * @throws \Exception
     * @covers ::putProjectProfile
     */
    public function testPutProjectProfile404(): void
    {
        $randomico=random_int(1000,20000);
        $id=$randomico;
        $project=$randomico;
        $profile=$randomico;

        $datos = [
            'project_id'=>$project,
            'profile_id'=>$profile,
            'state'=>true
        ];
        self::$client->request(
            Request::METHOD_PUT,
            ApiProjectProFileController::PROJECT_PROFILE_API_PATH. '/' . $id,
            [], [], [], json_encode($datos)
        );
        self::assertEquals(
            Response::HTTP_NOT_FOUND,
            self::$client->getResponse()->getStatusCode()
        );

    }
    /**

     * @throws \Exception
     * @param array $project_profile
     * @depends  testPostProjectProfile201
     * @covers ::putProjectProfile
     */
    public function testPutProjectProfile422(array $project_profile): void
    {
        $id=$project_profile['id'];
        $datos = [
            'project_id'=>'',
            'profile_id'=>'',
            'state'=>true
        ];
        self::$client->request(
            Request::METHOD_PUT,
            ApiProjectProFileController::PROJECT_PROFILE_API_PATH. '/' . $id,
            [], [], [], json_encode($datos)
        );
        self::assertEquals(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            self::$client->getResponse()->getStatusCode()
        );

    }

    /**
     * @throws \Exception
     * @covers ::optionsProjectProfile
     * @param array $project_profile
     * @depends  testPostProjectProfile201
     * @return void
     */
    public function testOptionsProjectProfile200(array $project_profile):void
    {
        $id=$project_profile['id'];

        self::$client->request(Request::METHOD_OPTIONS, ApiProjectProFileController::PROJECT_PROFILE_API_PATH. '/' . $id);
        self::assertEquals(
            Response::HTTP_OK,
            self::$client->getResponse()->getStatusCode()
        );

    }
    /**
     * @throws \Exception
     * @covers ::optionsProjectProfile
     * @return void
     */
    public function testOptionsProjectProfile404():void
    {
        $randomico=random_int(1000,20000);
        $id=$randomico;
        self::$client->request(Request::METHOD_OPTIONS,
            ApiProjectProFileController::PROJECT_PROFILE_API_PATH. '/' . $id);
        self::assertEquals(
            Response::HTTP_NOT_FOUND,
            self::$client->getResponse()->getStatusCode()
        );

    }
    /**
     * Implements testDeleteProjectProfile204
     * @param int $id
     * @return void
     *
     * @covers ::deleteProjectProfile
     * @depends testGetProjectProfileUnique200
     */
    public function testDeleteProjectProfile204(int $id): void
    {
        self::$client->request(
            Request::METHOD_DELETE,
            ApiProjectProFileController::PROJECT_PROFILE_API_PATH. '/' . $id);
        self::assertEquals(
            Response::HTTP_NO_CONTENT,
            self::$client->getResponse()->getStatusCode()
        );

    }
    /**
     * @return void
     *
     * @covers ::deleteProjectProfile
     * @throws \Exception
     */
    public function testDeleteProjectProfile404(): void
    {
        $id=random_int(100000,20000000);
        self::$client->request(
            Request::METHOD_DELETE,
            ApiProjectProFileController::PROJECT_PROFILE_API_PATH. '/' . $id);
              self::assertEquals(
            Response::HTTP_NOT_FOUND,
            self::$client->getResponse()->getStatusCode()
        );
    }

    /**
     * Implements testSearchAdvanceProjectProfile200
     * @param array $projectProfile
     * @return void
     * @throws \Exception
     * @covers ::searchAdvanceProjectProfile
     * @depends testPostProjectProfile201
     */
    public function testSearchAdvanceProjectProfile200(array $projectProfile): void
    {
        $name=$projectProfile['profile']['name'];
        $nivel=$projectProfile['profile']['nivel'];
        $working_Day= $projectProfile['profile']['working_day'];
        $title=$projectProfile['project']['title'];
        $initial_date =$projectProfile['project']['initial_date'];
        $final_date =$projectProfile['project']['final_date'];

        $datos = [
            'name'=> $name,
            'nivel'=> $nivel,
            'working_day'=> $working_Day,
            'title' => $title,
            'initial_date'=> $initial_date,
            'final_date'=> $final_date,
            'state'=> true,
        ];

        self::$client->request(
            Request::METHOD_POST,
            ApiProjectProFileController::PROJECT_PROFILE_API_PATH. ApiProjectProFileController::SEARCH. ApiProjectProFileController::ADVANCE  ,
            [], [], [], json_encode($datos)
        );
        self::assertEquals(
            Response::HTTP_OK,
            self::$client->getResponse()->getStatusCode()
        );
        $cuerpo = self::$client->getResponse()->getContent();
        self::assertJson($cuerpo);

    }


}

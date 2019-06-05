<?php
/**
 * Created by PhpStorm.
 * User: Ale
 * Date: 28/5/2019
 * Time: 11:54
 */

namespace App\Controller;


use App\Entity\Profile;
Use App\Entity\Projectprofile;
use App\Entity\Project;

use App\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
/**
 * Class ApiProjectProFileController
 * @package App\Controller
 * @Route(path=ApiProjectProFileController::PROJECT_PROFILE_API_PATH, name="api_project_profile_")
 *
 */
class ApiProjectProFileController extends AbstractController
{
    //ruta de la api de project profile
    const PROJECT_PROFILE_API_PATH='/api/v1/projectsProfiles';
    const STATES = '/states';
    const PROJECTS = '/projects';
    const PROFILES='/profiles';
    /**
     * @Route(path="", name="post",methods={Request::METHOD_POST})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function postProjectProfile(Request $request):Response{
        $datosPeticion=$request->getContent();
        $datos=json_decode($datosPeticion,true);

        $em = $this->getDoctrine()->getManager();
        if(empty($datos['project_id']) || empty($datos['profile_id']))
        {
            return $this->error422();
        }
        /** @var Project $project */
        $project=$em->getRepository(Project::class)->find($datos['project_id']);

        /** @var Profile $profile */
        $profile=$em->getRepository(Profile::class)->find($datos['profile_id']);

        if($project===null || $profile===null){
            return $this->error400();
        }
        $userProject=$project->getUser()->getId();
        $userProfile=$profile->getUser()->getId();

        if($userProject!==$userProfile){
             return $this->error403();
        }
        /** @var Projectprofile projectprofile */
        $projectprofileExist = $em->getRepository(Projectprofile::class)->findOneBy(array('project' => $project, 'profile' => $profile));

        if($projectprofileExist!==null){
            return $this->error409();
        }

        /**
         * @var Projectprofile projectprofile
         */
        $projectprofile= new Projectprofile( $project, $profile,$datos['state']);
        $em=$this->getDoctrine()->getManager();
        $em ->persist($projectprofile);
        $em->flush();
        return new JsonResponse(
            ["projectprofile" => $projectprofile],
            Response::HTTP_CREATED
        );

    }
    /**
     * @Route(path="/{id}", name="get_project_profile", methods={Request::METHOD_GET})
     * @param Projectprofile $projectprofile
     * @return JsonResponse
     */
    public function getProjectProfileUnique(?Projectprofile $projectprofile = null): JsonResponse
    {
        return (empty($projectprofile))
            ? $this->error404()
            : new JsonResponse(['projectprofile' => $projectprofile],
                Response::HTTP_OK);
    }

    /**
     * @Route(path="/states/{state}", name="getc_enabled", methods={Request::METHOD_GET})
     * @return JsonResponse
     * @param boolean $state
     */
    public function getProjectProfileState($state): JsonResponse
    {
        $em=$this->getDoctrine()->getManager();
        /** * @var Projectprofile[] projectprofile */
        $projectprofile =$em-> getRepository(Projectprofile::class)->findBy(['state' =>$state]);
        return (empty($projectprofile))
            ? $this-> error404()
            : new JsonResponse( ['projectsprofiles' => $projectprofile],
                Response::HTTP_OK);
    }

    /**
     * @Route(path="/projects/{project_id}", name="getc_project", methods={Request::METHOD_GET})
     * @return JsonResponse
     * @param int $project_id
     */
    public function getProjectProfileByProject(int $project_id): JsonResponse
    {
        $em=$this->getDoctrine()->getManager();
        /** @var Project $project */
        $project=$em->getRepository(Project::class)->find($project_id);
        if($project===null){
            return $this->error400();
        }
        /** * @var Projectprofile[] projectsprofiles */
        $projectsprofiles =$em-> getRepository(Projectprofile::class)->findBy(['project' =>$project]);
        return (empty($projectsprofiles))
            ? $this-> error404()
            : new JsonResponse( ['projectsprofiles' => $projectsprofiles],
                Response::HTTP_OK);
    }

    /**
     * @Route(path="/profiles/{profile_id}", name="getc_profile", methods={Request::METHOD_GET})
     * @return JsonResponse
     * @param int $profile_id
     */
    public function getProjectProfileByProfile(int $profile_id): JsonResponse
    {
        $em=$this->getDoctrine()->getManager();
        /** @var Profile $profile */
        $profile=$em->getRepository(Profile::class)->find($profile_id);

        if($profile===null){
            return $this->error400();
        }
        /** * @var Projectprofile[] projectsprofiles */
        $projectsprofiles =$em-> getRepository(Projectprofile::class)->findBy(['profile' =>$profile]);
        return (empty($projectsprofiles))
            ? $this-> error404()
            : new JsonResponse( ['projectsprofiles' => $projectsprofiles],
                Response::HTTP_OK);
    }
    /**
     * @param Request $request
     * @return Response
     * @Route(path="/search", name="search", methods={"POST"})
     */
    public function searchProjectProfile(Request $request): Response{
        $em = $this->getDoctrine()->getManager();
        $dataRequest = $request->getContent();
        $data = json_decode($dataRequest, true);

        if ($data['project_id']!= ""){
            $valorproject=' and p.project=?2';
        }
        else{
            $valorproject='';
        }
        if ($data['profile_id']!= ""){
            $valorprofile=' and p.profile=?3';
        }else{$valorprofile='';}

        $query = $em->createQuery('SELECT p FROM App\Entity\Projectprofile p WHERE p.state=?1 '.$valorproject.$valorprofile);
        $query->setParameter('1',$data['state']??true);


        if ($data['project_id']!= ""){
            $query->setParameter('2', $data['project_id']);
        }
        if ($data['profile_id']!= ""){
            $query->setParameter('3', $data['profile_id']);
        }

        /** * @var Projectprofile[] $projectsprofiles */
        $projectsprofiles = $query->getResult();

        return (empty($projectsprofiles))
            ? $this->error404()
            : new JsonResponse(
                ['projectsprofiles'=>$projectsprofiles],
                Response::HTTP_OK);
    }
    /**
     * @Route(path="/{id}", name="options_project_profile", methods={ Request::METHOD_OPTIONS })
     * @param Projectprofile|null $projectprofile
     * @return Response
     */
    public function optionsProjectProfile(?Projectprofile $projectprofile = null):Response{

        if (null === $projectprofile) {
            return $this->error404();
        }
        $options="POST,PATCH,GET,PUT,DELETE,OPTIONS";
        return new JsonResponse(null,Response::HTTP_OK ,["Allow" => $options]);
    }

    /**
     * @Route(path="/{id}",name="put",methods={Request::METHOD_PUT})
     * @param Projectprofile|null $projectprofile
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function putProjectProfile(?Projectprofile $projectprofile=null, Request $request):Response{
        $em = $this->getDoctrine()->getManager();
        if (null === $projectprofile) {
            return $this->error404();
        }
        $datosPeticion=$request->getContent();
        $datos=json_decode($datosPeticion,true);

        if(empty($datos['project_id']) || empty($datos['profile_id']))
        {
            return $this->error422();
        }
        /** @var Project $project */
        $project=$em->getRepository(Project::class)->find($datos['project_id']);

        /** @var Profile $profile */
        $profile=$em->getRepository(Profile::class)->find($datos['profile_id']);

        if($project===null || $profile===null){
            return $this->error400();
        }
        $userProject=$project->getUser()->getId();
        $userProfile=$profile->getUser()->getId();

        if($userProject!==$userProfile){
            return $this->error403();
        }

        if (isset($datos['state'])){
            $projectprofile->setState($datos['state']);
        };


        $projectprofile->setProfile($profile);
        $projectprofile->setProject($project);
        $em=$this->getDoctrine()->getManager();
        $em ->merge($projectprofile);
        $em->flush();
        return new JsonResponse(
            ["projectprofile" => $projectprofile],
            Response::HTTP_ACCEPTED

        );

    }

    /**
     * @Route(path="/{id}", name="delete", methods={ Request::METHOD_DELETE } )
     * @param Projectprofile|null $projectprofile
     * @return Response
     */
    public function deleteResult(?Projectprofile $projectprofile=null): Response
    {
        // No existe
        if (null === $projectprofile) {
            return $this->error404();
        }

        // Existe -> eliminar y devolver 204
        $em = $this->getDoctrine()->getManager();
        $em->remove($projectprofile);
        $em->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }


    /**
     * @return JsonResponse
     ** @codeCoverageIgnore
     */
    private function error422() : JsonResponse
    {
        $mensaje=[
            'code'=> Response::HTTP_UNPROCESSABLE_ENTITY,
            'mensaje' => 'Unprocessable entity is left out'
        ];
        return new JsonResponse(
            $mensaje,
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }


    /**
     * @return JsonResponse
     ** @codeCoverageIgnore
     */
    private function error400() : JsonResponse
    {
        $mensaje=[
            'code'=> Response::HTTP_BAD_REQUEST,
            'mensaje' => 'Bad Request Register do not exists'
        ];
        return new JsonResponse(
            $mensaje,
            Response::HTTP_BAD_REQUEST
        );
    }


    /**
     * @return JsonResponse
     * @codeCoverageIgnore
     */
    private function error403() : JsonResponse
    {
        $mensaje = [
            'code' => Response::HTTP_FORBIDDEN,
            'message' => 'User Id is Wrong',
        ];
        return new JsonResponse(
            $mensaje, Response::HTTP_FORBIDDEN
        );
    }


    /**
     * Genera una respuesta 404
     * @return JsonResponse
     * @codeCoverageIgnore
     */
    private function error404() : JsonResponse
    {
        $mensaje=[
            'code'=> Response::HTTP_NOT_FOUND,
            'mensaje' => 'Not found resource not found'
        ];
        return new JsonResponse(
            $mensaje,
            Response::HTTP_NOT_FOUND
        );
    }
    /**
     * Genera una respuesta 409 - Duplicated 409
     * @return JsonResponse
     * @codeCoverageIgnore
     */
    private function error409(): JsonResponse
    {

        $mensaje = [
            'code' => Response::HTTP_CONFLICT,
            'message' => 'Duplicated project profile',
        ];
        return new JsonResponse(
            $mensaje, Response::HTTP_CONFLICT
        );
    }


}
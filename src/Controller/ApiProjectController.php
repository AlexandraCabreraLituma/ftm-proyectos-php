<?php
/**
 * Created by PhpStorm.
 * User: Ale
 * Date: 13/5/2019
 * Time: 12:36
 */

namespace App\Controller;

use App\Entity\User;
use App\Entity\Project;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiProjectController
 * @package App\Controller
 * @Route(path=ApiProjectController::PROJECT_API_PATH, name="api_project_")
 *
 */
class ApiProjectController extends AbstractController
{
    //ruta de la api de project
    const PROJECT_API_PATH='/api/v1/projects';
    const USERS = '/users';
    const ENABLED = '/enabled';
    /**
     * @Route(path="", name="post",methods={Request::METHOD_POST})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function postProject(Request $request):Response{
        $datosPeticion=$request->getContent();
        $datos=json_decode($datosPeticion,true);

        if(empty($datos['title']) || empty($datos['user_id'])|| empty($datos['description'])||empty($datos['key_words']))
        {
            return $this->error422();
        }

        /** @var User $user */
        $user=$this->getDoctrine()->getManager()->getRepository(User::class)->find($datos['user_id']);

        if($user===null){
            return $this->error400();
        }

        $initial_date =new DateTime($datos['initial_date']) ?? new DateTime("now");
        $final_date =new DateTime($datos['final_date']) ?? new DateTime("now");
        /**
         * @var Project project
         */
        $project= new Project($datos['title'],$datos['description'],
                      $datos['key_words'], $initial_date,
                      $final_date,$datos['enabled']??true,$user);
        $em=$this->getDoctrine()->getManager();
        $em ->persist($project);
        $em->flush();
        return new JsonResponse(
            ["project" => $project],
            Response::HTTP_CREATED
        );

    }

    /**
     * @Route(path="", name="getc", methods={ Request::METHOD_GET })
     * @return Response
     */
    public function getCProject():Response{
        $em=$this->getDoctrine()->getManager();
        /** * @var Project[] $projetcs */
        $projetcs =$em-> getRepository(Project::class)->findAll();

        return (empty($projetcs))
            ? $this-> error404()
            : new JsonResponse( ['projects' => $projetcs],Response::HTTP_OK);
    }

    /**
     * @Route(path="/{id}", name="get_project", methods={Request::METHOD_GET})
     * @param Project $project
     * @return JsonResponse
     */
    public function getProjectUnique(?Project $project = null): JsonResponse
    {
        return (empty($project))
            ? $this->error404()
            : new JsonResponse(['project' => $project], Response::HTTP_OK);
    }

    /**
     * @Route(path="/enabled/{enabled}", name="get_project_enabled", methods={Request::METHOD_GET})
     * @return JsonResponse
     */
    public function getProjectEnabled($enabled): JsonResponse
    {
        $em=$this->getDoctrine()->getManager();
        /** * @var Project[] $projects */
        $projects =$em-> getRepository(Project::class)->findBy(['enabled' =>$enabled]);
        return (empty($projects))
            ? $this-> error404()
            : new JsonResponse( ['projects' => $projects],Response::HTTP_OK);
    }


    /**
     * @Route(path="/users/{user_id}", name="getc_project_user", methods={ Request::METHOD_GET })
     * @return Response
     */
    public function getCProjectUser($user_id):Response{
        $em=$this->getDoctrine()->getManager();
        /** @var User $user */
        $user=$this->getDoctrine()->getManager()->getRepository(User::class)->find($user_id);

        if($user===null){
            return $this->error400();
        }
        /** * @var Project[] $projetcs */
        $projetcs = $em->getRepository(Project::class)->findBy(['user' =>$user]);
        return (empty($projetcs))
            ? $this-> error404()
            : new JsonResponse( ['projects' => $projetcs]
                ,Response::HTTP_OK);
    }
    /**
     * @Route(path="/users/enabled/{user_id}", name="getc_project_user_enabled", methods={ Request::METHOD_GET })
     * @return Response
     */
    public function getCProjectUserEnabled($user_id):Response{
        $em=$this->getDoctrine()->getManager();
        /** @var User $user */
        $user=$this->getDoctrine()->getManager()->getRepository(User::class)->find($user_id);
        if($user===null){
            return $this->error400();
        }
        /** * @var Project[] $projetcs */
        $projetcs = $em->getRepository(Project::class)-> findBy(array('user' => $user, 'enabled' => true));

        return (empty($projetcs))
            ? $this-> error404()
            : new JsonResponse( ['projects' => $projetcs],Response::HTTP_OK);
    }

    /**
     * @Route(path="/{id}",name="put",methods={Request::METHOD_PUT})
     * @param Project|null $project
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function putProject(?Project $project = null, Request $request):Response{
        $em = $this->getDoctrine()->getManager();
        if (null === $project) {
            return $this->error404();
        }
        $datosPeticion=$request->getContent();
        $datos=json_decode($datosPeticion,true);

        if(empty($datos['title']) || empty($datos['user_id'])|| empty($datos['description'])||empty($datos['key_words']))
        {
            return $this->error422();
        }

        /** @var User $user */
        $user=$em->getRepository(User::class)->find($datos['user_id']);

        if($user===null){
            return $this->error400();
        }
        if (isset($datos['title'])){
            $project->setTitle($datos['title']);
        };
        if (isset($datos['description'])){
            $project->setDescription($datos['description']);
        };
        if (isset($datos['key_words'])){
            $project->setKeyWords($datos['key_words']);
        };

        if (isset($datos['enabled'])){
            $project->setEnabled($datos['enabled']);
        };
        $initial_date =new DateTime($datos['initial_date']) ??new DateTime("now");
        $project->setInitialDate($initial_date);

        $final_date =new DateTime($datos['final_date']) ??new DateTime("now");
        $project->setFinalDate($final_date);

        $project->setUser($user);
        $em=$this->getDoctrine()->getManager();
        $em ->merge($project);
        $em->flush();
        return new JsonResponse(
            ["project" => $project],
            Response::HTTP_ACCEPTED

        );

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
            'mensaje' => 'Bad Request User do not exists'
        ];
        return new JsonResponse(
            $mensaje,
            Response::HTTP_BAD_REQUEST
        );
    }
    /**
     * @return JsonResponse
     ** @codeCoverageIgnore
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
}
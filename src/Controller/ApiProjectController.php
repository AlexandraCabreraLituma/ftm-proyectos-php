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

    /**
     * @Route(path="", name="post",methods={Request::METHOD_POST})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function postProject(Request $request):Response{
        $datosPeticion=$request->getContent();
        $datos=json_decode($datosPeticion,true);

        if(empty($datos['title']) || empty($datos['user_id'])|| empty($datos['description'])||empty($datos['specific_objectives']))
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
                      $datos['specific_objectives'], $initial_date,
                      $final_date,$user);
        $em=$this->getDoctrine()->getManager();
        $em ->persist($project);
        $em->flush();
        return new JsonResponse(
            ["project" => $project],
            Response::HTTP_CREATED
        );

    }



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
     *
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
}
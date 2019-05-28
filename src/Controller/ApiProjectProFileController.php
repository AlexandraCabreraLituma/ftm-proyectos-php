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

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiProjectProFileController
 * @package App\Controller
 * @Route(path=ApiProjectProFileController::PROJECT_PROFILE_API_PATH, name="api_project_profile")
 *
 */
class ApiProjectProFileController extends AbstractController
{
    //ruta de la api de project profile
    const PROJECT_PROFILE_API_PATH='/api/v1/projectsProfiles';

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
        $userProject= $project->getUser()->getId();
        $userProfile=$profile->getUser()->getId();

        if($userProject!==$userProfile){
                $msg = [
                    'code' => Response::HTTP_BAD_REQUEST,
                    'message' => 'User Id is Wrong',
                ];
                return new JsonResponse(
                    $msg, 400
                );

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
            'mensaje' => 'Bad Request Register do not exists'
        ];
        return new JsonResponse(
            $mensaje,
            Response::HTTP_BAD_REQUEST
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
            'message' => 'DUPLICATED PROJECT PROFILE',
        ];
        return new JsonResponse(
            $mensaje, Response::HTTP_CONFLICT
        );
    }
}
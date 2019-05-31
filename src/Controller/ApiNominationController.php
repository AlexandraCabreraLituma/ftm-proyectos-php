<?php
/**
 * Created by PhpStorm.
 * User: Ale
 * Date: 31/5/2019
 * Time: 16:34
 */

namespace App\Controller;


use App\Entity\Nomination;
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
 * Class ApiNominationController
 * @package App\Controller
 * @Route(path=ApiNominationController::NOMINATION_API_PATH, name="api_nomination_")
 *
 */
class ApiNominationController extends AbstractController
{
    //ruta de la api de nominations
    const NOMINATION_API_PATH='/api/v1/nominations';

    /**
     * @Route(path="", name="post",methods={Request::METHOD_POST})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function postNominations(Request $request):Response{
        $datosPeticion=$request->getContent();
        $datos=json_decode($datosPeticion,true);

        $em = $this->getDoctrine()->getManager();
        if(empty($datos['project_profile_id']) || empty($datos['user_id']))
        {
            return $this->error422();
        }
        /** @var Projectprofile $project_profile */
        $project_profile=$em->getRepository(Projectprofile::class)->find($datos['project_profile_id']);

        /** @var User $user */
        $user=$em->getRepository(User::class)->find($datos['user_id']);

        if($project_profile===null || $user===null){
            return $this->error400();
        }

        /** @var Nomination $isNomination */
        $isNomination = $em->getRepository(Nomination::class)->findOneBy(array('projectProfile' => $project_profile, 'user' => $user));

        if($isNomination!==null){
            return $this->error409();
        }

        /** @var Nomination $nomination */
        $nomination= new Nomination($project_profile, $user,$datos['state']?? Nomination::NOMINATION_POSTULATED);
        $em=$this->getDoctrine()->getManager();
        $em ->persist($nomination);
        $em->flush();
        return new JsonResponse(
            ["nomination" => $nomination],
            Response::HTTP_CREATED
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
            'message' => 'Duplicated nomination',
        ];
        return new JsonResponse(
            $mensaje, Response::HTTP_CONFLICT
        );
    }

    /**
     * @Route(path="/{id}", name="options_nomination", methods={ Request::METHOD_OPTIONS })
     * @param Nomination|null $nomination
     * @return Response
     */
    public function optionsNomination(?Nomination $nomination = null):Response{

        if (null === $nomination) {
            return $this->error404();
        }
        $options="POST,PATCH,GET,PUT,DELETE,OPTIONS";
        return new JsonResponse(null,Response::HTTP_OK ,["Allow" => $options]);
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
            'mensaje' => 'Bad Request'
        ];
        return new JsonResponse(
            $mensaje,
            Response::HTTP_BAD_REQUEST
        );
    }

}
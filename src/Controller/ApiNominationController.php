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
    const USERS = '/users';
    const PROJECTSPROFILES = '/projectsProfiles';

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
     * @Route(path="/{id}", name="get_nomination", methods={Request::METHOD_GET})
     * @param Nomination $nomination
     * @return JsonResponse
     */
    public function getNominationUnique(?Nomination $nomination = null): JsonResponse
    {
        return (empty($nomination))
            ? $this->error404()
            : new JsonResponse(['nomination' => $nomination],
                Response::HTTP_OK);
    }


    /**
     * @Route(path="/users/{user_id}", name="getc_nomination_user", methods={ Request::METHOD_GET })
     * @return Response
     */
    public function getCNominationUser($user_id):Response{
        $em=$this->getDoctrine()->getManager();
        /** @var User $user */
        $user=$this->getDoctrine()->getManager()->getRepository(User::class)->find($user_id);

        if($user===null){
            return $this->error400();
        }
        /** * @var Nomination[] $nominations */
        $nominations = $em->getRepository(Nomination::class)->findBy(['user' =>$user]);
        return (empty($nominations))
            ? $this-> error404()
            : new JsonResponse( ['nominations' => $nominations]
                ,Response::HTTP_OK);
    }
    /**
     * @Route(path="/projectsProfiles/{id}", name="getc_nomination_project_profile", methods={ Request::METHOD_GET })
     * @return Response
     */
    public function getCNominationByProjectsProfile($id):Response{
        $em=$this->getDoctrine()->getManager();

        /** @var Projectprofile $project_profile */
        $project_profile=$em->getRepository(Projectprofile::class)->find($id);

        if($project_profile===null){
            return $this->error400();
        }
        /** * @var Nomination[] $nominations */
        $nominations = $em->getRepository(Nomination::class)->findBy(['projectProfile' =>$project_profile]);

       return (empty($nominations))
            ? $this-> error404()
            : new JsonResponse( ['nominations' => $nominations]
                ,Response::HTTP_OK);
    }

    /**
     * @Route(path="/users/{user_id}/projectsProfiles/{id}", name="getc_nomination_user_project_profile", methods={ Request::METHOD_GET })
     * @return Response
     * * @codeCoverageIgnore
     */
    public function getCNominationByUserProjectsProfile($user_id, $id):Response{
        $em=$this->getDoctrine()->getManager();
        /** @var User $user */
        $user=$this->getDoctrine()->getManager()->getRepository(User::class)->find($user_id);
        if($user===null){
            return $this->error400();
        }
        /** @var Projectprofile $project_profile */
        $project_profile=$em->getRepository(Projectprofile::class)->find($id);
        if($project_profile===null){
            return $this->error400();
        }
        /** * @var Nomination[] $nominations */
        $nominations = $em->getRepository(Nomination::class)->findBy(array('projectProfile'=>$project_profile, 'user'=>$user));
        return (empty($nominations))
            ? $this-> error404()
            : new JsonResponse( ['nominations' => $nominations]
                ,Response::HTTP_OK);
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
     * @Route(path="/{id}",name="put",methods={Request::METHOD_PUT})
     * @param Nomination|null $nomination
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function putNomination(?Nomination $nomination = null, Request $request):Response{
        $em = $this->getDoctrine()->getManager();
        if (null === $nomination) {
            return $this->error404();
        }
        $datosPeticion=$request->getContent();
        $datos=json_decode($datosPeticion,true);

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

        if (isset($datos['state'])){
            $nomination->setState($datos['state']);
        };


        $nomination->setUser($user);
        $nomination->setProjectProfile($project_profile);
        $em=$this->getDoctrine()->getManager();
        $em ->merge($nomination);
        $em->flush();
        return new JsonResponse(
            ["nomination" => $nomination],
            Response::HTTP_ACCEPTED

        );

    }

    /**
     * @Route(path="/{id}", name="delete", methods={ Request::METHOD_DELETE } )
     * @param Nomination|null $nomination
     * @return Response
     */
    public function deleteNomination(?Nomination $nomination=null): Response
    {
        // No existe
        if (null === $nomination) {
            return $this->error404();
        }

        // Existe -> eliminar y devolver 204
        $em = $this->getDoctrine()->getManager();
        $em->remove($nomination);
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
            'mensaje' => 'Bad Request'
        ];
        return new JsonResponse(
            $mensaje,
            Response::HTTP_BAD_REQUEST
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

}
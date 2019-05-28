<?php
/**
 * Created by PhpStorm.
 * User: Ale
 * Date: 9/5/2019
 * Time: 11:19
 */

namespace App\Controller;

use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiUserController
 * @package App\Controller
 * @Route(path=ApiUserController::USER_API_PATH, name="api_user_")
 *
 */
class ApiUserController extends AbstractController
{
    //ruta de la api de user
    const USER_API_PATH='/api/v1/users';
    const LOGIN = '/login';
    const USERNAME = '/username';
    const EMAIL = '/email';
    const ORCID = '/orcid';


    /**
     * @Route(path="", name="post",methods={Request::METHOD_POST})
     * @param Request $request
     * @return Response
     */
    public function postUsers(Request $request):Response{
        $datosPeticion=$request->getContent();
        $datos=json_decode($datosPeticion,true);
        if(empty($datos['username']) || empty($datos['password']) || empty($datos['orcid']) || empty($datos['firstname'])|| empty($datos['lastname']) || empty($datos['phone']) || empty($datos['address']) )
        {   return $this->error422();}
        if ($this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(['username' =>$datos['username']])||
            $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(['email' =>$datos['email']])||
            $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(['orcid' =>$datos['orcid']])
            )
        {
            return $this->error409();
        }
        /*** @var User user */
        $user= new User($datos['username'],$datos['password'], $datos['email'],
                        $datos['orcid'],$datos['firstname'], $datos['lastname'],
                        $datos['phone'],$datos['address']);
        $em=$this->getDoctrine()->getManager();
        $em ->persist($user);
        $em->flush();
        return new JsonResponse(
            ["user" => $user],
            Response::HTTP_CREATED
        );
    }

    /**
     * @Route(path="", name="getc", methods={ Request::METHOD_GET })
     * @return Response
     */
    public function getCUser():Response{
        $em=$this->getDoctrine()->getManager();
        /** * @var User[] $users */
        $users =$em-> getRepository(User::class)->findAll();
        return (null=== $users)
            ? $this-> error404()
            : new JsonResponse( ['users' => $users],Response::HTTP_OK);
    }
    /**
     * @Route(path="/{id}", name="get_user", methods={Request::METHOD_GET})
     * @param User $user
     * @return JsonResponse
     */
    public function getUserUnique(?User $user = null): JsonResponse
    {
        return (null == $user)
            ? $this->error404()
            : new JsonResponse(['user' => $user], Response::HTTP_OK);
    }

    /**
     * @param $username
     * @return Response
     * @Route(path="/username/{username}", name="get_user_username", methods={"GET"})
     */
    public function getUserName($username): Response{
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(['username' =>$username]);
        return (null=== $user)
            ? $this->error404()
            : new JsonResponse(['user'=> $user],
                Response::HTTP_OK);
    }


    /**
     * @param $email
     * @return Response
     * @Route(path="/email/{email}", name="get_user_email", methods={"GET"})
     */
    public function getUserEmail($email): Response{
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(['email' =>$email]);
        return (null=== $user)
            ? $this->error404()
            : new JsonResponse(['user'=> $user],
                Response::HTTP_OK);
    }

    /**
     * @param $orcid
     * @return Response
     * @Route(path="/orcid/{orcid}", name="get_user_orcid", methods={"GET"})
     */
    public function getUserOrcid($orcid): Response{
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(['orcid' =>$orcid]);
        return (null=== $user)
            ? $this->error404()
            : new JsonResponse(['user'=> $user],
                Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(path="/login", name="login", methods={"POST"})
     */
    public function login(Request $request): Response{
        $validator=false;
        $dataRequest = $request->getContent();
        $data = json_decode($dataRequest, true);
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['username'=> $data['username']]);
        if($user!==null){
            $validator= $user->validatePassword($data['password']);
        }
        return ($validator === false)
            ? $this->error404()
            : new JsonResponse(
                ['userid'=>$user->getId()],
                Response::HTTP_OK);
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
     * Genera una respuesta 409 - Duplicated 409
     * @return JsonResponse
     * @codeCoverageIgnore
     */
    private function error409(): JsonResponse
    {

        $mensaje = [
            'code' => Response::HTTP_CONFLICT,
            'message' => 'DUPLICATED RESOURCE',
        ];
        return new JsonResponse(
            $mensaje, Response::HTTP_CONFLICT
        );
    }




}
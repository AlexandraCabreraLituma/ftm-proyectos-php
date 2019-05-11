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


    /**
     * @Route(path="", name="post",methods={Request::METHOD_POST})
     * @param Request $request
     * @return Response
     */
    public function postUsers(Request $request):Response{
        $datosPeticion=$request->getContent();
        $datos=json_decode($datosPeticion,true);


        if(empty($datos['username']) || empty($datos['password']) || empty($datos['orcid']) || empty($datos['firstname'])|| empty($datos['lastname']) || empty($datos['phone']) || empty($datos['address']) )
        {
            return $this->error422();
        }

        if ( $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(['username' =>$datos['username']]))
        {
            return $this->error400();

        }
        if ( $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(['email' =>$datos['email']]))
        {

            return $this->error400();

        }
        if ( $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(['orcid' =>$datos['orcid']]))
        {

            return $this->error400();

        }

        /**
         * @var User user
         */
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

    private function error422() : JsonResponse
    {
        $mensaje=[
            'code'=> Response::HTTP_UNPROCESSABLE_ENTITY,
            'mensaje' => 'Unprocessable entity Username, e-mail or password is left out'
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
            'mensaje' => 'Bad Request Username or email already exists'
        ];
        return new JsonResponse(
            $mensaje,
            Response::HTTP_BAD_REQUEST
        );
    }



}
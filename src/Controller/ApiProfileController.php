<?php
/**
 * Created by PhpStorm.
 * User: Ale
 * Date: 23/5/2019
 * Time: 14:17
 */

namespace App\Controller;


use App\Entity\User;
use App\Entity\Profile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiProfileController
 * @package App\Controller
 * @Route(path=ApiProfileController::PROFILE_API_PATH, name="api_profile_")
 *
 */
class ApiProfileController extends AbstractController
{
    //ruta de la api de project
    const PROFILE_API_PATH='/api/v1/profiles';
    const USERS = '/users';

    /**
     * @Route(path="", name="getc", methods={ Request::METHOD_GET })
     * @return Response
     */
    public function getCProfile():Response{
        $em=$this->getDoctrine()->getManager();
        /** * @var Profile[] $profiles */
        $profiles =$em-> getRepository(Profile::class)->findAll();

        return (null=== $profiles)
            ? $this-> error404()
            : new JsonResponse( ['profiles' => $profiles],Response::HTTP_OK);
    }

    /**
     * @Route(path="/{id}", name="get_project", methods={Request::METHOD_GET})
     * @param Profile $profile
     * @return JsonResponse
     */
    public function getProjectUnique(?Profile $profile = null): JsonResponse
    {
        return (null == $profile)
            ? $this->error404()
            : new JsonResponse(['profile' => $profile], Response::HTTP_OK);
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

        /** * @var Profile[] $profiles */
        $profiles = $em->getRepository(Profile::class)->findBy(['user' =>$user]);

        return (empty($profiles))
            ? $this-> error404()
            : new JsonResponse( ['profiles' => $profiles]
                ,Response::HTTP_OK);
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
    /**
     * @return JsonResponse
     *
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
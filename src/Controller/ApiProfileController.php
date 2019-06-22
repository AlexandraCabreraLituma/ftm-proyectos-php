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
    const SEARCH = '/search';
    /**
     * @Route(path="", name="post",methods={Request::METHOD_POST})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function postProfile(Request $request):Response{
        $datosPeticion=$request->getContent();
        $datos=json_decode($datosPeticion,true);
        if(empty($datos['name']) || empty($datos['user_id'])|| empty($datos['description'])||empty($datos['working_day'])||empty($datos['nivel']))
        {
            return $this->error422();
        }
        /** @var User $user */
        $user=$this->getDoctrine()->getManager()->getRepository(User::class)->find($datos['user_id']);
        if($user===null){
            return $this->error400();
        }
        /*** @var Profile profile         */
        $profile= new Profile($datos['name'],$datos['description'],
                         $datos['working_day'],$datos['nivel'],$user);
        $em=$this->getDoctrine()->getManager();
        $em ->persist($profile);
        $em->flush();
        return new JsonResponse(
            ["profile" => $profile],
            Response::HTTP_CREATED
        );
    }

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
     * @Route(path="/{id}", name="get_profile", methods={Request::METHOD_GET})
     * @param Profile $profile
     * @return JsonResponse
     */
    public function getProfileUnique(?Profile $profile = null): JsonResponse
    {
        return (null == $profile)
            ? $this->error404()
            : new JsonResponse(['profile' => $profile], Response::HTTP_OK);
    }

    /**
     * @Route(path="/users/{user_id}", name="getc_profile_user", methods={ Request::METHOD_GET })
     * @return Response
     */
    public function getCProfileUser($user_id):Response{
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
     * @param Request $request
     * @return Response
     * @Route(path="/search", name="search", methods={"POST"})
     */
    public function searchProfile(Request $request): Response{
        $em = $this->getDoctrine()->getManager();
        $dataRequest = $request->getContent();
        $data = json_decode($dataRequest, true);

        $query = $em->createQuery('SELECT p FROM App\Entity\Profile p INNER JOIN App\Entity\User u where p.user=u and u.id=?1 and p.name LIKE :name and p.nivel LIKE :level and p.workingDay LIKE :workingDay ');
        $query->setParameter('1', $data['user_id']??true);
        $query->setParameter('name','%'.$data['name'].'%');
        $query->setParameter('level', '%'.$data['nivel'].'%');
        $query->setParameter('workingDay', '%'.$data['working_day'].'%');

        /** * @var Profile[] $profiles */
        $profiles = $query->getResult();

        return (empty($profiles))
            ? $this->error404()
            : new JsonResponse(
                ['profiles'=>$profiles],
                Response::HTTP_OK);
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

}
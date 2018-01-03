<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Repository\UserRepository;
use AppBundle\Form\Type\UserType;
use AppBundle\Form\Type\UserTypeForEnable;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class UserController
 * @package AppBundle\Controller
 *
 * @RouteResource("user")
 */
class UsersController extends FOSRestController implements ClassResourceInterface {

    /**
     * Gets an individual User
     *
     * @param int $id
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @ApiDoc(
     *     output="AppBundle\Entity\User",
     *     statusCodes={
     *         200 = "Returned when successful",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function getAction(int $id) {
        $user = $this->getUserRepository()
                ->createFindOneByIdQuery($id)
                ->getSingleResult();

        if ($user === null) {
            return new View(null, Response::HTTP_NOT_FOUND);
        }

        return $user;
    }

    /**
     * Gets a collection of Users
     *
     * @return array
     *
     * @ApiDoc(
     *     output="AppBundle\Entity\User",
     *     statusCodes={
     *         200 = "Returned when successful",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function cgetAction() {
        return $this->getUserRepository()->createFindAllQuery()->getResult();
    }

    /**
     * @param Request $request
     * @return View|\Symfony\Component\Form\Form
     *
     * @ApiDoc(
     *     input="AppBundle\Form\Type\UserType",
     *     output="AppBundle\Entity\User",
     *     statusCodes={
     *         201 = "Returned when a new User has been successful created",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function postAction(Request $request) {
        $form = $this->createForm(UserType::class, null, [
            'csrf_protection' => false,
        ]);


        $form->handleRequest($request);
        if (!$form->isValid()) {
            return $form;
        }

        /**
         * @var $user User
         */
        $user = $form->getData();

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $routeOptions = [
            'id' => $user->getId(),
            '_format' => $request->get('_format'),
        ];

        return $this->routeRedirectView('get_user', $routeOptions, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param int     $id
     * @return View|\Symfony\Component\Form\Form
     *
     * @ApiDoc(
     *     input="AppBundle\Form\Type\UserType",
     *     output="AppBundle\Entity\User",
     *     statusCodes={
     *         204 = "Returned when an existing User has been successful updated",
     *         400 = "Return when errors",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function putAction(Request $request, int $id) {
        /**
         * @var $user User
         */
        $user = $this->getUserRepository()->find($id);

        if ($user === null) {
            return new View(null, Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(UserType::class, $user, [
            'csrf_protection' => false,
        ]);


        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $form;
        }

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $routeOptions = [
            'id' => $user->getId(),
            '_format' => $request->get('_format'),
        ];

        return $this->routeRedirectView('get_user', $routeOptions, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
     * @param int     $id
     * @return View|\Symfony\Component\Form\Form
     *
     * @ApiDoc(
     *     input="AppBundle\Form\Type\UserTypeForEnable",
     *     output="AppBundle\Entity\User",
     *     statusCodes={
     *         204 = "Returned when an existing User has been successful updated",
     *         400 = "Return when errors",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function patchAction(Request $request, int $id) {
        /**
         * @var $user User
         */
        $user = $this->getUserRepository()->find($id);

        if ($user === null) {
            return new View(null, Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(UserTypeForEnable::class, $user, [
            'csrf_protection' => false,
        ]);

        $userData = $request->request->get($form->getName());
        if (!isset($userData['enabled'])) {

            return new View(null, Response::HTTP_BAD_REQUEST);
        }
        $user->setEnabled($userData['enabled']);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $routeOptions = [
            'id' => $user->getId(),
            '_format' => $request->get('_format'),
        ];

        return $this->routeRedirectView('get_user', $routeOptions, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param int $id
     * @return View
     *
     * @ApiDoc(
     *     statusCodes={
     *         204 = "Returned when an existing User has been successful deleted",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function deleteAction(int $id) {
        /**
         * @var $user User
         */
        $user = $this->getUserRepository()->find($id);

        if ($user === null) {
            return new View(null, Response::HTTP_NOT_FOUND);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        return new View(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @return UserRepository
     */
    private function getUserRepository() {
        return $this->getDoctrine()->getRepository(User::class);
    }

}

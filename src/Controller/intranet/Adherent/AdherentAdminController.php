<?php
namespace App\Controller\intranet\Adherent;

use App\Entity\Entreprise;
use App\Entity\Users;
use Knp\Menu\Renderer\TwigRenderer;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Exception\LockException;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Bridge\Twig\AppVariable;
use Symfony\Bridge\Twig\Command\DebugCommand;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyPath;

class AdherentAdminController extends CRUDController
{

    /**
     * Edit action.
     *
     * @param int|string|null $id
     *
     * @throws NotFoundHttpException If the object does not exist
     * @throws \RuntimeException     If no editable field is defined
     * @throws AccessDeniedException If access is not granted
     * @throws \Exception
     *
     * @return RedirectResponse|Response|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id = null)
    {
        $request = $this->getRequest();
        // the key used to lookup the template
        $templateKey = 'edit';

        $id = $request->get($this->admin->getIdParameter());
        $existingObject = $this->admin->getObject($id);
        $beforeUsername = null;

        /*Si il a un compte utilisateur alors on récupère l'username*/
        if($existingObject->getParent() != null){

            $existingObject->getParent()->getUsername() != null ? $beforeUsername = $existingObject->getParent()->getUsername() : null;
        }

        if (!$existingObject) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        $this->checkParentChildAssociation($request, $existingObject);

        $this->admin->checkAccess('edit', $existingObject);

        $preResponse = $this->preEdit($request, $existingObject);
        if (null !== $preResponse) {
            return $preResponse;
        }

        $this->admin->setSubject($existingObject);
        $objectId = $this->admin->getNormalizedIdentifier($existingObject);

        $form = $this->admin->getForm();

        if (!\is_array($fields = $form->all()) || 0 === \count($fields)) {
            throw new \RuntimeException(
                'No editable field defined. Did you forget to implement the "configureFormFields" method?'
            );
        }

        $form->setData($existingObject);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                $submittedObject = $form->getData();
                $this->admin->setSubject($submittedObject);
                $error = false;

                /*Si il n'y a pas d'erreur alors on met à jour l'utilisateur*/
                if (!$error) {

                    try {
                        $existingObject = $this->admin->update($submittedObject);

                        if ($this->isXmlHttpRequest()) {
                            return $this->renderJson([
                                'result' => 'ok',
                                'objectId' => $objectId,
                                'objectName' => $this->escapeHtml($this->admin->toString($existingObject)),
                            ], 200, []);
                        }

                        $this->addFlash(
                            'sonata_flash_success',
                            $this->trans(
                                'flash_edit_success',
                                ['%name%' => $this->escapeHtml($this->admin->toString($existingObject))],
                                'SonataAdminBundle'
                            )
                        );

                        // redirect to edit mode
                        return $this->redirectTo($existingObject);
                    } catch (ModelManagerException $e) {
                        $this->handleModelManagerException($e);

                        $isFormValid = false;
                    } catch (LockException $e) {
                        $this->addFlash('sonata_flash_error', $this->trans('flash_lock_error', [
                            '%name%' => $this->escapeHtml($this->admin->toString($existingObject)),
                            '%link_start%' => '<a href="' . $this->admin->generateObjectUrl('edit', $existingObject) . '">',
                            '%link_end%' => '</a>',
                        ], 'SonataAdminBundle'));
                    }
                }
                else{
                    if (!$this->isXmlHttpRequest()) {
                        $this->addFlash(
                            'sonata_flash_error',
                            $this->trans(
                                'flash_edit_error',
                                ['%name%' => "L'utilisateur ne peut pas etre créer veuillez verifier les informations saisis"],
                                'SonataAdminBundle'
                            )
                        );
                    }
                }

            }

            // show an error message if the form failed validation
            if (!$isFormValid) {
                if (!$this->isXmlHttpRequest()) {
                    $this->addFlash(
                        'sonata_flash_error',
                        $this->trans(
                            'flash_edit_error',
                            ['%name%' => $this->escapeHtml($this->admin->toString($existingObject))],
                            'SonataAdminBundle'
                        )
                    );
                }
            } elseif ($this->isPreviewRequested()) {
                // enable the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
                $this->admin->getShow();
            }
        }

        $formView = $form->createView();
        // set the theme for the current Admin Form
        $this->setFormTheme($formView, $this->admin->getFormTheme());

        // NEXT_MAJOR: Remove this line and use commented line below it instead
        $template = $this->admin->getTemplate($templateKey);
        // $template = $this->templateRegistry->getTemplate($templateKey);

        return $this->renderWithExtraParams($template, [
            'action' => 'edit',
            'form' => $formView,
            'object' => $existingObject,
            'objectId' => $objectId,
        ], null);
    }


    /**
     * Create action.
     *
     * @throws AccessDeniedException If access is not granted
     * @throws \RuntimeException     If no editable field is defined
     * @throws \ReflectionException
     *
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        $request = $this->getRequest();
        // the key used to lookup the template
        $templateKey = 'edit';

        $this->admin->checkAccess('create');

        $class = new \ReflectionClass($this->admin->hasActiveSubClass() ? $this->admin->getActiveSubClass() : $this->admin->getClass());

        if ($class->isAbstract()) {
            return $this->renderWithExtraParams(
                '@SonataAdmin/CRUD/select_subclass.html.twig',
                [
                    'base_template' => $this->getBaseTemplate(),
                    'admin' => $this->admin,
                    'action' => 'create',
                ],
                null
            );
        }

        $newObject = $this->admin->getNewInstance();

        $preResponse = $this->preCreate($request, $newObject);
        if (null !== $preResponse) {
            return $preResponse;
        }

        $this->admin->setSubject($newObject);

        $form = $this->admin->getForm();

        if (!\is_array($fields = $form->all()) || 0 === \count($fields)) {
            throw new \RuntimeException(
                'No editable field defined. Did you forget to implement the "configureFormFields" method?'
            );
        }

        $form->setData($newObject);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                $submittedObject = $form->getData();
                $this->admin->setSubject($submittedObject);
                $this->admin->checkAccess('create', $submittedObject);
                $error = false;

                /*SI aucun utilisateur n'a était trouvée alors on créer son compte*/
                if(!$error) {
                    try {
                        $newObject = $this->admin->create($submittedObject);

                        if ($this->isXmlHttpRequest()) {
                            return $this->renderJson([
                                'result' => 'ok',
                                'objectId' => $this->admin->getNormalizedIdentifier($newObject),
                                'objectName' => $this->escapeHtml($this->admin->toString($newObject)),
                            ], 200, []);
                        }

                        $this->addFlash(
                            'sonata_flash_success',
                            $this->trans(
                                'flash_create_success',
                                ['%name%' => $this->escapeHtml($this->admin->toString($newObject))],
                                'SonataAdminBundle'
                            )
                        );

                        // redirect to edit mode
                        return $this->redirectTo($newObject);
                    } catch (ModelManagerException $e) {
                        $this->handleModelManagerException($e);

                        $isFormValid = false;
                    }
                }
                /*Sinon on affiche un message d'erreur*/
                else{
                    if (!$this->isXmlHttpRequest()) {
                        $this->addFlash(
                            'sonata_flash_error',
                            $this->trans(
                                'flash_create_error',
                                ['%name%"' => "Un utilisateur est connu avec cette identifiant"],
                                'SonataAdminBundle'
                            )
                        );
                    }
                }
            }

            // show an error message if the form failed validation
            if (!$isFormValid) {
                if (!$this->isXmlHttpRequest()) {
                    $this->addFlash(
                        'sonata_flash_error',
                        $this->trans(
                            'flash_create_error',
                            ['%name%' => $this->escapeHtml($this->admin->toString($newObject))],
                            'SonataAdminBundle'
                        )
                    );
                }
            } elseif ($this->isPreviewRequested()) {
                // pick the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
                $this->admin->getShow();
            }
        }

        $formView = $form->createView();
        // set the theme for the current Admin Form
        $this->setFormTheme($formView, $this->admin->getFormTheme());

        // NEXT_MAJOR: Remove this line and use commented line below it instead
        $template = $this->admin->getTemplate($templateKey);
        // $template = $this->templateRegistry->getTemplate($templateKey);

        return $this->renderWithExtraParams($template, [
            'action' => 'create',
            'form' => $formView,
            'object' => $newObject,
            'objectId' => null,
        ], null);
    }



    /**
     * Delete action.
     *
     * @param int|string|null $id
     *
     * @throws NotFoundHttpException If the object does not exist
     * @throws AccessDeniedException If access is not granted
     *
     * @return Response|RedirectResponse
     */
    public function deleteAction($id)
    {
        $request = $this->getRequest();
        $id = $request->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        $this->checkParentChildAssociation($request, $object);

        $this->admin->checkAccess('delete', $object);

        $preResponse = $this->preDelete($request, $object);
        if (null !== $preResponse) {
            return $preResponse;
        }

        if ('DELETE' === $this->getRestMethod()) {
            // check the csrf token
            $this->validateCsrfToken('sonata.delete');

            $objectName = $this->admin->toString($object);

            try {
                $this->admin->delete($object);
                $em =$this->getDoctrine()->getManager();
                $user = $em->getRepository(Users::class)->findOneById($object->getParent()->getId());
                $em->remove($user);
                $em->flush();

                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(['result' => 'ok'], 200, []);
                }

                $this->addFlash(
                    'sonata_flash_success',
                    $this->trans(
                        'flash_delete_success',
                        ['%name%' => $this->escapeHtml($objectName)],
                        'SonataAdminBundle'
                    )
                );
            } catch (ModelManagerException $e) {
                $this->handleModelManagerException($e);

                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(['result' => 'error'], 200, []);
                }

                $this->addFlash(
                    'sonata_flash_error',
                    $this->trans(
                        'flash_delete_error',
                        ['%name%' => $this->escapeHtml($objectName)],
                        'SonataAdminBundle'
                    )
                );
            }

            return $this->redirectTo($object);
        }

        // NEXT_MAJOR: Remove this line and use commented line below it instead
        $template = $this->admin->getTemplate('delete');
        // $template = $this->templateRegistry->getTemplate('delete');

        return $this->renderWithExtraParams($template, [
            'object' => $object,
            'action' => 'delete',
            'csrf_token' => $this->getCsrfToken('sonata.delete'),
        ], null);
    }

    public function createUserAction(\Swift_Mailer $mailer){

        $username = $this->getRequest()->get('username');
        $email = $this->getRequest()->get('email');
        $id_adherent = $this->getRequest()->get('adherent_id');

        $em = $this->getDoctrine()->getManager();
        $adherent = $em->getRepository(Entreprise::class)->findOneBy(array('id' => $id_adherent));

        if($adherent == null)
            return new JsonResponse(['error' => "L'adherent n'a pas était trouvée"],400);

        $user = $em->getRepository(Users::class)->findOneBy(array("username" => $username));

        if($user != null)
            return new JsonResponse(["error" => "Ce numéro d'adhérent est déjà connu"],400);


        $emailFound =  $em->getRepository(Users::class)->findOneBy(array("email" => $email));

        if($emailFound != null)
            return new JsonResponse(["error" => "Cette email existe"], 400);

        $tokenGenerator = $this->get('fos_user.util.token_generator');
        $token = $tokenGenerator->generateToken();
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setEmailCanonical($email);
        $user->setEnabled(0);
        $user->setPlainPassword($username);
        $user->setConfirmationToken($token);
        $user->addRole("ROLE_ADHERENT");
        $userManager->updateUser($user);

        $adherent->setParent($user);
        $em->persist($adherent);
        $em->flush();


        $partialUrl = $this->generateUrl(
            'fos_user_registration_confirm',
            array(
                'token' => $user->getConfirmationToken()
            )
        );

        $url = 'http://' . $this->getRequest()->server->get('HTTP_HOST') . $partialUrl;

        $message = (new \Swift_Message('Inscription'))
            ->setSubject('Bienvenue !')
            ->setFrom("sitla45@hotmail.com")
            ->setTo($email)
            ->setBody(
                $this->renderView(
                    'emails/AdherentCreation.html.twig',
                    array('user' => $adherent, 'url' => $url, 'genPass' => $username)
                ),
                'text/html'
            );

        $mailer->send($message);

        $this->addFlash(
            'sonata_flash_success',
            $this->trans(
                'flash_edit_success',
                ['%name%' => "Le compte de l'utilisateur a était créer"],
                'SonataAdminBundle'
            )
        );

        return new JsonResponse(["success" => "L'utilisateur a bien était créer"]);

    }

    public function enableUserAction($id){
        $object = $this->admin->getObject($id);

        $em = $this->getDoctrine()->getManager();

        $object->getParent()->setEnabled(true);
        $em->persist($object);
        $em->flush();

        $this->addFlash(
            'sonata_flash_success',
            $this->trans(
                'flash_edit_success',
                ['%name%' => "Le compte de l'utilisateur a était activer"],
                'SonataAdminBundle'
            )
        );
        return $this->redirectToRoute("admin_app_entreprise_list");
    }

    public function unableUserAction($id){
        $object = $this->admin->getObject($id);
        $em = $this->getDoctrine()->getManager();

        $object->getParent()->setEnabled(false);
        $em->persist($object);
        $em->flush();
        $this->addFlash(
            'sonata_flash_success',
            $this->trans(
                'flash_edit_success',
                ['%name%' => "Le compte de l'utilisateur a était desactiver"],
                'SonataAdminBundle'
            )
        );
        return $this->redirectToRoute("admin_app_entreprise_list");
    }

    private function checkParentChildAssociation(Request $request, $object): void
    {
        if (!($parentAdmin = $this->admin->getParent())) {
            return;
        }

        // NEXT_MAJOR: remove this check
        if (!$this->admin->getParentAssociationMapping()) {
            return;
        }

        $parentId = $request->get($parentAdmin->getIdParameter());

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $propertyPath = new PropertyPath($this->admin->getParentAssociationMapping());

        if ($parentAdmin->getObject($parentId) !== $propertyAccessor->getValue($object, $propertyPath)) {
            // NEXT_MAJOR: make this exception
            @trigger_error("Accessing a child that isn't connected to a given parent is deprecated since 3.34"
                ." and won't be allowed in 4.0.",
                E_USER_DEPRECATED
            );
        }
    }


    /**
     * Sets the admin form theme to form view. Used for compatibility between Symfony versions.
     */
    private function setFormTheme(FormView $formView, array $theme = null): void
    {
        $twig = $this->get('twig');

        // BC for Symfony < 3.2 where this runtime does not exists
        if (!method_exists(AppVariable::class, 'getToken')) {
            $twig->getExtension(FormExtension::class)->renderer->setTheme($formView, $theme);

            return;
        }

        // BC for Symfony < 3.4 where runtime should be TwigRenderer
        if (!method_exists(DebugCommand::class, 'getLoaderPaths')) {
            $twig->getRuntime(TwigRenderer::class)->setTheme($formView, $theme);

            return;
        }

        $twig->getRuntime(FormRenderer::class)->setTheme($formView, $theme);
    }
}
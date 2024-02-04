<?php
namespace App\Controller\intranet\Articles;


use App\Entity\ArticleContents;
use App\Entity\ArticlePhotos;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Bridge\Twig\AppVariable;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Component\Form\Command\DebugCommand;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyPath;

class ArticleAdminController extends CRUDController
{

    /**
     * Edit action.
     *
     * @param int|string|null $id
     *
     * @throws NotFoundHttpException If the object does not exist
     * @throws \RuntimeException     If no editable field is defined
     * @throws AccessDeniedException If access is not granted
     *
     * @return Response|RedirectResponse
     */
    public function editAction($id = null)
    {
        $request = $this->getRequest();
        // the key used to lookup the template
        $templateKey = 'edit';

        $id = $request->get($this->admin->getIdParameter());
        $existingObject = $this->admin->getObject($id);

        if (!$existingObject) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        $this->checkParentChildAssociation($request, $existingObject);

        $this->admin->checkAccess('edit', $existingObject);

        $preResponse = $this->preEdit($request, $existingObject);
        if (null !== $preResponse) {
            return $preResponse;
        }
        $em = $this->getDoctrine()->getManager();




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
                $articlesContents = $form->get('articles')->getData();
                try {



                    /*On vérifie si le contenu ajoutée est déja ajouté*/
                    foreach ($articlesContents as $a){


                        if($a->getId() == null){
                        /*Si l'article n'a pas était trouvée*/
                        /*On peut procéder à l'ajout des nouveau contenu si ils existent*/
                            $firstImage = $a->getArticlePhotos();
                            /*On ajoute la premiere image au contenu si il existe*/
                            if ($firstImage != null) {
                                $firstImage->setArticle($submittedObject->getArticle());
                                $em->persist($firstImage);
                                $em->flush();

                            }
                            $a->setArticle($submittedObject->getArticle());
                            $a->setArticlePhotos($firstImage);
                            $em->persist($a);
                            $em->flush();


                        }
                        /*Si il existe et on modifie une image alors on supprime l'image precédente*/
                        else{
                            $oldImage = $submittedObject->getArticlePhotos();

                            if($oldImage !=null){
                                $firstImage =  $a->getArticlePhotos();
                                if($firstImage->getPhotoFile() != null){
                                    $firstImage->setArticle($submittedObject->getArticle());
                                    $em->persist($firstImage);
                                    $em->flush();

                                    $a->setArticle($submittedObject->getArticle());
                                    $a->setArticlePhotos($firstImage);
                                    $em->persist($a);
                                    $em->flush();
                                }
                            }
                        }
                    }

                    $existingObject = $this->admin->update($submittedObject);

                    $size = 10;

                    /*Si tout les contenu ont étaient supprimés alors on supprime dans la base tout les contenu de l'article concerné*/
                    if(sizeof($articlesContents) ==0){
                        $allContent = $em->getRepository(ArticleContents::class)->findAll();
                        $size = count($allContent);
                        foreach ($allContent as $a){
                            if($a->getArticle()->getId() == $submittedObject->getArticle()->getId()){
                                $em->remove($a);
                                $em->flush();
                                $size--;
                            }
                        }
                    }

                    /*Si il n'y a plus de contenu alors on le redirige vers la page des contenus*/
                    if($size == 0){
                        return $this->redirectToRoute("admin_app_articlecontents_list");
                    }
                    $contentToNotRemove = [];
                    $contentToRemove = [];
                    $allContent = $em->getRepository(ArticleContents::class)->findAll();

                    foreach ($articlesContents as $o){


                        foreach ($allContent as $a){
                            $trouv = false;
                            /*On regarde seulement pour les contenu de cette article*/
                            if($a->getArticle()->getId() == $o->getArticle()->getId()){

                                /*Si il ne se trouve pas dans la liste envoyée alors on le supprime*/
                                if($a->getId() != $o->getId()){

                                    /*On l'ajoute dans le contenu à supprimé*/
                                    array_push($contentToRemove, $a->getId());
                                }
                                else{
                                    array_push($contentToNotRemove, $a->getId());
                                    $existingObject = $a;
                                }
                            }
                        }

                    }
                    $diff = array_diff($contentToRemove, $contentToNotRemove);

                    $foundKey = null;
                    foreach ($allContent as $a){
                        $foundKey = array_search($a->getId(), $diff);

                        if($foundKey !== false){

                            if($a->getArticlePhotos() != null){
                                $em->remove($a->getArticlePhotos());
                                $em->flush();
                            }
                            $em->remove($a);
                            $em->flush();
                        }
                    }




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
                        '%link_start%' => '<a href="'.$this->admin->generateObjectUrl('edit', $existingObject).'">',
                        '%link_end%' => '</a>',
                    ], 'SonataAdminBundle'));
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
                $articlesContents = $form->get('articles')->getData();
                $this->admin->setSubject($submittedObject);
                $this->admin->checkAccess('create', $submittedObject);
                try {

                    $em = $this->getDoctrine()->getManager();
                    $firstImage = $articlesContents[1]->getArticlePhotos();
                    /*On ajoute la premiere image au contenu si il existe*/
                    if ($firstImage != null) {
                            $firstImage->setArticle($submittedObject->getArticle());
                            $em->persist($firstImage);
                            $em->flush();

                    }

                    /*On ajoute le premier contenu si il existe*/
                    if(sizeof($articlesContents) > 0){
                        $firstContent = $articlesContents[1];

                        if($firstContent != null) {
                            $submittedObject->setContent($firstContent->getContent());
                            $submittedObject->setArticlePhotos($firstContent->getArticlePhotos());
                        }


                    }


                    $newObject = $this->admin->create($submittedObject);
                    /*On parcours la liste des contenu ajoutée*/
                    for($i =2; $i<=sizeof($articlesContents);$i++){


                        $photos = $articlesContents[$i]->getArticlePhotos();

                        if($photos != null) {
                            $photos->setArticle($submittedObject->getArticle());
                            $em->persist($photos);
                            $em->flush();
                        }

                        $articlesContents[$i]->setArticle($submittedObject->getArticle());

                        if($photos != null)
                            $articlesContents[$i]->setArticlePhotos($articlesContents[$i]->getArticlePhotos());
                        $em->persist($articlesContents[$i]);
                        $em->flush();




                    }

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
                $em = $this->getDoctrine()->getManager();
                $contentToRemove = $em->getRepository(ArticleContents::class)->findBy(array("article" => $object->getArticle()));

                if($contentToRemove != null){
                    foreach ($contentToRemove as $c){
                        $em->remove($c);
                        $em->flush();
                    }
                }

                $this->admin->delete($object);

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


    /**
     * Sets the admin form theme to form view. Used for compatibility between Symfony versions.
     * @param FormView $formView
     * @param array|null $theme
     */
    private function setFormTheme(FormView $formView, array $theme = null): void
    {
        $twig = $this->get('twig');

        // BC for Symfony < 3.2 where this runtime does not exists
        if (!method_exists(AppVariable::class, 'getToken')) {
            $twig->getExtension(FormExtension::class)->renderer->setTheme($formView, $theme);

            return;
        }

        $twig->getRuntime(FormRenderer::class)->setTheme($formView, $theme);
    }
}
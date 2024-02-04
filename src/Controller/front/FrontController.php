<?php

namespace App\Controller\front;
use App\Entity\ArticleContents;
use App\Entity\Articles;
use App\Entity\CategoryArticle;
use App\Form\ContactType;
use App\Form\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class FrontController extends AbstractController
{
    /**
     * Fonction permettant de récupérer la page d'accueil
     * @Route("/", name="front_homepage")
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @return Response
     */
    public function homepage(Request $request, \Swift_Mailer  $mailer){
        $user = null;

        $this->getUser() != null ? $user = $this->getUser() :null;

        $category = $this->getDoctrine()->getRepository(CategoryArticle::class)->findAll();

        $contactForm = $this->createForm(ContactType::class);

        $contactForm->handleRequest($request);

        if($contactForm->isSubmitted()){
            $contact = $contactForm->getData();
            $message = (new \Swift_Message("Contact"))
                ->setFrom("sitla45@hotmail.com")
                ->setTo("sitla45@hotmail.com")
                ->setBody(
                    $this->renderView(
                        'emails/contact.html.twig',
                        ["contact" => $contact]

                    ),
                    'text/html'
                );

            $mailer->send($message);

            $this->addFlash("success", "Votre message a bien était envoyer");
        }

        return $this->render('front/homepage.html.twig', array(
            "user" =>$user,
            "category" => $category,
            "contactForm" => $contactForm->createView()
        ));
    }

    /**
     * Fonction permettant de faire une demande d'adhésion
     * @Route("/register", name="front_register")
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @return Response
     */
    public function register(Request $request, \Swift_Mailer $mailer){

        $user = null;

        $this->getUser() != null ? $user = $this->getUser() :null;

        $form = $this->createForm(RegisterType::class);

        $form->handleRequest($request);

        /*Si le formulaire est envoyé et valide*/
        if($form->isSubmitted() && $form->isValid()){
            /*On récupère les données envoyées*/
            $data = $form->getData();

            /*On prepare le message a etre envoyée*/
            $message = (new \Swift_Message("Demande d'adhésion"))
                ->setFrom("sitla45@hotmail.com")
                ->setTo("sitla45@hotmail.com")
                ->setBody(
                    $this->renderView(
                        'emails/registration.html.twig',
                        ['user' => $data]
                    ),
                    'text/html'
                );
            /*On envoie le message*/
            $mailer->send($message);

            $message = (new \Swift_Message("Accusé reception de votre demande d'adhesion"))
                ->setFrom("sitla45@hotmail.com")
                ->setTo($data["email"])
                ->setBody(
                    $this->renderView(
                        'emails/Accuseregistration.html.twig'

                    ),
                    'text/html'
                );

            /*On envoie le message*/
            $mailer->send($message);
            $this->addFlash('success', "Votre demande a bien était envoyé");
        }
        else{
            $this->addFlash('error', "Une erreur est survenue");
        }

        return $this->render('front/register.html.twig', array(
            'registerForm' => $form->createView(),
            'user' => $user
        ));
    }

    /**
     * Fonction permettant de récupérer la page pour les membre
     * @Route("/sitla45/membres/{slug}", name="front_member")
     * @param Request $request
     * @return Response
     */
    public function member(Request $request, $slug, $page=1){

        $article_count = 0;
        $maxArticle = 9;
        $category_find = null;
        if($slug == "Accueil"){

            if($this->container->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')){
                $articles = $this->getDoctrine()->getRepository(Articles::class)->findAll();
                $article_count = count($articles);
            }
            else {
                $articles = $this->getDoctrine()->getRepository(Articles::class)->findBy(array("public" => true));
                $article_count = count($articles);
            }

            if ($request->get('page')) { $page = $request->get('page'); }
            $pagination  = $this->getPaginator($article_count, $page, $request, $maxArticle, 'front_member', $slug);

            if($this->container->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')){
                $articles = $this->getDoctrine()->getRepository(Articles::class)->findArticleByPage($page, $maxArticle, null, false);
            }
            else{
                $articles = $this->getDoctrine()->getRepository(Articles::class)->findArticleByPage($page, $maxArticle, null, true);

            }

        }
        else {
            $category_find = $this->getDoctrine()->getRepository(CategoryArticle::class)->findOneBy(array("slug" => $slug));

            if($this->container->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')){
                $articles = $this->getDoctrine()->getRepository(Articles::class)->findBy(array("category" => $category_find->getId()));
                $article_count = count($articles);

            }
            else{
                $articles = $this->getDoctrine()->getRepository(Articles::class)->findBy(array("category" => $category_find->getId(), "public" => true));
                $article_count = count($articles);

            }

            if ($request->get('page')) { $page = $request->get('page'); }
            $pagination  = $this->getPaginator($article_count, $page, $request, $maxArticle, 'front_member', $slug);

            if($this->container->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')){
                $articles = $this->getDoctrine()->getRepository(Articles::class)->findArticleByPage($page, $maxArticle, $category_find->getId(), false);
            }
            else{
                $articles = $this->getDoctrine()->getRepository(Articles::class)->findArticleByPage($page, $maxArticle, $category_find->getId(), true);
            }

        }


        $category = $this->getDoctrine()->getRepository(CategoryArticle::class)->findAll();
        return $this->render('front/blog.html.twig', array(
            "category" => $category,
            "category_find" => $category_find,
            "articles" => $articles,
            "pagination" => $pagination,
        ));

    }


    protected function getPaginator($agency_count, $page, $request, $maxArticle = 9, $route, $slug)
    {
        $pagination = array(
            'page' => $page,
            'route' => $route,
            'pages_count' => ceil($agency_count / $maxArticle),
            'route_params' => array("slug" => $slug)
        );

        return $pagination;
    }


    /**
     * Fonction permettant de récupérer l'article choisis
     * @Route("/sitla45/articles/{slug}", name="front_article_show")
     * @param Request $request
     * @param $slug
     * @return Response
     */
    public function showArticle(Request $request, $slug){

        /*On essaie de récupérer l'article*/
        $article =$this->getDoctrine()->getRepository(Articles::class)->findOneBy(array("slug" => $slug));

        /*Si on ne le trouve pas on l'envoie à l'accueil*/
        if($article == null)
            return $this->redirectToRoute("front_member", array("slug" => "Accueil"));
        $category = $this->getDoctrine()->getRepository(CategoryArticle::class)->findAll();

        $content = $this->getDoctrine()->getRepository(ArticleContents::class)->findBy(array('article' => $article->getId()));
        return $this->render('front/post.html.twig', array(
            "article" => $article,
            "category" => $category,
            "content" => $content,
        ));
    }


}
<?php
namespace App\Controller\intranet\Listeners;

use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener extends AbstractController
{
    protected $userManager;
    protected $auth_checker;
    protected $redirect;
    protected $router;

    public function __construct(UserManagerInterface $userManager, RouterInterface $router){
        $this->userManager = $userManager;
        $this->router = $router;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        /*Si l'utilisatteur est un membre du syndicat ou le responsable alors on le redirige sur l'intranet*/
        if($this->auth_checker->isGranted('ROLE_SYNDICAT') || $this->auth_checker->isGranted('ROLE_SUPER_ADMIN')){
          $this->redirect = $this->router->generate("sonata_admin_dashboard");
        }
        else{
            $this->redirect = $this->router->generate("front_member", array("slug" => "Accueil"));

        }


    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (null !== $this->redirect) {
            $event->setResponse(new RedirectResponse($this->redirect));
        }
    }

    public function setAuthChecker($auth_checker)
    {
        $this->auth_checker = $auth_checker;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Arnaud
 * Date: 08/04/2019
 * Time: 10:22
 */

namespace App\Controller\intranet\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;


class MenuBuilder
{
    private $factory;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function createMainMenu(array $options)
    {
        $menu = $this->factory->createItem('root')->setChildrenAttributes(array('class'=>'sidebar-menu'));;


        #Si l'utilisateur est un super admin on créer son Menu
        if($this->auth_checker->isGranted('ROLE_SUPER_ADMIN')){
            $menu = $this->createSuperAdminMenu($menu);
        }
        #Si l'utilisateur est l'admin de l'agence on créer son Menu et n'est pas le super admin
        if($this->auth_checker->isGranted('ROLE_SYNDICAT') && !$this->auth_checker->isGranted('ROLE_SUPER_ADMIN')){
            $menu = $this->createAdminSyndicatMenu($menu);
        }

        return $menu;
    }

    #Fonction : Création du Menu super admin
    protected function createSuperAdminMenu($menu){

        $menu
            ->addChild("Don", array("route" => "admin_app_don_list"))
            ->setLabel("Don")
        ;

        $menu
            ->addChild("Adhérent", array("route" => "admin_app_entreprise_list"))
            ->setLabel("Adherent")
        ;

        $menu
            ->addChild("Paiements", array("route" => "admin_app_paiement_list"))
            ->setLabel("Paiements")
        ;

        $menu
            ->addChild("Timbre", array("route" => "admin_app_timbre_list"))
            ->setLabel("Timbre")
        ;

        $menu
            ->addChild("Category", array("route" => "admin_app_categoryarticle_list"))
            ->setLabel("Catégorie article")
        ;
        $menu
            ->addChild("Articles", array("route" => "admin_app_articles_list"))
            ->setLabel("Les articles")
        ;
        $menu
            ->addChild("Contents", array("route" => "admin_app_articlecontents_list"))
            ->setLabel("Contenu des articles")
        ;

        $menu
            ->addChild("front", array("route" => "front_member", "routeParameters" => array("slug" => "Accueil")))
            ->setLabel("Lien vers la page des adhérents")
        ;


        return $menu;
    }

    #Fonction : Création du Menu admin agent
    protected function createAdminSyndicatMenu($menu){
        $menu
            ->addChild("Don", array("route" => "admin_app_don_list"))
            ->setLabel("Don")
        ;


        $menu
            ->addChild("Paiements", array("route" => "admin_app_paiement_list"))
            ->setLabel("Paiements")
        ;

        $menu
            ->addChild("Timbre", array("route" => "admin_app_timbre_list"))
            ->setLabel("Timbre")
        ;

        $menu
            ->addChild("Adhérent", array("route" => "admin_app_entreprise_list"))
            ->setLabel("Adherent")
        ;
        return $menu;
    }


    public function setTokenStorage($token_storage)
    {
        $this->token_storage = $token_storage;
    }

    public function setAuthChecker($auth_checker)
    {
        $this->auth_checker = $auth_checker;
    }
}
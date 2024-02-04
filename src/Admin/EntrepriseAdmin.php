<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Users;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

final class EntrepriseAdmin extends AbstractAdmin
{

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('createUser');
        $collection->add('enableUser', "{id}/enableUser");
        $collection->add('unableUser', "{id}/unableUser");
    }


    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('nomEntreprise')
            ->add('nomAdherent')
            ->add('prenomAdherent')
            ->add('dateAdhesion')
            ->add('adresse')
            ->add('tel')
            ->add('parent.email')
            ->add('parent.username')
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('nomEntreprise')
            ->add('nomAdherent')
            ->add('prenomAdherent')
            ->add('dateAdhesion')
            ->add('adresse')
            ->add('tel')
            ->add('parent.email')
            ->add('parent.username')
            ->add('_action', null, [
                'actions' => [
                    ['template' => 'intranet/users/list__actions.html.twig']

                ]
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('nomEntreprise')
            ->add('nomAdherent')
            ->add('prenomAdherent')
            ->add('dateAdhesion')
            ->add('adresse')
            ->add('tel')
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('nomEntreprise')
            ->add('nomAdherent')
            ->add('prenomAdherent')
            ->add('dateAdhesion')
            ->add('adresse')
            ->add('tel')
            ->add('parent.email')
            ->add('parent.username')
        ;
    }


    public function getExportFormats()
    {
        return array_merge(parent::getExportFormats(), array('pdf'));
    }

    public function renderView($view, array $parameters = array())
    {
        return $this->getConfigurationPool()->getContainer()->get('templating')->render($view, $parameters);
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

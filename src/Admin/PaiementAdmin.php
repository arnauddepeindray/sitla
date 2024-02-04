<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;

final class PaiementAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('entreprise')
            ->add('typePaiement')
            ->add('montant')
            ->add('date', DateRangeFilter::class)
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('entreprise')
            ->add('typePaiement')
            ->add('montant')
            ->add('date')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('entreprise')
            ->add('typePaiement')
            ->add('montant')
            ->add('date')
            ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('entreprise')
            ->add('typePaiement')
            ->add('montant')
            ->add('date')
            ;
    }

    public function getExportFields()
    {
        return ['typePaiement', 'entreprise', 'montant', 'date'];
    }

    public function getExportFormats()
    {
        return array_merge(array('csv'), array('pdf'));
    }
}

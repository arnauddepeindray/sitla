<?php

declare(strict_types=1);

namespace App\Admin;

use phpDocumentor\Reflection\Element;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class TimbreAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $choiceOptions = array('janvier' => "janvier", 'fevrier' => "fevrier", 'mars' => "mars", 'avril' => "mars", 'mai' => "mai", 'juin' => "juin", 'juillet' => "juillet", 'aout' => "aout", 'septembre' => "septembre", 'octobre' => "octobre", 'novembre' => "novembre", 'decembre' => "decembre");
        $datagridMapper
            ->add('entreprise')
            ->add('typePaiement')
            ->add('mois',null, array(), ChoiceType::class, array(
                'choices' => $choiceOptions
            ))
            ->add('montant')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('entreprise')
            ->add('typePaiement')
            ->add('mois')
            ->add('montant')
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
            ->add('mois', ChoiceType::class, [
                'choices' => ['janvier' => "janvier", 'fevrier' => "fevrier", 'mars' => "mars", 'avril' => "mars", 'mai' => "mai", 'juin' => "juin", 'juillet' => "juillet", 'aout' => "aout", 'septembre' => "septembre", 'octobre' => "octobre", 'novembre' => "novembre", 'decembre' => "decembre"],

            ])
            ->add('montant')
            ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('entreprise')
            ->add('typePaiement')
            ->add('mois')
            ->add('montant')
            ;
    }

    public function getExportFields()
    {
        return ['typePaiement', 'entreprise', 'montant', 'mois'];
    }

    public function getExportFormats()
    {
        return array_merge(array('csv'), array('pdf'));
    }


}

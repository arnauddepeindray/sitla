<?php

declare(strict_types=1);

namespace App\Admin;

use App\Form\ArticlePhotosType;
use App\Form\ArticlesType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class ArticlesAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('category')
            ->add('title')
            ->add('datePost')
            ->add('public')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('photoFile', 'string', array(
                'template' => 'intranet/articles/list_photo.html.twig'
            ))
            ->add('category')
            ->add('title')
            ->add('excerpt')
            ->add('datePost')
            ->add('public')
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
            ->add('photoFile', VichImageType::class, [
                'required' => false,
            ])
            ->add('category')
            ->add('title')
            ->add('excerpt')
            ->add('datePost')
            ->add('public')
            ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('category')
            ->add('title')
            ->add('excerpt')
            ->add('datePost')
            ->add('public')
            ;
    }
}

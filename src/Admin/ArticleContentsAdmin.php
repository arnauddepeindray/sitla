<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\ArticleContents;
use App\Form\ArticlePhotosType;
use App\Form\ArticlesType;
use Doctrine\ORM\Query;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class ArticleContentsAdmin extends AbstractAdmin
{

    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        $alias = $query->getRootAliases()[0];

        $query->groupBy("$alias.article");
        if(count($query->execute(array())) == 0)
            $query = parent::createQuery($context);

        return $query;

    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('article')
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
        $article_id = $this->getSubject()->getArticle();

        $article_id !=null ? $article_id = $article_id->getId() :null;

        if($article_id != null) {
            $formMapper->getFormBuilder()->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {

                $article_id = $this->getSubject()->getArticle();

                $article_id !=null ? $article_id = $article_id->getId() :null;

                $form = $event->getForm();

                $doctrine = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
                $articleContent = $doctrine->getRepository(ArticleContents::class)->findBy(array('article' => $article_id));

                $array = [];
                foreach ($articleContent as $a){

                    array_push($array, $a);


                }
                $form->get('articles')->setData($array);



            });
        }

        $formMapper
            ->add('article')
            ->add('articles', CollectionType::class, [
                'entry_type' => ArticlesType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'mapped' => false

            ])
            ;


    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ;
    }

    public function createForm($type, $data = null, array $options = array())
    {
        return $this->getConfigurationPool()->getContainer()->get('form.factory')->create($type, $data, $options);
    }

}

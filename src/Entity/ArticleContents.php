<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleContentsRepository")
 */
class ArticleContents
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", length=16777215, nullable=true)
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Articles", inversedBy="articleContents")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $article;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ArticlePhotos", inversedBy="articleContents",cascade={"persist"})
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $articlePhotos;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @param mixed $article
     */
    public function setArticle(Articles $article)
    {
        $this->article = $article;
    }

    /**
     * @return mixed
     */
    public function getArticlePhotos()
    {
        return $this->articlePhotos;
    }

    /**
     * @param mixed $article
     */
    public function setArticlePhotos(ArticlePhotos $article)
    {
        $this->articlePhotos = $article;
    }




}

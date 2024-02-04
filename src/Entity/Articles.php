<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticlesRepository")
 * @Vich\Uploadable
 */
class Articles
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datePost;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ArticlePhotos", mappedBy="article", cascade={"all"}, orphanRemoval=true)
     */
    private $articlePhotos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ArticleContents", mappedBy="article", cascade={"all"}, orphanRemoval=true)
     */
    private $articleContents;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CategoryArticle", inversedBy="article")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     *
     * @Vich\UploadableField(mapping="article_banniere_photo", fileNameProperty="photoName")
     *
     * @var File
     */
    private $photoFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $photoName;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $excerpt;

    /**
     * @Gedmo\Slug(handlers={
     *      @Gedmo\SlugHandler(class="Gedmo\Sluggable\Handler\RelativeSlugHandler", options={
     *          @Gedmo\SlugHandlerOption(name="relationField", value="category"),
     *          @Gedmo\SlugHandlerOption(name="relationSlugField", value="alias"),
     *          @Gedmo\SlugHandlerOption(name="separator", value="-")
     *      })
     * }, separator="-", updatable=true, fields={"title"})
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="boolean")
     */
    private $public = false;




    public function __toString()
    {
        return $this->getTitle();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatePost(): ?\DateTimeInterface
    {
        return $this->datePost;
    }

    public function setDatePost(\DateTimeInterface $datePost): self
    {
        $this->datePost = $datePost;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getArticlePhotos()
    {
        return $this->articlePhotos;
    }


    /**
     * Add Article
     *
     * @param \App\Entity\ArticlePhotos $articlePhotos
     *
     * @return Articles
     */
    public function addArticlePhotos(ArticlePhotos $articlePhotos)
    {
        $this->articlePhotos[] = $articlePhotos;

        return $this;
    }

    /**
     * Remove article
     *
     * @param ArticlePhotos $articlePhotos
     */
    public function removeArticlePhotos(ArticlePhotos $articlePhotos)
    {
        $this->articlePhotos->removeElement($articlePhotos);
    }

    /**
     * @return mixed
     */
    public function getArticleContents()
    {
        return $this->articleContents;
    }


    /**
     * Add ArticleContents
     *
     * @param ArticleContents $articleContents
     * @return Articles
     */
    public function addArticleContents(ArticleContents $articleContents)
    {
        $this->articleContents[] = $articleContents;

        return $this;
    }

    /**
     * Remove article
     *
     * @param ArticleContents $articleContents
     */
    public function removeArticleContents(ArticleContents $articleContents)
    {
        $this->articleContents->removeElement($articleContents);
    }


    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $categoryArticle
     */
    public function setCategory(CategoryArticle $categoryArticle)
    {
        $this->category = $categoryArticle;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }


    /**
     *
     * @param \Symfony\Component\HttpFoundation\File\File|null $photo
     * @return Articles
     * @throws \Exception
     */
    public function setPhotoFile(\Symfony\Component\HttpFoundation\File\File $photo = null)
    {
        $this->photoFile = $photo;

        if ($photo) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return File|null
     */
    public function getPhotoFile()
    {
        return $this->photoFile;
    }

    /**
     * @param string $photoName
     *
     * @return Articles
     */
    public function setPhotoName($photoName)
    {
        $this->photoName = $photoName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhotoName()
    {
        return $this->photoName;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Articles
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function getExcerpt(): ?string
    {
        return $this->excerpt;
    }

    public function setExcerpt(string $excerpt): self
    {
        $this->excerpt = $excerpt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug): void
    {
        $this->slug = $slug;
    }

    public function getPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): self
    {
        $this->public = $public;

        return $this;
    }
}

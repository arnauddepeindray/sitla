<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryArticleRepository")
 * @Vich\Uploadable
 */
class CategoryArticle
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     *
     * @Vich\UploadableField(mapping="category_photo", fileNameProperty="photoName")
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
     * @Gedmo\Slug(handlers={
     *      @Gedmo\SlugHandler(class="Gedmo\Sluggable\Handler\InversedRelativeSlugHandler", options={
     *          @Gedmo\SlugHandlerOption(name="relationClass", value="App\Entity\Articles"),
     *          @Gedmo\SlugHandlerOption(name="mappedBy", value="category"),
     *          @Gedmo\SlugHandlerOption(name="inverseSlugField", value="slug")
     *      })
     * }, fields={"title"})
     * @ORM\Column(length=64, unique=true)
     */
    private $alias;

    /**
     * @Gedmo\Slug(separator="-", updatable=true, fields={"title"})
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Articles", mappedBy="category", cascade={"all"}, orphanRemoval=true)
     */
    private $article;

    public function __toString()
    {
        return $this->getTitle();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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


    /**
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return CategoryArticle
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
     * @return CategoryArticle
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
     * @return CategoryArticle
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

    /**
     * @return mixed
     */
    public function getArticle()
    {
        return $this->article;
    }



    /**
     * Add Article
     *
     * @param \App\Entity\Articles $articles
     *
     * @return CategoryArticle
     */
    public function addArticle(Articles $articles)
    {
        $this->article[] = $articles;

        return $this;
    }

    /**
     * Remove article
     *
     * @param Articles $articles
     */
    public function removeArticlePhotos(Articles $articles)
    {
        $this->article->removeElement($articles);
    }

    /**
     * @return mixed
     */
    public function getAlias()
    {
        return $this->alias;
    }
}

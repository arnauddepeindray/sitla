<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticlePhotosRepository")
 * @Vich\Uploadable
 */
class ArticlePhotos
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @Vich\UploadableField(mapping="article_photo", fileNameProperty="photoName")
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Articles", inversedBy="articlePhotos")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $article;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ArticleContents", mappedBy="article", cascade={"all"}, orphanRemoval=true)
     */
    private $articleContents;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $position;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descriptionImage;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return ArticlePhotos
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
     * @return ArticlePhotos
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
     * @return ArticlePhotos
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
     * @param mixed $article
     */
    public function setArticle(Articles $article)
    {
        $this->article = $article;
    }



    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;

        return $this;
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
     * @return ArticlePhotos
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

    public function getDescriptionImage(): ?string
    {

        if($this->descriptionImage == null)
            return "";
        return $this->descriptionImage;
    }

    public function setDescriptionImage($descriptionImage)
    {

        $this->descriptionImage = $descriptionImage;

        return $this;
    }
}

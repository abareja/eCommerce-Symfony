<?php

namespace App\Entity;

use App\Repository\PageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PageRepository::class)
 */
class Page
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    const GROUP_ORDER = 'order';
    const GROUP_INFORMATION = 'information';

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pageGroup;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPageGroup(): ?string
    {
        return $this->pageGroup;
    }

    public function setPageGroup(string $pageGroup): self
    {
        $this->pageGroup = $pageGroup;

        return $this;
    }

    public function getPageGroupLabel(): ?string
    {
        switch( $this->pageGroup ) {
            case self::GROUP_ORDER: return 'Orders';
            case self::GROUP_INFORMATION: return 'Information';
        }
    }
}

<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Annotation\ApiFilter;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompetitionRepository")
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *      "title": "partial",
 *      "content": "partial",
 *      "peoples.firstName": "partial",
 *      "peoples.lastName": "partial"
 *      }
 *  )
 *
 * @ApiResource(
 *     attributes={"order"={"date": "ASC"}},
*      itemOperations={
 *         "get"={
 *             "normalization_context"={
 *                 "groups"={"get-all-competition-with-people"}
 *             }
 *          },
 *
 *     },
 *     collectionOperations={
 *         "get"
 *     },
 *     denormalizationContext={
 *         "groups"={"post"}
 *     }
 * )
 */
class AllCompetition
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min=10)
     * @Groups({"get", "post", "get-all-competition-with-people"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min=20)
     * @Groups({"get", "post", "get-all-competition-with-people"})
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\DateTime()
     * @Groups({"get", "post", "get-all-competition-with-people"})
     */
    private $date;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Groups({"get", "get-all-competition-with-people"})
     */
    private $fee;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="participation")
     * @Groups({"get", "get-all-competition-with-people"})
     *
     */

    private $peoples;

    public function __construct()
    {
        $this->peoples = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getPeoples(): Collection
    {
        return $this->peoples;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }


    public function getFee()
    {
        return $this->fee;
    }


    public function setFee($fee)
    {
        $this->fee = $fee;
    }

    public function __toString(): string{
        return $this->title;
    }
}

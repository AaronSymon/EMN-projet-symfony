<?php

namespace App\Entity;

use App\Repository\SiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SiteRepository::class)
 */
class Site
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity=Participant::class, mappedBy="siteRatache", orphanRemoval=true)
     */
    private $participants;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="site", orphanRemoval=true)
     */
    private $siteSorties;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->siteSorties = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
            $participant->setSiteRatache($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): self
    {
        if ($this->participants->removeElement($participant)) {
            // set the owning side to null (unless already changed)
            if ($participant->getSiteRatache() === $this) {
                $participant->setSiteRatache(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSiteSorties(): Collection
    {
        return $this->siteSorties;
    }

    public function addSiteSorty(Sortie $siteSorty): self
    {
        if (!$this->siteSorties->contains($siteSorty)) {
            $this->siteSorties[] = $siteSorty;
            $siteSorty->setSite($this);
        }

        return $this;
    }

    public function removeSiteSorty(Sortie $siteSorty): self
    {
        if ($this->siteSorties->removeElement($siteSorty)) {
            // set the owning side to null (unless already changed)
            if ($siteSorty->getSite() === $this) {
                $siteSorty->setSite(null);
            }
        }

        return $this;
    }
    public function __toString()
    {

        return $this->getNom();
    }
}

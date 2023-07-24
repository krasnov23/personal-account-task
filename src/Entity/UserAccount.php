<?php

namespace App\Entity;

use App\Repository\UserAccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserAccountRepository::class)]
class UserAccount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $balance = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'userAccount', targetEntity: Transaction::class, cascade: ['persist','remove'], orphanRemoval: true)]
    private Collection $userTransactions;

    #[ORM\ManyToMany(targetEntity: ServiceInfo::class, inversedBy: 'userAccounts',cascade: ['persist','remove'])]
    private Collection $userServices;

    public function __construct()
    {
        $this->userTransactions = new ArrayCollection();
        $this->userServices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBalance(): ?int
    {
        return $this->balance;
    }

    public function setBalance(int $balance): static
    {
        $this->balance = $balance;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getUserTransactions(): Collection
    {
        return $this->userTransactions;
    }

    public function addUserTransaction(Transaction $userTransaction): static
    {
        if (!$this->userTransactions->contains($userTransaction)) {
            $this->userTransactions->add($userTransaction);
            $userTransaction->setUserAccount($this);
        }

        return $this;
    }

    public function removeUserTransaction(Transaction $userTransaction): static
    {
        if ($this->userTransactions->removeElement($userTransaction)) {
            // set the owning side to null (unless already changed)
            if ($userTransaction->getUserAccount() === $this) {
                $userTransaction->setUserAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ServiceInfo>
     */
    public function getUserServices(): Collection
    {
        return $this->userServices;
    }

    public function addUserService(ServiceInfo $userService): static
    {
        if (!$this->userServices->contains($userService)) {
            $this->userServices->add($userService);
        }

        return $this;
    }

    public function removeUserService(ServiceInfo $userService): static
    {
        $this->userServices->removeElement($userService);

        return $this;
    }
}

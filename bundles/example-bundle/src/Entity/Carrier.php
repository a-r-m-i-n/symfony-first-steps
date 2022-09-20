<?php declare(strict_types = 1);
namespace Armin\ExampleBundle\Entity;

use Armin\ExampleBundle\Repository\CarrierRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'carriers')]
#[ORM\Entity(repositoryClass: CarrierRepository::class)]
class Carrier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    public string $name;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}


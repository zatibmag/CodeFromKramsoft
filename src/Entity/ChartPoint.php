<?php

namespace App\Entity;

use App\Repository\ChartPointRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChartPointRepository::class)
 */
class ChartPoint extends AbstractChartPoint
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected ?int $id = null;

    /**
     * @ORM\Column(type="datetime")
     */
    protected ?\DateTimeInterface $date = null;

    /**
     * @ORM\Column(type="integer")
     */
    protected ?int $value = null;

    /**
     * @ORM\ManyToOne(targetEntity=ChartLine::class, inversedBy="chartPoints", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    protected ?ChartLine $chartLine = null;

    public static function fromArray(array $data): self
    {
        $chartPoint = new self();

        $chartPoint->setDate(new DateTime($data['date']));
        $chartPoint->setValue($data['value']);

        return $chartPoint;
    }
}

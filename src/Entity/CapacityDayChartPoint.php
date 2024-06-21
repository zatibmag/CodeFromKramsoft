<?php

namespace App\Entity;

use App\Entity\Api\ArrayConvertibleInterface;
use App\Repository\CapacityDayChartPointRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CapacityDayChartPointRepository::class)
 */
class CapacityDayChartPoint extends AbstractChartPoint
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected ?int $id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected ?\DateTimeInterface $date;

    /**
     * @ORM\Column(type="integer")
     */
    protected ?int $value;

    /**
     * @ORM\ManyToOne(targetEntity=ChartLine::class, inversedBy="capacityDayChartPoints", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    protected ?ChartLine $chartLine;

    public static function fromArray(array $data): self
    {
        $chartPoint = new self();

        $chartPoint->setDate(new DateTime($data['date']));
        $chartPoint->setValue($data['value']);

        return $chartPoint;
    }
}

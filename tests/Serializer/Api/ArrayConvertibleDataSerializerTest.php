<?php

namespace App\Tests\Serializer\Api;

use App\Entity\ChartLine;
use App\Entity\ChartPoint;
use App\Entity\Api\Sprint;
use App\Entity\Api\User;
use App\Serializer\Api\ArrayConvertibleDataSerializer;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use PHPUnit\Framework\TestCase;

class ArrayConvertibleDataSerializerTest extends TestCase
{
    protected $serializer;

    protected function setUp(): void
    {
        $this->serializer = new ArrayConvertibleDataSerializer();
    }

    /**
     * @dataProvider sprintDataProvider
     */
    public function testSerializeAndDeserializeSprint(
        Sprint $sprint,
        string $expectedJson,
        array $data,
        array $expectedData
    ) {
        $result = $this->serializer->serialize($sprint);
        $this->assertEquals($expectedJson, $result);

        $result = $this->serializer->deserialize($data, Sprint::class, 'json');
        $this->assertInstanceOf(Sprint::class, $result);
        $this->assertSame($expectedData['name'], $result->getName());
        $this->assertSame($expectedData['capacity'], $result->getCapacity());
        $this->assertEquals($expectedData['startAt'], $result->getStartAt());
        $this->assertEquals($expectedData['endAt'], $result->getEndAt());
        $this->assertEquals($expectedData['listDoneId'], $result->getListDoneId());
        $this->assertEquals($expectedData['capacityType'], $result->getCapacityType());
    }

    public function sprintDataProvider(): array
    {
        $sprint1 = new Sprint();
        $sprint1->setName('Sprint Test');
        $sprint1->setCapacity(10);
        $sprint1->setStartAt(new DateTime('2024-04-01'));
        $sprint1->setEndAt(new DateTime('2024-04-15'));
        $sprint1->setSprintStories(new ArrayCollection());
        $sprint1->setListDoneId('1');
        $sprint1->setCapacityType(0);
        $sprint1->setExcludedDays(new ArrayCollection());

        return [
            [
                $sprint1,
                '{"id":null,"name":"Sprint Test","capacity":10,"startAt":"2024-04-01","endAt":"2024-04-15","sprintStories":[],"listDoneId":"1","capacityType":0,"excludedDays":[]}',
                [
                    'name'          => 'Sprint Test',
                    'capacity'      => 10,
                    'startAt'       => new DateTime('2024-04-01'),
                    'endAt'         => new DateTime('2024-04-15'),
                    'sprintStories' => new ArrayCollection(),
                    'listDoneId'    => '1',
                    'capacityType'  => 0,
                    'excludedDays'  => new ArrayCollection(),
                ],
                [
                    'name'          => 'Sprint Test',
                    'capacity'      => 10,
                    'startAt'       => new DateTime('2024-04-01'),
                    'endAt'         => new DateTime('2024-04-15'),
                    'sprintStories' => new ArrayCollection(),
                    'listDoneId'    => null,
                    'capacityType'  => 0,
                    'excludedDays'  => new ArrayCollection(),
                ],
                true,
                true,
            ],
        ];
    }

    /**
     * @dataProvider chartLineDataProvider
     */
    public function testSerializeAndDeserializeChartLine(
        $chartLine,
        $expectedJson,
        array $data
    ) {
        $serializedData = $this->serializer->serialize($chartLine);
        $this->assertEquals($serializedData, $expectedJson);

        $deserializedChartLine = $this->serializer->deserialize($data, ChartLine::class, 'json');
        $this->assertInstanceOf(ChartLine::class, $deserializedChartLine);

        $expectedChartPoints = $chartLine->getChartPoints();
        $actualChartPoints   = $deserializedChartLine->getChartPoints();

        $this->assertSameSize($expectedChartPoints, $actualChartPoints);

        foreach ($expectedChartPoints as $key => $expectedChartPoint) {
            $this->assertEquals($expectedChartPoint->getDate(), $actualChartPoints[$key]->getDate());
            $this->assertEquals($expectedChartPoint->getValue(), $actualChartPoints[$key]->getValue());
        }
    }

    public function chartLineDataProvider()
    {
        $chartPoint1 = new ChartPoint();
        $chartPoint1->setDate(new DateTime('2024-04-15'));
        $chartPoint1->setValue(10);

        $chartPoint2 = new ChartPoint();
        $chartPoint2->setDate(new DateTime('2024-04-16'));
        $chartPoint2->setValue(20);

        $chartLine = new ChartLine();
        $chartLine->setChartPoints(new ArrayCollection([$chartPoint1, $chartPoint2]));

        yield [
            $chartLine,
            '{"id":null,"chartPoints":[{"x":"2024-04-15","y":10},{"x":"2024-04-16","y":20}]}',
            [
                ['date' => '2024-04-15', 'value' => 10],
                ['date' => '2024-04-16', 'value' => 20],
            ],
        ];
    }

    /**
     * @dataProvider userDataProvider
     */
    public function testSerialize(User $user, string $expectedJson, array $userData): void
    {
        $serializedData = $this->serializer->serialize($user);

        $this->assertEquals($expectedJson, $serializedData);

        $result = $this->serializer->deserialize($userData, User::class, 'json');

        $this->assertEquals($user, $result);
    }

    public static function userDataProvider(): iterable
    {
        $user = new User();
        $user->setUsername('Test');
        $user->setPassword('password');
        $user->setRoles(['ROLE_TEAM_MEMBER']);

        $expectedJson = '{"id":null,"username":"Test","roles":["ROLE_TEAM_MEMBER"]}';

        yield [
            $user,
            $expectedJson,
            [
                'username' => 'Test',
                'password' => 'password',
                'roles'    => ['ROLE_TEAM_MEMBER'],
            ],
        ];
    }

    public function testSerializeThrowsException(): void
    {
        $this->expectException(Exception::class);

        $this->serializer->serialize(null);
    }

    public function testDeserializeThrowsException(): void
    {
        $this->expectException(Exception::class);

        $this->serializer->deserialize(null, User::class, 'json');
    }
}

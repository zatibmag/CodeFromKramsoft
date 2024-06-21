<?php

namespace App\Tests\Manager;

use App\Api\Manager\PermissionManager;
use App\Manager\UserQuestionManager;
use App\Validator\UserValidator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class UserQuestionManagerTest extends TestCase
{
    private $inputInterface;
    private $outputInterface;
    private $validator;
    private $optionChecker;

    protected function setUp(): void
    {
        $this->inputInterface  = $this->createMock(InputInterface::class);
        $this->outputInterface = $this->createMock(OutputInterface::class);
        $this->validator       = $this->createMock(UserValidator::class);
        $this->optionChecker   = $this->createMock(PermissionManager::class);
    }

    public function provideInputData(): iterable
    {
        $validName     = 'validName';
        $validPassword = 'validPassword';
        $emptyValue    = '';

        $testCases = [
            $emptyValue,
            $validName,
            $emptyValue,
            $validPassword,
        ];

        $questionHelper = new class($testCases) extends QuestionHelper {
            private static $counter = 0;
            private        $testCases;

            public function __construct($testCases)
            {
                $this->testCases = $testCases;
            }

            public function ask(InputInterface $input, OutputInterface $output, Question $question): ?string
            {
                $answer = $this->testCases[self::$counter];
                self::$counter++;

                return $answer;
            }
        };

        yield [
            $questionHelper,
        ];
    }

    /**
     * @dataProvider provideInputData
     */
    public function testManageValidInput($questionHelper)
    {
        $userManager = new UserQuestionManager($this->validator, $this->optionChecker, $questionHelper);

        $params = [
            'usernameValidate',
            'passwordValidate',
        ];

        foreach ($params as $key) {
            $this->validator
                ->expects($this->exactly(2))
                ->method($key)
                ->willReturnCallback(function ($value) {
                    if ($value === '') {
                        throw new InvalidArgumentException();
                    } else {
                        return true;
                    }
                });
        }

        $this->outputInterface
            ->expects($this->exactly(2))
            ->method('writeln');

        $userManager->manage($this->inputInterface, $this->outputInterface);
    }
}

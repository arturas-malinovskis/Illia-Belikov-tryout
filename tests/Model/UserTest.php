<?php

declare(strict_types=1);

namespace App\Tests\Model;

use App\Model\User;
use Generator;
use PHPUnit\Framework\TestCase;
use stdClass;

class UserTest extends TestCase
{
    /**
     * @param $id
     * @param $type
     *
     * @dataProvider correctUserProvider
     */
    public function testCorrectUser($id, $type)
    {
        $user = new User($id, $type);

        $this->assertSame($id, $user->getId());
        $this->assertSame($type, $user->getType());

    }

    /**
     * @param $id
     * @param $type
     *
     * @dataProvider incorrectUserProvider
     */
    public function testIncorrectUser($id, $type)
    {
        $this->expectException('TypeError');
        $user = new User($id, $type);
    }

    public function testUserType()
    {
        $user = new User(1, User::TYPE_BUSINESS);
        $this->assertTrue($user->isBusiness());
        $this->assertFalse($user->isPrivate());

        $user = new User(1, User::TYPE_PRIVATE);
        $this->assertTrue($user->isPrivate());
        $this->assertFalse($user->isBusiness());
    }

    /**
     * @return Generator
     */
    public function correctUserProvider(): Generator
    {
        yield [7, User::TYPE_BUSINESS];
        yield [5, User::TYPE_PRIVATE];
    }

    /**
     * @return Generator
     */
    public function incorrectUserProvider(): Generator
    {
        yield [7.5, User::TYPE_BUSINESS];
        yield ['7', User::TYPE_BUSINESS];
        yield [true, User::TYPE_BUSINESS];
        yield [new StdClass(), User::TYPE_BUSINESS];
        yield [1, 1];
        yield [1, 1.5];
        yield [1, true];
        yield [new StdClass(), new StdClass()];
    }
}

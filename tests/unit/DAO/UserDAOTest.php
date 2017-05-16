<?php

namespace AppTest\Service;

use App\DAO\UserDAO;
use Doctrine\DBAL\Connection;
use Mockery;
use PHPUnit\Framework\TestCase;

class UserDAOTest extends TestCase
{

    public function testObjectIsCreatedWithConnectionProperty()
    {
        $connMock = Mockery::mock(Connection::class);
        $dao = $this->getDAO($connMock);

        $this->assertObjectHasAttribute('conn', $dao);
        $this->assertInstanceOf(Connection::class, $dao->getConn());
    }

    public function testGetByUsernameAndPasswordReturnsNullOnNonExistentUser()
    {
        $connMock = Mockery::mock(Connection::class);
        $connMock->shouldReceive('fetchAssoc')->andReturn(null);

        $dao = $this->getDAO($connMock);

        $username = 'any';
        $password = 'any';

        $user = $dao->getByUsernameAndPassword($username, $password);

        $this->assertNull($user);
    }

    public function testGetByUsernameAndPasswordReturnsObjectOnExistentUser()
    {
        $username = 'any';
        $password = 'any';

        $connMock = Mockery::mock(Connection::class);
        $connMock->shouldReceive('fetchAssoc')->andReturn(array(
            'username' => $username,
            'password' => password_hash($password, PASSWORD_BCRYPT)
        ));

        $dao = $this->getDAO($connMock);

        $user = $dao->getByUsernameAndPassword($username, $password);

        $this->assertInternalType('array', $user);
        $this->assertArrayHasKey('username', $user);
        $this->assertArrayHasKey('password', $user);
        $this->assertEquals($user['username'], $username);
        $this->assertTrue(password_verify($password, $user['password']));
    }

    private function getDAO($conn)
    {
        return new UserDAO($conn);
    }
}

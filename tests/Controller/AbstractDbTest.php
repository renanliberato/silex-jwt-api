<?php

namespace AppTest\Controller;

use Silex\WebTestCase;

class AbstractTest extends WebTestCase
{

    public function createApplication()
    {
        $app = require __DIR__.'/../../src/app.php';

        require __DIR__.'/../../config/dev.php';

        $app['session.test'] = true;

        require __DIR__.'/../../src/services.php';

        require __DIR__.'/../../src/controllers.php';

        require __DIR__.'/../../src/routes.php';

        $this->app = $app;

        $this->populateUserTable();

        return $this->app;
    }

    public function testApplicationLoadedTestDb()
    {
        $dbPath = $this->app['db']->getParams()['path'];

        $this->assertEquals('app_test.db', basename($dbPath));
    }

    public function testDatabaseHasAnUser()
    {
        $conn = $this->app['db'];

        $users = $conn->fetchAll('SELECT * FROM user');

        $this->assertEquals(1, count($users));
    }

    public function tearDown()
    {
        $this->eraseUserTable();
    }

    private function populateUserTable()
    {
        $conn = $this->app['db'];

        $conn->insert('user', array(
            'id' => 1,
            'username' => 'superuser',
            'password' => password_hash('superuser', PASSWORD_BCRYPT)
        ));
    }

    private function eraseUserTable()
    {
        $conn = $this->app['db'];
        $conn->delete('user', array('id' => 1));
    }
}
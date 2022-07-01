<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProductControllerTest extends WebTestCase
{

    protected function createAuthenticatedClient($username = 'test@test.com', $password = 'password')
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/login_check',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode(
                [
                    'email' => $username,
                    'password' => $password,
                ]
            )
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2NTY2ODc2NzIsImV4cCI6MTY1NjY5MTI3Miwicm9sZXMiOlsiUk9MRV9VU0VSIl0sImVtYWlsIjoidGVzdEB0ZXN0LmNvbSJ9.gum6YRqoXHW1JgJ-Qwx53looEKZryhEow7dcr_YECIco4TtcarmNon_nD4obqz8rd6u0_tSGSSxBC8PGhbFHkcVEPPNwMe9ic9nUwGfSqvHiL-jR8tXgr4m-XcsN4CkMLLlEezxQRhdUnWRFUVvHHhyHlyzoHDNZcG30Rdq-TNZr_z1jtX7kerSsiTwZ6gITSrsXReNmzG5znJwY1t7wNDaeesAr_5hGqIkS2rBw9lKOwCJSCyqQMMB0A6UDOW17Y8TkRGnFJcJY0ezR6vWIJDC3zN0bBpFbuxrkgW9rBWAgZ3QfKIOsRKHpqp0Fbe1umLgqvL0lZNJAeBWaInAhCA"));

        return $client;
    }

    public function testSomething(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'GET',
            '/api/products',

        );
        $response = $client->getResponse();
        $this->assertSame(401, $response->getStatusCode());
    }


}

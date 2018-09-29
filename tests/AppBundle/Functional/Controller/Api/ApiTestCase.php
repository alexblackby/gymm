<?php

namespace Tests\AppBundle\Functional\Controller\Api;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ApiTestCase extends WebTestCase
{
    const USER_EMAIL = 'user@test.com';
    const ANOTHER_USER_EMAIL = 'admin@test.com';

    static protected $userId;
    static protected $token;
    static protected $tokenAnotherUser;

    public static function setUpBeforeClass()
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();
        $jwtManager = $container->get('lexik_jwt_authentication.jwt_manager');
        $entityManager = $container
            ->get('doctrine')
            ->getManager();

        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => self::USER_EMAIL]);

        self::$userId = $user->getId();
        self::$token = $jwtManager->create($user);

        $anotherUser = $entityManager->getRepository(User::class)->findOneBy(['email' => self::ANOTHER_USER_EMAIL]);
        self::$tokenAnotherUser = $jwtManager->create($anotherUser);

        parent::setUpBeforeClass();
    }


    protected function trainUrl($trainId, $userId)
    {
        return '/api/users/' . $userId . '/trains/' . $trainId;
    }


    protected function createTrain($trainId, $userId)
    {
        $url = $this->trainUrl($trainId, $userId);

        $requestData = [
            "createTime" => time()
        ];

        $response = $this->apiRequest($url, 'PUT', $requestData, [], self::$token);

        return $response;
    }


    protected function apiGetData(Response $response)
    {
        return json_decode($response->getContent(), true);
    }


    protected function apiRequest($uri, $method = 'GET', $data = [], $headers = [], $authToken = '')
    {
        if (!empty($data)) {
            $headers['CONTENT_TYPE'] = 'application/json';
        }
        if ($authToken) {
            $headers['HTTP_AUTHORIZATION'] = 'Bearer ' . $authToken;
        }

        $client = static::createClient();
        $client->request(
            $method,
            $uri,
            [],
            [],
            $headers,
            json_encode($data)
        );

        $response = $client->getResponse();
        return $response;
    }
}
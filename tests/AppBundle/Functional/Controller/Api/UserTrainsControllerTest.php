<?php

namespace Tests\AppBundle\Functional\Controller\Api;

use Ramsey\Uuid\Uuid;

class UserTrainsControllerTest extends ApiTestCase
{
    public function testCreateTrain()
    {
        $trainId = Uuid::uuid4();
        $response = $this->createTrain($trainId, self::$userId);
        $responseData = $this->apiGetData($response);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals($trainId, $responseData['id']);
    }


    public function testViewTrain()
    {
        $trainId = Uuid::uuid4();
        $this->createTrain($trainId, self::$userId);
        $location = $this->trainUrl($trainId, self::$userId);

        $response = $this->apiRequest($location, 'GET', [], [], self::$token);
        $responseData = $this->apiGetData($response);

        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals($trainId, $responseData['id']);

        $fields = ['createTime', 'trainers'];
        foreach ($fields as $field) {
            $this->assertArrayHasKey($field, $responseData);
        }
    }


    public function testDeleteTrain()
    {
        $trainId = Uuid::uuid4();
        $this->createTrain($trainId, self::$userId);
        $location = $this->trainUrl($trainId, self::$userId);

        $response = $this->apiRequest($location, 'DELETE', [], [], self::$token);
        $this->assertEquals(204, $response->getStatusCode());

        $response = $this->apiRequest($location, 'GET', [], [], self::$token);
        $this->assertEquals(404, $response->getStatusCode());
    }


    public function testAnotherUserAccessDenied()
    {
        $trainId = Uuid::uuid4();
        $this->createTrain($trainId, self::$userId);
        $location = $this->trainUrl($trainId, self::$userId);

        $actions = ['GET', 'DELETE', 'PUT'];

        foreach ($actions as $action) {
            $response = $this->apiRequest($location, $action, [], [], self::$tokenAnotherUser);
            $this->assertEquals(403, $response->getStatusCode());
        }
    }

}
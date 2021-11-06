<?php

namespace Tests\Feature;

use App\Enums\UserTypeValues;
use App\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Saurovh\EdsPhpSdk\Api;

class EmployeeDutyControllerTest extends TestCase
{

    private function doTestByUserType(int $userType)
    {
        /**
         * @var User $user
         */
        $user = factory(User::class)->create([
            'type'        => $userType,
            'employee_id' => 1
        ]);
        factory(User::class)->create([
            'type'        => UserTypeValues::SUPERVISOR,
            'employee_id' => 2
        ]);
        $this->actingAs($user);

        $edsDutiesData = [
            [
                "id"         => 1,
                "employeeId" => 1,
                "dutyStart"  => "2021-10-28T10:17=>30.87Z",
                "dutyEnd"    => "2021-10-29T10:17:30.87Z"
            ],
            [
                "id"         => 2,
                "employeeId" => 2,
                "dutyStart"  => "2021-10-28T10:17:30.87Z",
                "dutyEnd"    => "2021-10-29T10:17:30.87Z"
            ]
        ];

        $assertCount = $userType === UserTypeValues::ADMIN ? count($edsDutiesData) : count(
            array_filter($edsDutiesData, function ($item) use ($user) {
                return $item["employeeId"] == $user->employee_id;
            })
        );

        /**
         * @var Api $edsApi
         */
        $edsApi       = $this->app->get(Api::class);
        $mock         = new MockHandler([
            new Response(200, [], json_encode($edsDutiesData)),
            new RequestException('UnAuthorized', new Request('GET', '/employees'),
                new Response(401))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client       = new Client(['handler' => $handlerStack]);
        $edsApi->setHttpClient($client);
        $response = $this->json('GET', '/api/v1/employee-duties');
        $response->assertOk();
        $response->assertJsonCount($assertCount);
        $response->assertJsonStructure(['*' => ["id", "employeeId", "employeeName", "dutyStart", "dutyEnd"]]);
        $response = $this->json('GET', '/api/v1/employee-duties');
        $response->assertStatus(401);
        $response->assertJsonCount(0);
    }

    public function testAdminGet()
    {
        $this->doTestByUserType(UserTypeValues::ADMIN);
    }

    public function testSupervisorGet()
    {
        $this->doTestByUserType(UserTypeValues::SUPERVISOR);
    }
}

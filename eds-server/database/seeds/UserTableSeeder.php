<?php

use App\Enums\UserTypeValues;
use App\User;
use Cassandra\Type\UserType;
use Illuminate\Database\Seeder;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Schema;
use Saurovh\EdsPhpSdk\Api;
use Saurovh\EdsPhpSdk\Http\Response;
use Saurovh\EdsPhpSdk\Object\Employee;
use Faker\Factory;

class UserTableSeeder extends Seeder
{
    /**
     * @var Employee
     */
    protected $edsEmployee;

    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        $edsSdk = Api::init(env('EDS_API_KEY'));
        //$this->edsApiSdks->setLogger(app()->get(Logger::class));

        $this->edsEmployee = new Employee(null, $edsSdk);

        /**
         * @var Faker\Generator
         */
        $faker    = Factory::create();
        $password = app('hash')->make('demo123');

        $this->createUser([
            'email'       => 'msi.saurovh@gmail.com',
            'name'        => 'Msi Saurovh',
            'phone'       => $faker->phoneNumber,
            'joiningDate' => '2021-11-04T20:00:23.996Z',
        ], UserTypeValues::ADMIN, $password);

        for ($i = 1; $i < 50; $i++) {
            $this->createUser([
                'email'       => $faker->unique()->safeEmail,
                'name'        => $faker->name,
                'phone'       => $faker->phoneNumber,
                'joiningDate' => '2021-11-04T20:00:23.996Z',
            ], $i % 2 ? UserTypeValues::ADMIN : UserTypeValues::SUPERVISOR, $password);
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * @param array  $apiData
     * @param int    $userType
     * @param string $password
     *
     * @throws Exception
     */
    private function createUser(array $apiData, int $userType, string $password)
    {
        $response = $this->edsEmployee->create($apiData);
        if ($response->isSuccessful()) {
            $data = $response->getData();
            User::create([
                'name'        => $data['name'],
                'email'       => $data['email'],
                'type'        => $userType,
                'employee_id' => $data['id'],
                'password'    => $password
            ]);
        } else if ($response->hasException()) {
            throw $response->getException();
        }
    }
}

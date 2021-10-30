<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        User::truncate();

        /**
         * createing first user
         */
        User::create([
            'name' => 'Msi saurovh',
            'email' => 'msi.saurovh@gmail.com',
            'password' => app('hash')->make('saurovh')
        ]);

        Schema::enableForeignKeyConstraints();
    }
}

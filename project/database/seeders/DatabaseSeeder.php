<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::query()->where('email', '=', 'admin@shop.ru')->first();
        $user = User::query()->where('email', '=', 'user@shop.ru')->first();

        if(!$admin)
        {
            $admin = User::create
            ([
                'name' => 'admin',
                'email' => 'admin@shop.ru',
                'password' => 'QWEasd123',
                'admin' => true,
            ]);

            $token = $admin->createToken('myapptoken')->plainTextToken;

            $admin->forceFill(['remember_token' => $token,])->save();
        }

        if(!$user)
        {
            $user = User::create
            ([
                'name' => 'user',
                'email' => 'user@shop.ru',
                'password' => 'password',
            ]);

            $token = $user->createToken('myapptoken')->plainTextToken;

            $user->forceFill(['remember_token' => $token,])->save();
        }
    }
}



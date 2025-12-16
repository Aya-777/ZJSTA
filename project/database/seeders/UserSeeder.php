<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
        'first_name'=>'John',
        'last_name'=>'Doe',
        'email'=>'john.doe@example.com',
        'password'=>Hash::make('password123'),
        'phone_number'=>'123-456-7890',
        'role'=>'admin',
        'profile_picture'=>'john_profile.jpg',
        'identity_image'=>'john_identity.jpg',
        'birth_date'=>'1990-01-01',
        'is_active'=>true,
        ]);

        User::create([
        'first_name'=>'Tuka',
        'last_name'=>'MH',
        'email'=>'tuka@example.com',
        'password'=>Hash::make('password'),
        'phone_number'=>'987-654-3210',
        'role'=>'renter',
        'profile_picture'=>'tuka_profile.jpg',
        'identity_image'=>'tuka_identity.jpg',
        'birth_date'=>'2005-07-18',
        'is_active'=>true,
        ]);

        User::create([
            'first_name'=>'Alice',
            'last_name'=>'Smith',
            'email'=>'alice.smith@example.com',
            'password'=>Hash::make('alicepass'),
            'phone_number'=>'555-123-4567',
            'role'=>'owner',
            'profile_picture'=>'alice_profile.jpg',
            'identity_image'=>'alice_identity.jpg',
            'birth_date'=>'1985-05-15',
            'is_active'=>true,
        ]);

        User::factory()->count(10)->create();

        
    }
}

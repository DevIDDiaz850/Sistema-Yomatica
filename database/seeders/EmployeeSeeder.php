<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employeesData = [
            [
                'first_name' => 'Carlos',
                'last_name' => 'Gómez',
                'email' => 'carlos.gomez@empresa.com',
                'position' => 'Soporte Técnico Senior',
                'department' => 'Tecnología',
                'employee_code' => 'EMP-001',
                'phone' => '+1 555-0101',
                'dni_or_vat' => '12345678A',
            ],
            [
                'first_name' => 'María',
                'last_name' => 'López',
                'email' => 'maria.lopez@empresa.com',
                'position' => 'Desarrolladora Backend',
                'department' => 'Tecnología',
                'employee_code' => 'EMP-002',
                'phone' => '+1 555-0102',
                'dni_or_vat' => '23456789B',
            ],
            [
                'first_name' => 'Alejandro',
                'last_name' => 'Torres',
                'email' => 'alejandro.torres@empresa.com',
                'position' => 'Especialista en QA',
                'department' => 'Control de Calidad',
                'employee_code' => 'EMP-003',
                'phone' => '+1 555-0103',
                'dni_or_vat' => '34567890C',
            ],
            [
                'first_name' => 'Ana',
                'last_name' => 'Martínez',
                'email' => 'ana.martinez@empresa.com',
                'position' => 'Líder de Proyecto',
                'department' => 'Operaciones',
                'employee_code' => 'EMP-004',
                'phone' => '+1 555-0104',
                'dni_or_vat' => '45678901D',
            ],
            [
                'first_name' => 'Javier',
                'last_name' => 'Ramírez',
                'email' => 'javier.ramirez@empresa.com',
                'position' => 'Analista de Sistemas',
                'department' => 'Tecnología',
                'employee_code' => 'EMP-005',
                'phone' => '+1 555-0105',
                'dni_or_vat' => '56789012E',
            ],
        ];

        foreach ($employeesData as $data) {
            $user = User::create([
                'name' => "{$data['first_name']} {$data['last_name']}",
                'email' => $data['email'],
                'password' => Hash::make('password123'),
                'type' => 'employee',
                'email_verified_at' => now(),
            ]);

            Employee::create([
                'user_id' => $user->id,
                'employee_code' => $data['employee_code'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'dni_or_vat' => $data['dni_or_vat'],
                'phone' => $data['phone'],
                'position' => $data['position'],
                'department' => $data['department'],
                'hired_at' => now()->subMonths(rand(1, 24))->format('Y-m-d'),
            ]);
        }
    }
}

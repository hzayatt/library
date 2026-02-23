<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Books
            'view books', 'create books', 'edit books', 'delete books',
            // Borrowings
            'view borrowings', 'checkout books', 'checkin books', 'manage borrowings',
            // Users
            'view users', 'manage users',
            // Reports
            'view reports',
            // Reviews
            'create reviews', 'delete reviews',
            // Reading list
            'manage reading list',
            // AI
            'use ai features',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        $librarian = Role::firstOrCreate(['name' => 'librarian']);
        $librarian->syncPermissions([
            'view books', 'create books', 'edit books',
            'view borrowings', 'checkout books', 'checkin books', 'manage borrowings',
            'view users', 'view reports',
            'create reviews', 'delete reviews',
            'use ai features',
        ]);

        $member = Role::firstOrCreate(['name' => 'member']);
        $member->syncPermissions([
            'view books',
            'view borrowings',
            'create reviews',
            'manage reading list',
        ]);

        // Create default admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@library.com'],
            [
                'name' => 'Library Admin',
                'password' => bcrypt('admin123'),
                'is_active' => true,
            ]
        );
        $adminUser->assignRole('admin');

        // Create librarian
        $librarianUser = User::firstOrCreate(
            ['email' => 'librarian@library.com'],
            [
                'name' => 'Jane Librarian',
                'password' => bcrypt('librarian123'),
                'is_active' => true,
            ]
        );
        $librarianUser->assignRole('librarian');

        // Create demo member
        $memberUser = User::firstOrCreate(
            ['email' => 'member@library.com'],
            [
                'name' => 'John Member',
                'password' => bcrypt('member123'),
                'is_active' => true,
            ]
        );
        $memberUser->assignRole('member');

        $this->command->info('Roles, permissions, and default users created!');
    }
}

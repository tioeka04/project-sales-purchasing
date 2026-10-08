<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public const ADMIN = 'Admin';

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'product.view', 'product.create', 'product.edit', 'product.delete',
            'supplier.view', 'supplier.create', 'supplier.edit', 'supplier.delete',
            'customer.view', 'customer.create', 'customer.edit', 'customer.delete',
            'purchase-order.view', 'purchase-order.create', 'purchase-order.edit', 'purchase-order.delete', 'purchase-order.approve',
            'sales-order.view', 'sales-order.create', 'sales-order.edit', 'sales-order.delete', 'sales-order.approve',
            'user.manage', 'role.manage',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        Role::firstOrCreate(['name' => self::ADMIN])->syncPermissions(Permission::all());

        Role::firstOrCreate(['name' => 'Purchasing Staff'])->syncPermissions([
            'product.view', 'supplier.view', 'supplier.create', 'supplier.edit', 'customer.view',
            'purchase-order.view', 'purchase-order.create', 'purchase-order.edit',
        ]);

        Role::firstOrCreate(['name' => 'Sales Staff'])->syncPermissions([
            'product.view', 'supplier.view', 'customer.view', 'customer.create', 'customer.edit',
            'sales-order.view', 'sales-order.create', 'sales-order.edit',
        ]);

        Role::firstOrCreate(['name' => 'Manager'])->syncPermissions([
            'product.view', 'supplier.view', 'customer.view',
            'purchase-order.view', 'purchase-order.approve',
            'sales-order.view', 'sales-order.approve',
        ]);

        User::firstOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Administrator',
            'password' => Hash::make('password'),
        ])->assignRole(self::ADMIN);
    }
}

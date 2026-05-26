<?php

namespace App\Console\Commands;

use App\Models\ApplicationConfiguration;
use App\Models\BookingConfiguration;
use App\Models\Configuration;
use App\Models\DriverConfiguration;
use App\Models\Merchant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Passport\Client;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class BootstrapStarideMerchant extends Command
{
    protected $signature = 'staride:bootstrap-merchant
                            {--alias=staride : Merchant alias used in the login URL}
                            {--name=StarRide : Business name}
                            {--email=admin@staride.akiliapp.co.tz : Login email}
                            {--password=StarRide@123 : Login password}';

    protected $description = 'Create the first merchant account and required related records for a fresh database';

    public function handle(): int
    {
        if (Merchant::query()->exists()) {
            $this->error('Merchants already exist. Aborting to avoid duplicates.');

            return self::FAILURE;
        }

        if (!DB::table('permissions')->exists()) {
            $this->warn('No permissions found. Run: php artisan db:seed --class=PermissionTableSeeder');
        }

        $alias = Str::slug($this->option('alias'), '_');
        $email = $this->option('email');
        $password = $this->option('password');

        DB::beginTransaction();

        try {
            $merchant = Merchant::create([
                'parent_id' => 0,
                'BusinessName' => $this->option('name'),
                'email' => $email,
                'BusinessLogo' => 'default_logo.png',
                'alias_name' => $alias,
                'country_ids' => null,
                'merchantFirstName' => 'Admin',
                'merchantLastName' => 'User',
                'merchantPhone' => '0000000000',
                'merchantAddress' => 'Tanzania',
                'password' => Hash::make($password),
                'merchantPublicKey' => Str::random(32),
                'merchantSecretKey' => Str::random(32),
                'merchantStatus' => 1,
                'string_group' => 'all_in_one',
                'string_file' => $alias . '_all_in_one_en',
                'access_pin' => null,
            ]);

            Configuration::create(['merchant_id' => $merchant->id]);
            DriverConfiguration::create(['merchant_id' => $merchant->id]);
            BookingConfiguration::create(['merchant_id' => $merchant->id]);
            ApplicationConfiguration::create(['merchant_id' => $merchant->id]);

            $role = Role::create([
                'merchant_id' => $merchant->id,
                'name' => 'Super Admin' . $merchant->id,
                'display_name' => 'Super Admin',
                'description' => 'Super Admin',
                'guard_name' => 'merchant',
            ]);

            $permissions = Permission::all();
            if ($permissions->isNotEmpty()) {
                $role->givePermissionTo($permissions);
            }

            $merchant->assignRole($role);

            (new Client)->forceFill([
                'user_id' => $merchant->id,
                'name' => $merchant->alias_name,
                'secret' => Str::random(40),
                'redirect' => config('app.url', 'http://localhost'),
                'personal_access_client' => false,
                'password_client' => true,
                'revoked' => false,
            ])->save();

            $segmentId = DB::table('segments')->value('id');
            if ($segmentId) {
                DB::table('merchant_segment')->insert([
                    'merchant_id' => $merchant->id,
                    'segment_id' => $segmentId,
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $loginUrl = rtrim(config('app.url'), '/') . '/merchant/admin/' . $alias . '/login';

        $this->info('Merchant created successfully.');
        $this->table(['Field', 'Value'], [
            ['ID', (string) $merchant->id],
            ['Alias', $alias],
            ['Email', $email],
            ['Password', $password],
            ['Login URL', $loginUrl],
        ]);

        return self::SUCCESS;
    }
}

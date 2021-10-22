<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ConfigurationTableSeeder::class);
        $this->call(AgamaTableSeeder::class);        
        $this->call(TATableSeeder::class);        
        $this->call(KebutuhanKhususTableSeeder::class);
        $this->call(ModaTransportasiTableSeeder::class);        
        $this->call(JenjangStudiTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);        
    }
}

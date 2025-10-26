<?php 
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class StaffSeeder extends Seeder
{
    public function run()
    {
        $password = password_hash('Test@123', PASSWORD_DEFAULT);
        $data = [
            'name'=>'Admin User',
            'code'=>'ADM001',
            'branch'=>'Main',
            'username'=>'admin',
            'password'=>$password,
            'mobile'=>'9999999999',
            'gender'=>'Male',
            'created_at'=>date('Y-m-d H:i:s')
        ];
        $this->db->table('staffs')->insert($data);
    }
}

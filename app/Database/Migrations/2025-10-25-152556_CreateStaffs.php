<?php 
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStaffs extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'name' => ['type'=>'VARCHAR','constraint'=>255],
            'code' => ['type'=>'VARCHAR','constraint'=>100,'null'=>true],
            'branch' => ['type'=>'VARCHAR','constraint'=>150,'null'=>true],
            'username' => ['type'=>'VARCHAR','constraint'=>150,'unique'=>true],
            'password' => ['type'=>'VARCHAR','constraint'=>255],
            'mobile' => ['type'=>'VARCHAR','constraint'=>20,'null'=>true],
            'gender' => ['type'=>'ENUM','constraint'=>"'Male','Female','Other'","default"=>"Male"],
            'created_at' => ['type'=>'DATETIME','null'=>true],
            'updated_at' => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('staffs');
    }

    public function down()
    {
        $this->forge->dropTable('staffs');
    }
}

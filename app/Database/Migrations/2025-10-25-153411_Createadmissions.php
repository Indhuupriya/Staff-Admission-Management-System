<?php 
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAdmissions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'=> ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'patient_name' => ['type'=>'VARCHAR','constraint'=>255],
            'patient_phone' => ['type'=>'VARCHAR','constraint'=>30,null=>true],
            'admission_date' => ['type'=>'DATETIME','null'=>true],
            'admission_type' => ['type'=>"ENUM",'constraint'=>"'OP','IP'","default"=>'OP'],
            'patient_signature' => ['type'=>'TEXT','null'=>true], // svg/base64
            'status' => ['type'=>"ENUM",'constraint'=>"'Admitted','Treatment In Progress','Discharged'","default"=>"Admitted"],
            'created_by' => ['type'=>'INT','unsigned'=>true],
            'created_branch' => ['type'=>'VARCHAR','constraint'=>150,null=>true],
            'created_at' => ['type'=>'DATETIME','null'=>true],
            'updated_at' => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('admissions');
    }

    public function down()
    {
        $this->forge->dropTable('admissions');
    }
}

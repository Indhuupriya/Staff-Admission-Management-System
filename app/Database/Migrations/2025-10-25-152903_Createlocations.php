<?php 
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLocations extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'=> ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'name'=> ['type'=>'VARCHAR','constraint'=>150],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('locations');

        // insert sample locations
        $db = \Config\Database::connect();
        $db->table('locations')->insertBatch([
            ['name'=>'Chennai'],['name'=>'Trichy'],['name'=>'Mumbai'],['name'=>'Delhi']
        ]);
    }
    public function down()
    {
        $this->forge->dropTable('locations');
    }
}

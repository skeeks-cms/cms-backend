<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 28.08.2015
 */

use yii\db\Migration;

class m180313_120601_create_table__backend_showing extends Migration
{
    public function safeUp()
    {
        $tableName = 'backend_showing';
        $tableExist = $this->db->getTableSchema($tableName, true);
        if ($tableExist) {
            return true;
        }
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable($tableName, [
            'id' => $this->primaryKey(),

            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),

            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),

            'name'          => $this->string(255),
            'cms_user_id'   => $this->integer(),
            'is_default'    => $this->integer(1)->notNull()->defaultValue(1),
            'key'           => $this->string(255)->notNull(),
            'priority'      => $this->integer()->notNull()->defaultValue(100),
            'config_jsoned' => $this->text(),

        ], $tableOptions);


        $this->createIndex($tableName.'__updated_by', $tableName, 'updated_by');
        $this->createIndex($tableName.'__created_by', $tableName, 'created_by');
        $this->createIndex($tableName.'__created_at', $tableName, 'created_at');
        $this->createIndex($tableName.'__updated_at', $tableName, 'updated_at');

        $this->createIndex($tableName.'__cms_user_id', $tableName, 'cms_user_id');
        $this->createIndex($tableName.'__is_default', $tableName, 'is_default');
        $this->createIndex($tableName.'__name', $tableName, 'name');
        $this->createIndex($tableName.'__key', $tableName, 'key');
        $this->createIndex($tableName.'__priority', $tableName, 'priority');

        $this->addForeignKey(
            "{$tableName}__created_by", $tableName,
            'created_by', '{{%cms_user}}', 'id', 'SET NULL', 'SET NULL'
        );

        $this->addForeignKey(
            "{$tableName}__updated_by", $tableName,
            'updated_by', '{{%cms_user}}', 'id', 'SET NULL', 'SET NULL'
        );

        $this->addForeignKey(
            "{$tableName}__cms_user_id", $tableName,
            'cms_user_id', '{{%cms_user}}', 'id', 'CASCADE', 'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey("{$tableName}__created_by", $tableName);
        $this->dropForeignKey("{$tableName}__updated_by", $tableName);
        $this->dropForeignKey("{$tableName}__cms_user_id", $tableName);

        $this->dropTable($tableName);
    }
}
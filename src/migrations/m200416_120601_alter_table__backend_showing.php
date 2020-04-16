<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 28.08.2015
 */

use yii\db\Migration;

class m200416_120601_alter_table__backend_showing extends Migration
{
    public function safeUp()
    {
        $tableName = 'backend_showing';

        $this->addColumn($tableName, "cms_site_id", $this->integer());
        $this->createIndex("cms_site_id", $tableName, "cms_site_id");

        $this->addForeignKey(
            "{$tableName}__cms_site_id", $tableName,
            'cms_site_id', "cms_site", 'id', 'CASCADE', 'CASCADE'
        );
        
        $subQuery = $this->db->createCommand("
            UPDATE 
                `{$tableName}` as c
            SET 
                c.cms_site_id = (select cms_site.id from cms_site where cms_site.is_default = 1)
        ")->execute();
        
        $this->alterColumn($tableName, "cms_site_id", $this->integer()->notNull());
    }

    public function safeDown()
    {
        echo "m200416_120601_alter_table__backend_showing cannot be reverted.\n";
        return false;
    }
}
<?= '<?php' ?>

use DbMigration\Migration;

class <?= $className ?> extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        // code ...
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        return false; // 此迁移不能被回滚
    }
}

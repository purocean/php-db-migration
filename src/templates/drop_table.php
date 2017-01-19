<?= '<?php' ?>

use DbMigration\Migration;

/**
 * Handles the droping of table `<?= $table ?>`.
 */
class <?= $className ?> extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropTable('<?= $table ?>');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        return false; // 此迁移不能被回滚
    }
}

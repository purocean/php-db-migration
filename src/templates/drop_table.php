<?= '<?php' ?>

use yii\db\Migration;

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
        // Create Table;
    }
}

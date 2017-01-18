<?= '<?php' ?>

use yii\db\Migration;

/**
 * Handles droping column(s) from table `<?= $table ?>`.
 */
class <?= $className ?> extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropColumn('<?= $table ?>', '__COLUMN_NAME__');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // Add column;
    }
}

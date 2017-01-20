<?= '<?php' ?>

use DbMigration\Migration;

/**
 * Handles adding column(s) to table `<?= $table ?>`.
 */
class <?= $className ?> extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn(
            '<?= $table ?>',
            '__COLUMN_NAME__',
            $this->string()->after('id')->notNull()->defaultValue('')->comment('comment')
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('<?= $table ?>', '__COLUMN_NAME__');
    }
}

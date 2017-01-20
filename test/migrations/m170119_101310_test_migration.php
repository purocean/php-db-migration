<?php
use DbMigration\Migration;

class m170119_101310_test_migration extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->dropTable('{{%test}}', true);

        $this->createTable('{{%test}}', [
            'id'        => $this->primaryKey()->comment('testID'),
            'char'      => $this->char(12)->notNull()->defaultValue('testChar')->unique()->comment('测试'),
            'varchar'   => $this->varchar(123)->notNull()->defaultValue('test varchar')->append('UNIQUE'),
            'string'    => $this->string(200)->defaultValue('hello'),
            'string2'   => $this->string()->defaultValue('hello2'),
            'text'      => $this->text(),
            'smallint'  => $this->smallInteger()->notNull()->null(),
            'int'       => $this->integer(18),
            'integer'   => $this->integer()->unsigned(),
            'float'     => $this->float(),
            'float2'    => $this->float(13, 5),
            'double'    => $this->double(10, 4),
            'decimal'   => $this->decimal(15, 7),
            'datetime'  => $this->dateTime(),
            'datetime2' => $this->dateTime(5),
            'timestamp' => $this->timestamp(6),
            'timestamp' => $this->timestamp(),
            'time'      => $this->time(),
            'time2'     => $this->time(4),
            'date'      => $this->date(),
            'binary'    => $this->binary(255),
            'boolean'   => $this->boolean(),
            'binary2'   => (new \DbMigration\Column('varbinary', 128))->comment('test'),
        ], $tableOptions);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%test}}');

        return true;
    }
}
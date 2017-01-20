<?php
namespace DbMigration;

trait ColumnTrait
{
    public function primaryKey($length = null)
    {
        return new Column('int', $length, ' AUTO_INCREMENT PRIMARY KEY');
    }

    public function char($length = null)
    {
        return new Column('char', $length);
    }

    public function varchar($length = 255)
    {
        return new Column('char', $length);
    }

    public function string($length = 255)
    {
        return $this->varchar($length);
    }

    public function text()
    {
        return new Column('text');
    }

    public function smallInteger($length = null)
    {
        return new Column('smallint', $length);
    }

    public function integer($length = null)
    {
        return new Column('int', $length);
    }

    public function bigInteger($length = null)
    {
        return new Column('bigint', $length);
    }

    public function float($precision = null, $scale = null)
    {
        return new Column('float', array_filter([$precision, $scale]));
    }

    public function double($precision = null, $scale = null)
    {
        return new Column('double', array_filter([$precision, $scale]));
    }

    public function decimal($precision = null, $scale = null)
    {
        return new Column('decimal', array_filter([$precision, $scale]));
    }

    public function dateTime($precision = null)
    {
        return new Column('datetime', $precision);
    }

    public function timestamp($precision = null)
    {
        return new Column('timestamp', $precision);
    }

    public function time($precision = null)
    {
        return new Column('time', $precision);
    }

    public function date()
    {
        return new Column('date');
    }

    public function binary($length = null)
    {
        return new Column('binary', $length);
    }

    public function boolean()
    {
        return new Column('boolean');
    }
}

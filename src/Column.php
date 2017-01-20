<?php
namespace DbMigration;

/**
* Column
*/
class Column
{
    private $_type;
    private $_length;
    private $_isNotNull;
    private $_isUnique = false;
    private $_default;
    private $_append;
    private $_isUnsigned = false;
    private $_after;
    private $_isFirst;
    private $_comment;

    public function __construct($type, $length = null, $append = null)
    {
        $this->_type = $type;
        $this->_length = $length;
        $this->_append = $append;
    }

    public function notNull()
    {
        $this->_isNotNull = true;
        return $this;
    }

    public function null()
    {
        $this->_isNotNull = false;
        return $this;
    }

    public function unique()
    {
        $this->_isUnique = true;
        return $this;
    }

    public function defaultValue($default)
    {
        if ($default === null) {
            $this->_null();
        }

        $this->_default = $default;
        return $this;
    }

    public function comment($comment)
    {
        $this->_comment = $comment;
        return $this;
    }

    public function unsigned()
    {
        $this->_isUnsigned = true;
        return $this;
    }

    public function after($after)
    {
        $this->_after = $after;
        return $this;
    }

    public function first()
    {
        $this->_isFirst = true;
        return $this;
    }

    public function append($sql)
    {
        $this->_append = $sql;
        return $this;
    }

    public function buildCompleteString($key)
    {
        $placeholderValues = [
            '{key}' => $key,
            '{type}' => $this->_type,
            '{length}' => $this->_buildLengthString(),
            '{unsigned}' => $this->_buildUnsignedString(),
            '{notnull}' => $this->_buildNotNullString(),
            '{unique}' => $this->_buildUniqueString(),
            '{default}' => $this->_buildDefaultString(),
            '{comment}' => $this->_buildCommentString(),
            '{pos}' => $this->_isFirst ? $this->_buildFirstString() : $this->_buildAfterString(),
            '{append}' => $this->_buildAppendString(),
        ];
        return strtr('{key} {type}{length}{unsigned}{notnull}{unique}{default}{comment}{pos}{append}', $placeholderValues);
    }

    protected function _buildLengthString()
    {
        if ($this->_length === null || $this->_length === []) {
            return '';
        }
        if (is_array($this->_length)) {
            $this->_length = implode(',', $this->_length);
        }
        return "({$this->_length})";
    }

    protected function _buildNotNullString()
    {
        if ($this->_isNotNull === true) {
            return ' NOT NULL';
        } elseif ($this->_isNotNull === false) {
            return ' NULL';
        } else {
            return '';
        }
    }

    protected function _buildUniqueString()
    {
        return $this->_isUnique ? ' UNIQUE' : '';
    }

    protected function _buildDefaultString()
    {
        if ($this->_default === null) {
            return $this->_isNotNull === false ? ' DEFAULT NULL' : '';
        }

        $string = ' DEFAULT ';
        switch (gettype($this->_default)) {
            case 'integer':
                $string .= (string) $this->_default;
                break;
            case 'double':
                // ensure type cast always has . as decimal separator in all locales
                $string .= str_replace(',', '.', (string) $this->_default);
                break;
            case 'boolean':
                $string .= $this->_default ? 'TRUE' : 'FALSE';
                break;
            case 'object':
                $string .= (string) $this->_default;
                break;
            default:
                $string .= "'{$this->_default}'";
        }

        return $string;
    }

    private function _buildUnsignedString()
    {
        return $this->_isUnsigned ? ' UNSIGNED' : '';
    }

    private function _buildAfterString()
    {
        return $this->_after !== null ? ' AFTER ' . Db::getQuoted($this->_after) : '';
    }

    private function _buildFirstString()
    {
        return $this->_isFirst ? ' FIRST' : '';
    }

    private function _buildCommentString()
    {
        return $this->_comment !== null ? ' COMMENT \''.Db::addcslashes($this->_comment).'\'' : '';
    }

    private function _buildAppendString()
    {
        return $this->_append !== null ? ' ' . $this->_append : '';
    }
}

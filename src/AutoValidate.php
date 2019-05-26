<?php

namespace ke;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Db;
use think\facade\App;
use think\facade\Config;

class AutoValidate extends Command
{
    private $database = '';

    private $table = '';

    protected function configure()
    {
        $this->setName('ke:validate')
            ->addArgument('table', Argument::REQUIRED, 'table name')
            ->addOption('m', null, Option::VALUE_REQUIRED, 'module name');
    }


    protected function execute(Input $input, Output $output)
    {
        if ($input->hasOption('m')) {
            $module = $input->getOption('m');
        } else {
            $module = 'common';
        }
        $table = $input->getArgument('table');
        $this->table = Parse::humpToLine($table);
        $this->database = Config::get('database.database');

        $columns = $this->getColumnInfo();
        $handle = new BuildValidate($columns, $table, $module);
        $handle->generate(App::getAppPath() . $module . '/validate/' . $table . 'Validate.php');
    }


    /**
     * 获得字段信息
     * @return array
     */
    private function getColumnInfo()
    {
        // auto_increment
        $field = [
            'COLUMN_NAME',
            'COLUMN_DEFAULT',
            'IS_NULLABLE',
            'DATA_TYPE',
            'COLUMN_TYPE',
            'EXTRA',
            'COLUMN_COMMENT'
        ];
        $field = implode(',', $field);
        $list = Db::query("SELECT {$field} from information_schema.COLUMNS where TABLE_NAME='{$this->table}'  AND table_schema = '{$this->database}' ORDER BY `ORDINAL_POSITION` ASC");
        return $list;
    }

}

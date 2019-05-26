<?php


namespace ke;


class BuildValidate
{
    private $list;

    private $className;

    private $moduleName;

    private $file;

    public function __construct(array $list, $className, $moduleName)
    {
        $this->list = $list;
        $this->className = $className;
        $this->moduleName = $moduleName;
    }


    public function generate($file)
    {
        $rules = [];
        foreach ($this->list as $item) {
            $label = $item['COLUMN_NAME'];
            $rule = [];
            if ($this->isRequire($item)) {
                $rule[] = 'require';
            }

            if (strpos($item['DATA_TYPE'], 'int') === false) {
                $len = $this->getLen($item['COLUMN_TYPE']);
                if (!is_null($len)) {
                    $rule['max'] = $len;
                }
            } else {
                $rule[] = 'number';
            }

            if ($item['COLUMN_COMMENT']) {
                $label .= '|' . $item['COLUMN_COMMENT'];
            }
            $rules[$label] = $rule;
        }

        $this->list = $rules;
        $this->file = $file;
        $this->write();
    }


    private function isRequire($item)
    {
        return $item['IS_NULLABLE'] === 'NO' && is_null($item['COLUMN_DEFAULT']);
    }


    private function getLen($str)
    {
        if (preg_match('/\((\d+)\)/', $str, $match)) {
            return $match[1];
        }
        return null;
    }


    private function write()
    {
        if (is_file($this->file)) {
            print "Validate Is Exist\r\n";
            return;
        }
        $dir = dirname($this->file);
        if (!is_dir($dir)) {
            mkdir($dir, 0555, true);
        }
        $content = file_get_contents(__DIR__ . '/../template/dist.php');
        $vars = "[\r\n";
        foreach ($this->list as $key => $item) {
            $val = [];
            foreach ($item as $k => $v) {
                if (is_numeric($k)) {
                    $val[] = "{$v}";
                } else {
                    $val[] = "{$k}:{$v}";
                }
            }
            $val = implode('|', $val);
            $vars .= "        '{$key}'=>'{$val}',\r\n";
        }
        $vars .= "    ]";

        file_put_contents($this->file, str_replace([
            '{$NAMESPACE}',
            '{$CLASS}',
            '{$RULES}'
        ], [
            'app\\' . $this->moduleName . '\\validate',
            Parse::convertUnderline($this->className),
            $vars
        ], $content));

        print "make file:";
        print $this->file . "\r\n";
    }



}

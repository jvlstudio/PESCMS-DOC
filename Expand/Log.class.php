<?php

/**
 * PESCMS for PHP 5.4+
 *
 * Copyright (c) 2014 PESCMS (http://www.pescms.com)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace Expand;

/**
 * 日志记录扩展
 */
class Log {

    private $config, $logPath, $path;

    public function __construct() {
        $this->checkPath();
        $this->deleteLog($this->logPath);
    }

    /**
     * 验证日志目录是否存在
     */
    private function checkPath() {
        $this->config = require PES_PATH . 'Config/config.php';

        $this->logPath = PES_PATH . $this->config['LOG_PATH'];
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath);
            fopen("{$this->logPath}/index.html", 'w');
        }

        $this->path = $this->logPath . date('/Ymd');
        if (!is_dir($this->path)) {
            mkdir($this->path);
            fopen("{$this->path}/index.html", 'w');
        }
    }

    /**
     * 创建日志
     * @param type $fileName 日志名称
     * @param type $logContent 日志内容
     */
    public function creatLog($fileName, $logContent) {
        $file = "{$this->path}/{$fileName}_" . md5(md5($this->config['PRIVATE_KEY'])) . ".txt";
        if (!file_exists("$file")) {
            fopen("$file", "w");
            $fp = fopen("$file", 'ab');
        } else {
            $fp = fopen("$file", 'ab');
        }
        fwrite($fp, $logContent . "\n");
        fclose($fp);
    }

    /**
     * 移除过期的日志
     */
    public function deleteLog($path) {
        $expired = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - $this->config['LOG_DELETE'], date("Y")));
        if ($handle = opendir($path)) {
            while (false !== ($item = readdir($handle))) {
                if ($item != "." && $item != "..") {
                    if (is_dir("{$path}/{$item}") && $item < $expired) {
                        $this->deleteLog("{$path}/{$item}");
                    } else {
                        if ($path != $this->logPath) {
                            unlink("{$path}/{$item}");
                        }
                    }
                }
            }
            closedir($handle);

            if ($path != $this->logPath) {
                rmdir($path);
            }
        }
    }

}

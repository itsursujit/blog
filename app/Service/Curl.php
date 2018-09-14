<?php
namespace App\Service;

/**
 * File Curl
 *
 *
 * PHP version 7
 *
 * @category   PHP
 * @package    ${NAMESPACE}
 * @subpackage
 * @author     Sujit Baniya <sujit@intergo.com.cy>
 * @copyright  2018 intergo.com.cy. All rights reserved.
 */
class Curl
{

    protected $id;
    protected $handle;
    protected $meetPhp55 = false;
    /**
     * @var Response
     */
    protected $response;
    protected $multi = false;
    protected $options = [];
    protected static $defaultOptions = [
        //bool
        CURLOPT_HEADER         => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_RETURNTRANSFER => true,
        //int
        CURLOPT_MAXREDIRS      => 3,
        CURLOPT_TIMEOUT        => 6,
        CURLOPT_CONNECTTIMEOUT => 3,
        //string
        CURLOPT_USERAGENT      => 'Multi-cURL client v1.5.0',
    ];
    public function __construct($id = null, array $options = [])
    {
        $this->id = $id;
        $this->options = $options + self::$defaultOptions;
        $this->meetPhp55 = version_compare(PHP_VERSION, '5.5.0', '>=');
    }

    protected function init()
    {
        if ($this->meetPhp55) {
            if ($this->handle === null) {
                $this->handle = curl_init();
            } else {
                curl_reset($this->handle); //Reuse cUrl handle: since 5.5.0
            }
        } else {
            if ($this->handle !== null) {
                curl_close($this->handle);
            }
            $this->handle = curl_init();
        }
        curl_setopt_array($this->handle, $this->options);
    }

    public function getId()
    {
        return $this->id;
    }
    public function get($url, $params = null, array $headers = [])
    {
        $this->init();
        if (is_string($params) || is_array($params)) {
            is_array($params) AND $params = http_build_query($params);
            $url = rtrim($url, '?');
            if (strpos($url, '?') !== false) {
                $url .= '&' . $params;
            } else {
                $url .= '?' . $params;
            }
        }
        curl_setopt_array($this->handle, [CURLOPT_URL => $url, CURLOPT_HTTPGET => true]);
        if (!empty($headers)) {
            curl_setopt($this->handle, CURLOPT_HTTPHEADER, $headers);
        }
    }

    public function post($url, $params = null, array $headers = [])
    {
        $this->init();
        curl_setopt_array($this->handle, [CURLOPT_URL => $url, CURLOPT_POST => true]);
        //CURLFile support
        if (is_array($params)) {
            $hasUploadFile = false;
            if ($this->meetPhp55) {//CURLFile: since 5.5.0
                foreach ($params as $k => $v) {
                    if ($v instanceof \CURLFile) {
                        $hasUploadFile = true;
                        break;
                    }
                }
            }
            $hasUploadFile OR $params = http_build_query($params);
        }
        //$params: array => multipart/form-data, string => application/x-www-form-urlencoded
        if (!empty($params)) {
            curl_setopt($this->handle, CURLOPT_POSTFIELDS, $params);
        }
        if (!empty($headers)) {
            curl_setopt($this->handle, CURLOPT_HTTPHEADER, $headers);
        }
    }

    public function put($url, $params = null, array $headers = [])
    {
        $this->init();
        curl_setopt_array($this->handle, [CURLOPT_URL => $url, CURLOPT_POST => true, CURLOPT_CUSTOMREQUEST => 'PUT']);
        //CURLFile support
        if (is_array($params)) {
            $hasUploadFile = false;
            if ($this->meetPhp55) {//CURLFile: since 5.5.0
                foreach ($params as $k => $v) {
                    if ($v instanceof \CURLFile) {
                        $hasUploadFile = true;
                        break;
                    }
                }
            }
            $hasUploadFile OR $params = http_build_query($params);
        }
        //$params: array => multipart/form-data, string => application/x-www-form-urlencoded
        if (!empty($params)) {
            curl_setopt($this->handle, CURLOPT_POSTFIELDS, $params);
        }
        if (!empty($headers)) {
            curl_setopt($this->handle, CURLOPT_HTTPHEADER, $headers);
        }
    }

    public function delete($url, $params = null, array $headers = [])
    {
        $this->init();
        curl_setopt_array($this->handle, [CURLOPT_URL => $url, CURLOPT_POST => true, CURLOPT_CUSTOMREQUEST => 'DELETE']);
        //CURLFile support
        if (is_array($params)) {
            $hasUploadFile = false;
            if ($this->meetPhp55) {//CURLFile: since 5.5.0
                foreach ($params as $k => $v) {
                    if ($v instanceof \CURLFile) {
                        $hasUploadFile = true;
                        break;
                    }
                }
            }
            $hasUploadFile OR $params = http_build_query($params);
        }
        //$params: array => multipart/form-data, string => application/x-www-form-urlencoded
        if (!empty($params)) {
            curl_setopt($this->handle, CURLOPT_POSTFIELDS, $params);
        }
        if (!empty($headers)) {
            curl_setopt($this->handle, CURLOPT_HTTPHEADER, $headers);
        }
    }

    public function request($method, $url, $params = null, array $headers = [])
    {
        $this->init();
        $post = false;
        switch (strtolower($method))
        {
            case 'get':
                curl_setopt_array($this->handle, [CURLOPT_URL => $url, CURLOPT_HTTPGET => true]);
                break;
            case 'post':
            case 'delete':
            case 'put':
                $post = true;
                curl_setopt_array($this->handle, [CURLOPT_URL => $url, CURLOPT_POST => true]);
                break;
            default:
                curl_setopt_array($this->handle, [CURLOPT_URL => $url, CURLOPT_HTTPGET => true]);
                break;

        }

        //CURLFile support
        if (is_array($params)) {
            $hasUploadFile = false;
            if ($this->meetPhp55) {//CURLFile: since 5.5.0
                foreach ($params as $k => $v) {
                    if ($v instanceof \CURLFile) {
                        $hasUploadFile = true;
                        break;
                    }
                }
            }
            $hasUploadFile OR $params = http_build_query($params);
        }
        //$params: array => multipart/form-data, string => application/x-www-form-urlencoded
        if (!empty($params) && $post) {
            curl_setopt($this->handle, CURLOPT_POSTFIELDS, $params);
        }
        if (!empty($headers)) {
            curl_setopt($this->handle, CURLOPT_HTTPHEADER, $headers);
        }
    }

    public function exec()
    {
        if ($this->multi) {
            $responseStr = curl_multi_getcontent($this->handle);
        } else {
            $responseStr = curl_exec($this->handle);
        }
        $errno = curl_errno($this->handle);
        $errstr = curl_error($this->handle);//Fix: curl_errno() always return 0 when fail
        $url = curl_getinfo($this->handle, CURLINFO_EFFECTIVE_URL);
        $code = curl_getinfo($this->handle, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($this->handle, CURLINFO_HEADER_SIZE);
        $this->response = Response::make($url, $code, $responseStr, $headerSize, [$errno, $errstr]);
        return $this->response;
    }
    public function setMulti($isMulti)
    {
        $this->multi = (bool)$isMulti;
    }

    /**
     * @param $filename
     *
     * @return bool|int
     */
    public function responseToFile($filename)
    {
        $folder = dirname($filename);
        if (!file_exists($folder)) {
            if (!mkdir($folder, 0777, true) && !is_dir($folder))
            {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $folder));
            }
        }
        return file_put_contents($filename, $this->getResponse()->getBody());
    }
    public function getResponse()
    {
        return $this->response;
    }
    public function getHandle()
    {
        return $this->handle;
    }
    public function __destruct()
    {
        curl_close($this->handle);
    }

}

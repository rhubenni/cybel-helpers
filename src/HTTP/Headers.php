<?php declare(strict_types=1);

namespace Rtelesco\Cybel\HTTP;

class Headers
{
    
    use StatusCodes;
    
    /**
     * Send redirect header
     * @param string $to target URL
     * @param bool $permanent
     * @return void
     */
    public static function redirect(string $to, bool $permanent = false) : void
    {
        $code = ($permanent) ? 301 : 302;
        self::response_code($code);
        \header ("Location: " . $to);
        exit();
    }
    
    /**
     * Send refresh header to redirect user after timeout
     * @param string $to target URL
     * @param int $time timeout, in seconds
     * @param string $content optionally sends content to browser
     * @return void
     */
    public static function redirect_delay(string $to, int $time, string $content = "") : void
    {
        \header ("Refresh: " . $time . "; url=" . $to);
        print $content;
        exit();
    }
    
    /**
     * Closes http connection
     * @return void
     */
    public static function closeConn() : void
    {
        header("Connection: close");
    }
    
    /**
     * Set http response code
     * @param int $code
     * @return void
     */
    public static function response_code(int $code) : void {
        if(\array_key_exists($code, self::$Code)) {
            $desc = self::$Code[$code];
        } else {
            $desc = self::$Code[200];
        }
        $header = "HTTP/1.1 {$code} {$desc}";
        header($header, true, $code);
    }
    
    /**
     * Set http response code
     * @deprecated use response_code() instead
     */
    public static function status(int $code) : void
    {
        self::response_code($code);
    }
    
    /**
     * send headers to disable browser caching
     * @return void
     */
    public static function noCache() : void
    {
        header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
        header('Expires: Fri, 13 Dez 2013 05:00:00 GMT');
        header('Pragma: no-cache');
    }
    
    
    /**
     * Set headers to download file and sends the file
     * @param string $file
     * @param string $filename
     * @param bool $isStr
     * @return void
     */
    public static function sendDownload (string $file, string $filename, bool $isStr = false) : void {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"'); 
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        if($isStr === false) {
            header('Content-Length: ' . filesize($file));
        } else {
            header('Content-Length: ' . mb_strlen($file, '8bit'));
        }
        if($isStr === false) {
            readfile($file);
        } else {
            print $file;
        }
    }
}

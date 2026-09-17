<?php
class TApplicationFileSessionHandler implements SessionHandlerInterface
{
    private $savePath;
    public function open($savePath, $sessionName)
    {
        $this->savePath = $savePath ? $savePath : '/tmp';
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0777);
        }

        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($id)
    {
        $application = APPLICATION_NAME;
        return (string)@file_get_contents("{$this->savePath}/sess_{$application}_{$id}");
    }

    public function write($id, $data)
    {
        $application = APPLICATION_NAME;
        return file_put_contents("{$this->savePath}/sess_{$application}_{$id}", $data) === false ? false : true;
    }

    public function destroy($id)
    {
        $application = APPLICATION_NAME;
        $file = "{$this->savePath}/sess_{$application}_{$id}";
        if (file_exists($file)) {
            unlink($file);
        }

        return true;
    }

    public function gc($maxlifetime)
    {
        $application = APPLICATION_NAME;
        foreach (glob("{$this->savePath}/sess_{$application}_*") as $file) {
            clearstatcache(true, $file);
            if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
                unlink($file);
            }
        }

        return true;
    }
}

<?php namespace Ptilz\ProgExec;

abstract class Stream {
    /** @var resource */
    protected $handle;

    public function __construct($handle) {
        if(!is_resource($handle)) {
            throw new \Exception("Handle must be a resource");
        }
        $this->handle = $handle;
    }

    /**
     * Closes the stream.
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function close() {
        if($this->handle) {
            $ret = fclose($this->handle);
            unset($this->handle);
            return $ret;
        }
        return false;
    }

    function __destruct() {
        $this->close();
    }


}
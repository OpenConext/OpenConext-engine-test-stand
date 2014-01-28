<?php

namespace OpenConext\Bundle\ReplayToolsBundle\Command\Helper;

class LogStream
{
    const LINE_LENGTH = 1024;

    const STOP = false;

    protected $stream;

    public function __construct($stream)
    {
        $this->stream = $stream;
        $this->rewind();
    }

    public function foreachLine($fn)
    {
        while (!feof($this->stream)) {
            if ($fn(fgets($this->stream, static::LINE_LENGTH)) === false) {
                return $this;
            }
        }
        return $this;
    }

    public function foreachLineReverse($fn)
    {
        $line = '';
        $pos = -2;

        if (feof($this->stream)) {
            fseek($this->stream, -1, SEEK_CUR);
            $line = fgetc($this->stream);
        }

        while (fseek($this->stream, $pos, SEEK_CUR) !== -1) {
            $char = fgetc($this->stream);

            if ("\n" !== $char) {
                $line = $char . $line;
                continue;
            }
            $line = $line . $char;

            if ($fn($line) === static::STOP) {
                return $this;
            }

            $line = '';
        }
        $fn($line);

        $this->rewind();

        return $this;
    }

    public function write($content)
    {
        fwrite($this->stream, $content);
        return $this;
    }

    public function rewind()
    {
        rewind($this->stream);
        return $this;
    }

    public function close()
    {
        fclose($this->stream);
        return $this;
    }

    public function isEof()
    {
        return feof($this->stream);
    }

    public function onEof($fn)
    {
        if ($this->isEof()) {
            $fn();
        }
        return $this;
    }

    public function onNotEof($fn)
    {
        if (!$this->isEof()) {
            $fn();
        }
        return $this;
    }

    public function toString()
    {
        $this->rewind();
        return stream_get_contents($this->stream);
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function toStream()
    {
        return $this->stream;
    }

    public function __destruct()
    {
        fclose($this->stream);
    }
}

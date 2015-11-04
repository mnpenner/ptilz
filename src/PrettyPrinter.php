<?php namespace Ptilz;

use Ptilz\Exceptions\ArgumentOutOfRangeException;

class PrettyPrinter {
    /** @var resource */
    private $stream;
    /** @var string */
    private $indentStr = '  ';
    /** @var int */
    private $indentLevel = 0;
    /** @var bool */
    private $colorize = true;
    /** @var bool */
    private $newLine = true;
    /** @var string */
    private $lineSep = PHP_EOL;
    /** @var bool */
    private $wordWrap;

    function __construct($stream = STDOUT) {
        $this->stream = $stream;
        $this->colorize = $stream === STDOUT;
        $this->wordWrap = Env::isCli();
    }

    /**
     * Should output be ran through Cli::colorize?
     *
     * @param bool $colorize
     */
    public function setColored($colorize) {
        $this->colorize = (bool)$colorize;
    }

    /**
     * Set the indent level directly.
     *
     * @param int $level
     */
    public function setIndentLevel($level = 0) {
        if($level < 0) {
            throw new ArgumentOutOfRangeException('levels',"Indent level cannot be negative");
        }
        $this->indentLevel = $level;
    }

    /**
     * Separate lines using this string.
     *
     * @param string $sep
     */
    public function setLineSeparator($sep) {
        $this->lineSep = $sep;
    }

    /**
     * Set the indent string. This is prepended to the start of each line.
     *
     * @param string $indent
     */
    public function setIndentString($indent) {
        $this->indentStr = $indent;
    }

    /**
     * @param int $levels Number of levels to indent by
     */
    public function indent($levels = 1) {
        $this->setIndentLevel($this->indentLevel + $levels);
    }

    /**
     * @param int $levels Number of levels to unindent by
     */
    public function dedent($levels = 1) {
        $this->setIndentLevel($this->indentLevel - $levels);
    }

    /**
     * Write string to output stream.
     *
     * @param string $str
     */
    public function write($str) {
        if($this->wordWrap) { // FIXME: need to de-color before wrapping
            $width = Cli::width(100) - ($this->indentLevel * strlen($this->indentStr)) - 1;
            if($width > 0) {
                $str = wordwrap($str, $width);
            }
        }
        // TODO: add support for printing tables?
        $endsWithNl = (bool)preg_match('#\R\z#',$str);
        if($this->indentLevel > 0) {
            $lines = preg_split('#\R#', $str);
            $start = $this->newLine ? 0 : 1;
            $end = count($lines);
            if($endsWithNl) {
                --$end;
            }
            for($i = $start; $i < $end; ++$i) {
                $lines[$i] = str_repeat($this->indentStr, $this->indentLevel) . $lines[$i];
            }
            $str = implode($this->lineSep, $lines);
        }
        if($this->colorize) {
            $str = Cli::colorize($str);
        }
        fwrite($this->stream, $str);
        $this->newLine = $endsWithNl;
    }

    /**
     * Write string to output stream with trailing line separator.
     *
     * @param string $str
     */
    public function writeLine($str='') {
        $this->write($str.$this->lineSep);
    }
}
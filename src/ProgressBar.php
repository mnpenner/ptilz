<?php namespace Ptilz;

/**
 * CLI progress bar for long running scripts.
 */
class ProgressBar {
    private $current;
    private $max;
    private $width;
    private $start_time;
    private $last_str_length;
    private $last_render_time;
    private $last_line;


    /**
     * @param int|float $max Number to count up to; usually the total number of records to process
     * @param int $width Width of progress bar; leave room for % and ETA
     * @param bool $start Start timer immediately; otherwise you must call start()
     */
    public function __construct($max=1, $width=null, $start=true) {
        $this->width = $width ?: Cli::width(80)-25;
        $this->max = $max;
        if($start) $this->start();
    }

    /**
     * Start (or restart) timer used for ETA calculations
     */
    public function start() {
        $this->start_time = microtime(true);
        $this->render();
    }

    /**
     * Returns the first truthy argument, or last argument if none found
     * @static
     * @return mixed First truthy argument
     */
    public static function coalesce() {
        $args = func_get_args();
        $last = array_pop($args);
        foreach($args as $a) {
            if($a) return $a;
        }
        return $last;
    }

    /**
     * Increment the progressbar and re-render.
     */
    public function increment() {
        $this->update($this->current+1);
    }

    /**
     * Print the progressbar.
     */
    public function render() {
        $percent = $this->max ? min($this->current/$this->max,1) : 1;
        $now = microtime(true);
        if($percent < 1 && $this->last_render_time !== null && ($now - $this->last_render_time) < 0.033333) {
            return; // rate-limit to 30 FPS
        }
        $this->last_render_time = $now;
        $inner_width = $this->width;
        $dt = microtime(true) - $this->start_time;
        if($dt >= 5 && $percent >= 0.03 && $percent < 1) {
            $eta = (1-$percent)/$percent*$dt;
            $suffix = ' '.self::sec2time($eta);
        } else $suffix = '';

        $percent_str = Str::pad(floor($percent*100+.25),4,' ',STR_PAD_LEFT).'%';
        $bar_length = $percent*$inner_width;
        $full_bars = (int)floor($bar_length);
        $sub = "▏▎▍▌▋▊▉█";
        $rem = (int)round(($bar_length - $full_bars)*Str::length($sub));
        $filler = str_repeat('█',$full_bars);
        if($rem > 0) {
            $filler .= Str::substr($sub, $rem-1, 1);
        }
        $bar_str = '<bg:dark-grey;fg:bright-green>'.Str::pad($filler,$inner_width," ").'</>';
        $full_line = "$percent_str $bar_str$suffix";
        if($full_line === $this->last_line) {
            return;
        }
        $this->last_line = $full_line;
        $this->_writeLine($full_line);
    }

    /**
     * Update progress bar
     * @param int|float $progress Current item number
     */
    public function update($progress) {
        $this->current = $progress;
        $this->render();
    }

    /**
     * Sets progress bar to 100% complete
     */
    public function complete() {
        $this->update($this->max);
        echo "\n";
    }

    /**
     * Overwrite last line with new text
     * @param string $str String to print
     */
    private function _writeLine($str) {
        $len = Str::length(strip_tags($str));
        if($this->last_str_length) {
            echo "\r".Cli::colorize($str);
            $pad = $this->last_str_length - $len;
            if($pad > 0) {
                echo str_repeat(' ', $pad);
            }
//            echo Str::pad("\r".$str, $this->last_str_length, ' '); // pad with spaces so there's no residue left at the end
        }
        $this->last_str_length = $len;
    }

    /**
     * Write a line of text and push the progress bar down.
     *
     * @param string $text
     */
    public function writeLine($text) {
        $lines = preg_split('/\R/', rtrim($text));
        $this->_writeLine(array_shift($lines));
        echo PHP_EOL;
        foreach($lines as $line) {
            echo $line.PHP_EOL;
        }
        $this->render();
    }

    /**
     * Converts seconds into a human-readable string (with days, hours, minutes, seconds and ms)
     * @static
     * @param int $sec Seconds
     * @return string Formatted string
     */
    private static function sec2time($sec) {
        if($sec<0.00001) return 'done';
        $t = array();
        if($sec>=86400) $t[] = floor($sec/86400).'d';
        if($sec>=3600) $t[] = (floor($sec/3600)%24).'h';
        if($sec>=60) $t[] = (floor($sec/60)%60).'m';
        if($sec>=1) $t[] = (floor($sec)%60).'s';
        else $t[] = round($sec*1000).'ms';
        return implode(' ',$t);
    }
}
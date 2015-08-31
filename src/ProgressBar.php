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
        ++$this->current;
        $this->render();
    }

    /**
     * Print the progressbar.
     */
    public function render() {
        $percent = min($this->current/$this->max,1);
        $bar_width = $this->width-2;
        $dt = microtime(true) - $this->start_time;
        if($dt >= 5 && $percent >= 0.03 && $percent < 1) {
            $eta = (1-$percent)/$percent*$dt;
            $suffix = ' '.self::sec2time($eta);
        } else $suffix = '';
        $this->writeline(str_pad(floor($percent*100),4,' ',STR_PAD_LEFT).'% ['.str_pad(str_repeat('=',round($percent*$bar_width)),$bar_width,'.').']'.$suffix);
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
    private function writeline($str) {
        echo str_pad("\r".$str, $this->last_str_length, ' '); // pad with spaces so there's no residue left at the end
        $this->last_str_length = strlen($str)+1;
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
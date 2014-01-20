<?php
header("Content-type: text/plain; charset=utf-8");

$ffmpeg = 'ffmpeg';
$video  = '/mnt/NAS/Video/Film/The\ Doors\ \(1991\ -\ Oliver\ Stone\).avi';
$image  = '/var/www/demo.gif';
/*
$second = 1;
$cmd = "$ffmpeg -i $video 2>&1";
if (preg_match('/Duration: ((\d+):(\d+):(\d+))/s', `$cmd`, $time)) {
	$info["video"]["duration"] = $time[1];
	
	$total = ($time[2] * 3600) + ($time[3] * 60) + $time[4];
	$second = rand(1, ($total - 1));
}
if (preg_match('/bitrate: ((\d+) (\w+)\/(\w+))/s', `$cmd`, $bit)) {
	$info["video"]["bitrate"] = $bit[1];
}
if (preg_match('/Video: (.*?), (.*?), (.*?), (.*?) /s', `$cmd`, $bit)) {
	$info["video"]["codec"] = $bit[1];
	$info["video"]["color_profile"] = $bit[2];
	$info["video"]["size"] = $bit[3];
	$info["video"]["frames"] = $bit[4];
}
if (preg_match('/Audio: (.*?), (.*?), (.*?), (.*?), (.*?)\n/s', `$cmd`, $bit)) {
	$info["audio"]["format"] = $bit[1];
	$info["audio"]["frequency"] = $bit[2];
	$info["audio"]["channel"] = $bit[3];
	$info["audio"]["compression"] = $bit[4];
	$info["audio"]["framerate"] = $bit[5];
}
//$cmd = "$ffmpeg -i $video -deinterlace -an -ss $second -t 00:00:01 -r 1 -y -vcodec mjpeg -f mjpeg $image 2>&1";
$cmd = "$ffmpeg -vframes 1 -ss $second -i $video $image";
$return = `$cmd`;
print_r($info);
*/
class Thumbnail_Extractor implements Iterator
{
    /**
     * @var string path to ffmpeg binary
     */
    protected $ffmpeg = 'ffmpeg';

    /**
     * @var string path to video
     */
    protected $video;

    /**
     * @var array frames extracted from video
     */
    protected $frames = array();

    /**
     * @var string thumbnail size
     */
    protected $size = '';

    /**
     * @var integer video length
     */
    protected $duration = 0;

    /**
     * @var boolean A switch to keep track of the end of the array
     */
    private $valid = false;

    /**
     * Constructor
     *
     * @param string $video  path to source video
     * @param array  $frames array of frames extracted [array('10%', '30%', '50%', '70%', '90%')]
     * @param string $size   frame size [format: '320x260' or array(320,260)]
     * @param string $ffmpeg path to ffmpeg binary
     */
    public function __construct($video, $frames = array(), $size = '', $ffmpeg = 'ffmpeg') {
        $this->video  = escapeshellarg($video);
        $this->ffmpeg = escapeshellcmd($ffmpeg);
        $this->duration = $this->_getDuration();
        $this->_setSizeParam($size);
        $this->_setFrames($frames);
    }

    /**
     * Parse and set the frame size args to pass to ffmpeg
     *
     * @param string|array $size frame size [format: '320x260' or array(320,260)]
     *
     * @return void
     */
    private function _setSizeParam($size) {
        if (is_array($size) && 2 == count($size)) {
            $this->size = '-s '.(int)array_shift($size).'x'.(int)array_shift($size);
        } elseif (is_string($size) && preg_match('/^\d+x\d+$/', $size)) {
            $this->size = '-s '.$size;
        }
    }

    /**
     * Init the frames array
     *
     * @param mixed $frames If integer, take a frame every X seconds;
     *                      If array, take a frame for each array value,
     *                      which can be an integer (seconds from start)
     *                      or a string (percent)
     */
    private function _setFrames($frames) {
        if (empty($frames)) {
            // throw exception?
            return;
        }
        if (is_integer($frames)) {
            // take a frame every X seconds
            $interval = $frames;
            $frames = array();
            for ($pos=0; $pos < $this->duration; $pos += $interval) {
            	$frames[] = $pos;
            }
        }
        if (!is_array($frames)) {
            // throw exception?
            return;
        }
        // init the frames array
        foreach ($frames as $frame) {
            $this->frames[$frame] = null;
        }
    }

    /**
     * Get the video duration
     *
     * @return integer
     */
    private function _getDuration() {
        $cmd = "{$this->ffmpeg} -i {$this->video} 2>&1";
        if (preg_match('/Duration: ((\d+):(\d+):(\d+))/s', `$cmd`, $time)) {
            return ($time[2] * 3600) + ($time[3] * 60) + $time[4];
        }
        return 0;
    }

    /**
     * Get a video frame from a certain point in time
     *
     * @param integer $second seconds from start
     *
     * @return string binary image contents
     */
    private function getFrame($second) {
        $image = tempnam('/tmp', 'FRAME_');
        $out = escapeshellarg($image);
        $cmd = "{$this->ffmpeg} -i {$this->video} -deinterlace -an -ss {$second} -t 00:00:01 -r 1 -y {$this->size} -vcodec mjpeg -f mjpeg {$out} 2>&1";
        `$cmd`;
        $frame = file_get_contents($image);
        @unlink($image);
        return $frame;
    }

    /**
     * Get the second
     *
     * @param mixed $second if integer, it's taken as absolute time in seconds
     *                      from the start, otherwise it's supposed to be a percentual
     *
     * @return integer
     */
    private function getSecond($second) {
        if (false !== strpos($second, '%')) {
            $percent = (int)str_replace('%', '', $second);
            return (int)($percent * $this->duration / 100);
        }
        return (int)$second;
    }

    /**
     * Return the array "pointer" to the first element
     * PHP's reset() returns false if the array has no elements
     *
     * @return void
     */
    public function rewind() {
        $this->valid = (false !== reset($this->frames));
    }

    /**
     * Return the current array element
     *
     * @return string binary image contents
     */
    public function current() {
        if (is_null(current($this->frames))) {
            $k = $this->key();
            $second = $this->getSecond($k);
            $this->frames[$k] = $this->getFrame($second + 1);
        }
        return current($this->frames);
    }

    /**
     * Return the key of the current array element
     *
     * @return mixed
     */
    public function key() {
        return key($this->frames);
    }

    /**
     * Move forward by one
     * PHP's next() returns false if there are no more elements
     *
     * @return void
     */
    public function next() {
        $this->valid = (false !== next($this->frames));
    }

    /**
     * Is the current element valid?
     *
     * @return boolean
     */
    public function valid() {
        return $this->valid;
    }
}
class Thumbnail_Joiner
{
    /**
     * @var integer delay between images (in milliseconds)
     */
    protected $delay = 50;

    /**
     * @var array
     */
    protected $images = array();

    /**
     * @param integer $delay between images
     */
    public function __construct($delay = 50) {
        $this->delay = $delay;
    }

    /**
     * Load an image from file
     *
     * @param string $filename
     *
     * @return void
     */
    public function addFile($image) {
        $this->images[] = file_get_contents($image);
    }

    /**
     * Load an image
     *
     * @param string $image binary image data
     *
     * @return void
     */
    public function add($image) {
        $this->images[] = $image;
    }

    /**
     * Generate the animated gif
     *
     * @return string binary image data
     */
    public function get() {
        $animation = new Imagick();
        $animation->setFormat('gif');
        foreach ($this->images as $image) {
            $frame = new Imagick();
            $frame->readImageBlob($image);
            $animation->addImage($frame);
            $animation->setImageDelay($this->delay);
        }
        return $animation->getImagesBlob();
    }

    /**
     * Save the animated gif to file
     *
     * @param string $outfile output file name
     *
     * @return void
     */
    public function save($outfile) {
        file_put_contents($outfile, $this->get());
    }
}

// where ffmpeg is located, such as /usr/sbin/ffmpeg
$ffmpeg = '/usr/bin/avconv';

// extract one frame at 10% of the length, one at 30% and so on
$frames = array('10%', '30%', '50%', '70%', '90%');

// set the delay between frames in the output GIF
$joiner = new Thumbnail_Joiner(50);
// loop through the extracted frames and add them to the joiner object
foreach (new Thumbnail_Extractor($video, $frames, '200x120', $ffmpeg) as $key => $frame) {
    $joiner->add($frame);
}
$joiner->save($image);
?>
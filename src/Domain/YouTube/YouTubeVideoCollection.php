<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Domain\YouTube;

use ArrayIterator;

final class YouTubeVideoCollection
{
    /** @var YouTubeVideo[] $videos */
    private array $videos = [];

    public function add(YouTubeVideo $video): void
    {
        $this->videos[] = $video;
    }

    /** @return ArrayIterator<int,YouTubeVideo> */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->videos);
    }
}

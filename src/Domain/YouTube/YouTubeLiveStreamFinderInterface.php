<?php

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Domain\YouTube;

use SportClimbing\IfscCalendar\Domain\Event\Info\IFSCEventInfo;
use SportClimbing\IfscCalendar\Domain\Stream\LiveStream;

interface YouTubeLiveStreamFinderInterface
{
    public function findLiveStream(IFSCEventInfo $event, string $roundName): LiveStream;
}

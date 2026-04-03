<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Infrastructure\StartList;

enum IFSCStartListStatus: string
{
    case CONFIRMED_STATUS = 'confirmed';
    case NOT_ATTENDING_STATUS = 'not attending';

    public function isAttending(): bool
    {
        return $this !== self::NOT_ATTENDING_STATUS;
    }
}

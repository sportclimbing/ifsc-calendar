<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Round;

enum IFSCRoundStatus: string
{
    case CONFIRMED = 'confirmed';
    case ESTIMATED = 'estimated';

    public function isConfirmed(): bool
    {
        return $this === self::CONFIRMED;
    }
}

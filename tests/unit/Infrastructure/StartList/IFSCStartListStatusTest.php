<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\tests\Infrastructure\StartList;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use SportClimbing\IfscCalendar\Infrastructure\StartList\IFSCStartListStatus;

final class IFSCStartListStatusTest extends TestCase
{
    #[Test] public function confirmed_status_is_attending(): void
    {
        $this->assertTrue(IFSCStartListStatus::CONFIRMED_STATUS->isAttending());
    }

    #[Test] public function not_attending_status_is_not_attending(): void
    {
        $this->assertFalse(IFSCStartListStatus::NOT_ATTENDING_STATUS->isAttending());
    }
}

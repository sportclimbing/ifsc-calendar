<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Event;

use nicoSWD\IfscCalendar\Domain\Event\Exceptions\InvalidLeagueName;
use nicoSWD\IfscCalendar\Domain\Round\IFSCRound;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;
use nicoSWD\IfscCalendar\Domain\Starter\IFSCStarter;

final class IFSCEvent
{
    /** @var IFSCRound[] $rounds */
    public array $rounds;

    /**
     * @param IFSCRound[] $rounds
     * @param IFSCStarter[] $starters
     */
    public function __construct(
        public readonly IFSCSeasonYear $season,
        public readonly int $eventId,
        public readonly int $leagueId,
        public readonly string $leagueName,
        public readonly string $timeZone,
        public readonly string $eventName,
        public readonly string $location,
        public readonly string $country,
        public readonly ?string $poster,
        public readonly string $siteUrl,
        public readonly string $startsAt,
        public readonly string $endsAt,
        public readonly array $disciplines,
        array $rounds,
        public readonly array $starters = [],
    ) {
        $this->rounds = $rounds;
    }

    /** @throws InvalidLeagueName */
    public function normalizedName(): string
    {
        return sprintf('IFSC: %s - %s', $this->leagueName(), $this->location);
    }

    /** @throws InvalidLeagueName */
    private function leagueName(): string
    {
        if (preg_match('~(?<name>(?:World|Continental)\s+(?:Cup|Championships?))~', $this->eventName, $match)) {
            return $match['name'];
        }

        if (preg_match('~(?<name>Olympic\s+(?:Games|(Qualifier\sSeries)))~', $this->eventName, $match)) {
            return $match['name'];
        }

        throw new InvalidLeagueName('Unable to parse league name');
    }
}

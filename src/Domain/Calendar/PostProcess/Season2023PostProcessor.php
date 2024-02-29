<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Calendar\PostProcess;

use DateTime;
use DateTimeImmutable;
use Exception;
use nicoSWD\IfscCalendar\Domain\Event\IFSCEvent;
use nicoSWD\IfscCalendar\Domain\Event\IFSCRound;

final readonly class Season2023PostProcessor
{
    private const BERN_IFSC_EVENT_ID = 1301;

    /**
     * @param IFSCEvent[] $events
     * @return IFSCEvent[]
     * @throws Exception
     */
    public function process(array $events): array
    {
        foreach ($events as $event) {
            if ($this->isBernEvent($event)) {
                $event->rounds = $this->fetchBernEvents();
            }
        }

        return $events;
    }

    /**
     * @return IFSCRound[]
     * @throws Exception
     */
    private function fetchBernEvents(): array
    {
        return [
            $this->createRound(
                name: "Men's Boulder Qualification",
                startTime: "2023-08-01T09:00:00+02:00",
            ),
            $this->createRound(
                name: "Women's Lead Qualification",
                startTime: "2023-08-02T11:00:00+02:00",
            ),
            $this->createRound(
                name: "Men's Lead Qualification",
                startTime: "2023-08-03T08:30:00+02:00",
            ),
            $this->createRound(
                name: "Women's Boulder Qualification",
                startTime: "2023-08-03T15:30:00+02:00",
            ),
            $this->createRound(
                name: "Men's Boulder Semi-final",
                startTime: "2023-08-04T10:00:00+02:00",
            ),
            $this->createRound(
                name: "Men's Boulder Final",
                startTime: "2023-08-04T18:30:00+02:00",
            ),
            $this->createRound(
                name: "Women's Boulder Semi-final",
                startTime: "2023-08-05T10:00:00+02:00",
            ),
            $this->createRound(
                name: "Women's Boulder Final",
                startTime: "2023-08-05T18:30:00+02:00",
            ),
            $this->createRound(
                name: "Lead Semi-finals",
                startTime: "2023-08-06T10:00:00+02:00",
            ),
            $this->createRound(
                name: "Lead Finals",
                startTime: "2023-08-06T18:30:00+02:00",
            ),
            $this->createRound(
                name: "Women's Boulder & Lead Semi-final",
                startTime: "2023-08-09T09:00:00+02:00",
            ),
            $this->createRound(
                name: "Men's Boulder & Lead Semi-final",
                startTime: "2023-08-09T13:00:00+02:00",
            ),
            $this->createRound(
                name: "Boulder & Lead Semi-finals",
                startTime: "2023-08-09T20:30:00+02:00",
            ),
            $this->createRound(
                name: "Speed Qualifications",
                startTime: "2023-08-10T09:00:00+02:00",
            ),
            $this->createRound(
                name: "Speed Finals",
                startTime: "2023-08-10T20:00:00+02:00",
            ),
            $this->createRound(
                name: "Women's Boulder & Lead Final",
                startTime: "2023-08-11T19:00:00+02:00",
            ),
            $this->createRound(
                name: "Men's Boulder & Lead Final",
                startTime: "2023-08-12T16:00:00+02:00",
            ),
        ];
    }

    /** @throws Exception */
    private function createRound(string $name, string $startTime): IFSCRound
    {
        $startTime = new DateTime($startTime);

        return new IFSCRound(
            name: $name,
            streamUrl: null,
            startTime: DateTimeImmutable::createFromMutable($startTime),
            endTime: DateTimeImmutable::createFromMutable($startTime->modify('+3 hours')),
        );
    }

    private function isBernEvent(IFSCEvent $event): bool
    {
        return $event->eventId === self::BERN_IFSC_EVENT_ID;
    }
}

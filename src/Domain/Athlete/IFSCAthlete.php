<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Domain\Athlete;

final readonly class IFSCAthlete
{
    /**
     * @param IFSCAthleteDisciplinePodium[] $disciplinePodiums
     * @param IFSCAthleteDisciplinePodium[] $worldChampionshipsDisciplinePodiums
     * @param IFSCAthleteDisciplinePodium[] $continentalChampionshipsDisciplinePodiums
     * @param IFSCAthleteResult[] $allResults
     * @param IFSCAthleteCupRanking[] $cupRankings
     */
    public function __construct(
        public int $id,
        public string $firstName,
        public string $lastName,
        public ?string $birthday,
        public string $gender,
        public ?string $personalStory,
        public ?IFSCAthleteFederation $federation,
        public string $country,
        public string $flagUrl,
        public ?string $city,
        public ?int $age,
        public ?int $height,
        public ?string $instagram,
        public ?string $nickname,
        public ?string $spokenLanguages,
        public ?string $photoUrl,
        public ?string $actionPhotoUrl,
        public array $disciplinePodiums,
        public array $worldChampionshipsDisciplinePodiums,
        public array $continentalChampionshipsDisciplinePodiums,
        public array $allResults,
        public array $cupRankings,
    ) {
    }

    public function withInstagram(string $instagram): self
    {
        return clone($this, [
            'instagram' => $instagram,
        ]);
    }
}

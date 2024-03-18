<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Application\Command;

use Closure;
use Exception;
use nicoSWD\IfscCalendar\Application\UseCase\BuildCalendar\BuildCalendarRequest;
use nicoSWD\IfscCalendar\Application\UseCase\BuildCalendar\BuildCalendarResponse;
use nicoSWD\IfscCalendar\Application\UseCase\BuildCalendar\BuildCalendarUseCase;
use nicoSWD\IfscCalendar\Application\UseCase\FetchSeasons\FetchSeasonsUseCase;
use nicoSWD\IfscCalendar\Domain\Calendar\IFSCCalendarFormat;
use nicoSWD\IfscCalendar\Domain\Event\Exceptions\InvalidURLException;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeason;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Filesystem\Filesystem;

class BuildCommand extends Command
{
    public function __construct(
        private readonly FetchSeasonsUseCase $fetchSeasonsUseCase,
        private readonly BuildCalendarUseCase $buildCalendarUseCase,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('nicoswd:build-ifsc-calender')
            ->setDescription('Build a custom IFSC calender (.ics)')
            ->addOption('season', mode: InputOption::VALUE_OPTIONAL, description: 'IFSC Season')
            ->addOption('league', mode: InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, description: 'IFSC Leagues')
            ->addOption('format', mode: InputOption::VALUE_OPTIONAL, description: 'Output format', default: 'ics')
            ->addOption('output', mode: InputOption::VALUE_OPTIONAL, description: '.ics output file name', default: 'ifsc-calendar.ics')
        ;
    }

    /** @throws InvalidURLException */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $seasons = $this->getSeasons();

        $selectedSeason = $input->getOption('season');
        $selectedLeagues = $input->getOption('league');
        $fileName = $input->getOption('output');
        $format = $input->getOption('format');

        if (!$selectedSeason) {
            $selectedSeason = $this->getSelectedSeason($seasons, $helper, $input, $output);
        } elseif ($selectedSeason === 'current') {
            $selectedSeason = key($seasons);
        }

        $selectedSeason = (int) $selectedSeason;
        $leaguesByName = [];

        foreach ($seasons[$selectedSeason]->leagues as $league) {
            $leaguesByName[$league->name] = $league->id;
        }

        if (!$selectedLeagues) {
            $selectedLeagues = $this->getSelectedLeague($leaguesByName, $helper, $input, $output);
        }

        $leagueIds = [];

        foreach ($selectedLeagues as $name) {
            $leagueIds[] = $leaguesByName[$name];
        }

        $formats = array_map($this->createFormats(), explode(',', $format));
        $season = IFSCSeasonYear::from($selectedSeason);

        $pathInfo = pathinfo($fileName);

        try {
            $response = $this->buildCalendar($season, $leagueIds, $formats, $output);
        } catch (Exception $e) {
            $output->writeln(
                messages: "[+] Fatal error: {$e->getMessage()} in {$e->getFile()} on line {$e->getLine()}",
            );

            return self::FAILURE;
        }

        foreach ($formats as $format) {
            $fileName = "{$pathInfo['dirname']}/{$pathInfo['filename']}.{$format->value}";
            $this->saveCalendar($fileName, $response->calendarContents[$format->value], $output);
        }

        $output->writeln("[+] Done!");

        return self::SUCCESS;
    }

    /**
     * @param int[] $leagueIds
     * @param IFSCCalendarFormat[] $formats
     * @throws InvalidURLException
     */
    public function buildCalendar(
        IFSCSeasonYear $selectedSeason,
        array $leagueIds,
        array $formats,
        OutputInterface $output,
    ): BuildCalendarResponse {
        $output->writeln("[+] Fetching event info...");

        return $this->buildCalendarUseCase->execute(
            new BuildCalendarRequest(
                leagueIds: $leagueIds,
                season: $selectedSeason,
                formats: $formats,
            )
        );
    }

    /** @return IFSCSeason[] */
    private function getSeasons(): array
    {
        $seasons = [];

        foreach ($this->fetchSeasonsUseCase->execute()->seasons as $season) {
            $seasons[$season->name] = $season;
        }

        return $seasons;
    }

    public function getSelectedSeason(array $seasons, Helper $helper, InputInterface $input, OutputInterface $output): int
    {
        $seasonNames = array_keys($seasons);
        $seasonNames = array_slice($seasonNames, 0, 3);

        $question = new ChoiceQuestion(
            "Please select your season (defaults to $seasonNames[0])",
            $seasonNames,
            0
        );
        $question->setErrorMessage('Season %s is invalid.');

        return (int) $helper->ask($input, $output, $question);
    }

    public function getSelectedLeague(array $leaguesByName, Helper $helper, InputInterface $input, OutputInterface $output): array
    {
        $question = new ChoiceQuestion(
            'Please select a league (defaults to "' . key($leaguesByName) . '")',
            array_keys($leaguesByName),
            0
        );
        $question->setMultiselect(true);
        $question->setErrorMessage('League %s is invalid.');

        return $helper->ask($input, $output, $question);
    }

    private function saveCalendar(string $fileName, string $calendarContents, OutputInterface $output): void
    {
        $output->writeln("[+] Saved file as {$fileName}");

        $filesystem = new Filesystem();
        $filesystem->dumpFile($fileName, $calendarContents);
    }

    private function createFormats(): Closure
    {
        return static fn (string $format): IFSCCalendarFormat => IFSCCalendarFormat::from($format);
    }
}

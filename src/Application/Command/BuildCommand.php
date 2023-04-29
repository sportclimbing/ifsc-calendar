<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Application\Command;

use nicoSWD\IfscCalendar\Application\UseCase\BuildCalendar\BuildCalendarRequest;
use nicoSWD\IfscCalendar\Application\UseCase\BuildCalendar\BuildCalendarResponse;
use nicoSWD\IfscCalendar\Application\UseCase\BuildCalendar\BuildCalendarUseCase;
use nicoSWD\IfscCalendar\Application\UseCase\FetchSeasons\FetchSeasonsUseCase;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeason;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Filesystem\Filesystem;

final class BuildCommand extends Command
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
            ->addOption('league', mode: InputOption::VALUE_OPTIONAL, description: 'IFSC League')
            ->addOption('format', mode: InputOption::VALUE_OPTIONAL, description: 'Output format', default: 'ics')
            ->addOption('output', mode: InputOption::VALUE_OPTIONAL, description: '.ics output file name', default: 'ifsc-calendar.ics')
            ->addOption('skip-youtube-fetch', mode: InputOption::VALUE_OPTIONAL, description: 'Do not fetch stream URLs from YouTube API (requires API key)', default: false)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $seasons = $this->getSeasons();

        $selectedSeason = $input->getOption('season');
        $selectedLeague = $input->getOption('league');
        $fileName = $input->getOption('output');
        $format = $input->getOption('format');
        $skipYouTubeFetch = $input->getOption('skip-youtube-fetch') !== false;

        if (!$selectedSeason) {
            $selectedSeason = $this->getSelectedSeason($seasons, $helper, $input, $output);
        } elseif ($selectedSeason === 'current') {
            $selectedSeason = key($seasons);
        }

        $selectedSeason = (int) $selectedSeason;
        $leaguesByName = [];

        foreach ($seasons[$selectedSeason]->leagues as $league) {
            $leaguesByName[$league->name] = $league;
        }

        if (!$selectedLeague) {
            $selectedLeague = $this->getSelectedLeague($leaguesByName, $helper, $input, $output);
        }

        $league = $leaguesByName[$selectedLeague];

        foreach (explode(',', $format) as $calFormat) {
            $pathInfo = pathinfo($fileName);
            $fileName = "{$pathInfo['dirname']}/{$pathInfo['filename']}.{$calFormat}";

            $response = $this->buildCalendar($selectedSeason, [$league], $calFormat, $output, $skipYouTubeFetch);
            $this->saveCalendar($fileName, $response->calendarContents, $output);
        }

        $output->writeln("[+] Done!");

        return self::SUCCESS;
    }

    public function buildCalendar(
        int $selectedSeason,
        array $leagues,
        string $format,
        OutputInterface $output,
        bool $skipYouTubeFetch
    ): BuildCalendarResponse {
        $output->writeln("[+] Fetching event info...");

        return $this->buildCalendarUseCase->execute(
            new BuildCalendarRequest(
                season: $selectedSeason,
                leagues: $leagues,
                format: $format,
                skipYouTubeFetch: $skipYouTubeFetch,
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

    public function getSelectedLeague(array $leaguesByName, Helper $helper, InputInterface $input, OutputInterface $output): string
    {
        $question = new ChoiceQuestion(
            'Please select a or multiple leagues (defaults to "' . key($leaguesByName) . '")',
            array_keys($leaguesByName),
            0
        );
        $question->setMultiselect(false);
        $question->setErrorMessage('League %s is invalid.');

        return $helper->ask($input, $output, $question);
    }

    private function saveCalendar(string $fileName, string $calendarContents, OutputInterface $output): void
    {
        $output->writeln("[+] Saving file as $fileName...");

        $filesystem = new Filesystem();
        $filesystem->dumpFile($fileName, $calendarContents);
    }
}

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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
            ->addArgument('season', InputArgument::OPTIONAL, 'IFSC Season')
            ->addArgument('leagues', InputArgument::OPTIONAL, 'IFSC Leagues')
            ->addArgument('output', InputArgument::OPTIONAL, 'Calendar file name', 'ifsc-calendar.ics')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $seasons = $this->getSeasons();

        $selectedSeason = $input->getArgument('season');
        $selectedLeagues = $input->getArgument('leagues');
        $fileName = $input->getArgument('output');

        if (!$selectedSeason) {
            $selectedSeason = $this->getSelectedSeason($seasons, $helper, $input, $output);
        }

        $leaguesByName = [];

        foreach ($seasons[$selectedSeason]->leagues as $league) {
            $leaguesByName[$league->name] = $league;
        }

        if (!$selectedLeagues) {
            $selectedLeagues = $this->getSelectedLeague($leaguesByName, $helper, $input, $output);
        }

        $leagues = [];

        foreach ($selectedLeagues as $league) {
            $leagues[] = $leaguesByName[$league];
        }

        $response = $this->buildCalendar($selectedSeason, $leagues, $output);
        $this->saveCalendar($fileName, $response->calendarContents, $output);

        $output->writeln("[+] Done!");

        return self::SUCCESS;
    }

    public function buildCalendar(mixed $selectedSeason, array $leagues, OutputInterface $output): BuildCalendarResponse
    {
        $output->writeln("[+] Fetching event info...");

        return $this->buildCalendarUseCase->execute(
            new BuildCalendarRequest(
                season: $selectedSeason,
                leagues: $leagues,
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

    public function getSelectedSeason(array $seasons, Helper $helper, InputInterface $input, OutputInterface $output): mixed
    {
        $seasonNames = array_keys($seasons);
        $seasonNames = array_slice($seasonNames, 0, 3);

        $question = new ChoiceQuestion(
            "Please select your season (defaults to $seasonNames[0])",
            $seasonNames,
            0
        );
        $question->setErrorMessage('Season %s is invalid.');

        return $helper->ask($input, $output, $question);
    }

    public function getSelectedLeague(array $leaguesByName, Helper $helper, InputInterface $input, OutputInterface $output): array
    {
        $question = new ChoiceQuestion(
            'Please select a or multiple leagues (defaults to "' . key($leaguesByName) . '")',
            array_keys($leaguesByName),
            0
        );
        $question->setMultiselect(true);
        $question->setErrorMessage('League %s is invalid.');

        return $helper->ask($input, $output, $question);
    }

    private function saveCalendar(string $fileName, string $calendarContents, OutputInterface $output): void
    {
        $output->writeln("[+] Saving .ics file...");

        $filesystem = new Filesystem();
        $filesystem->dumpFile($fileName, $calendarContents);
    }
}

<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Application\Command;

use Closure;
use nicoSWD\IfscCalendar\Application\UseCase\BuildCalendar\BuildCalendarRequest;
use nicoSWD\IfscCalendar\Application\UseCase\BuildCalendar\BuildCalendarResponse;
use nicoSWD\IfscCalendar\Application\UseCase\BuildCalendar\BuildCalendarUseCase;
use nicoSWD\IfscCalendar\Application\UseCase\FetchSeasons\FetchSeasonsUseCase;
use nicoSWD\IfscCalendar\Domain\Calendar\IFSCCalendarFormat;
use nicoSWD\IfscCalendar\Domain\Event\Exceptions\InvalidURLException;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeason;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;
use Symfony\Component\Console\Command\Command;
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
            ->addOption('format', mode: InputOption::VALUE_OPTIONAL, description: 'Output format', default: 'ics')
            ->addOption('output', mode: InputOption::VALUE_OPTIONAL, description: '.ics output file name', default: 'ifsc-calendar.ics')
        ;
    }

    /** @throws InvalidURLException */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $seasons = $this->getSeasons();
        $selectedSeason = $this->getSelectedSeason($seasons, $input, $output);
        $formats = $this->getFormats($input);

        $calendar = $this->buildCalendar($selectedSeason, $formats, $output);
        $this->saveCalendarToFile($calendar, $formats, $input, $output);

        $output->writeln('[+] Done!');

        return self::SUCCESS;
    }

    /**
     * @param IFSCCalendarFormat[] $formats
     * @throws InvalidURLException
     */
    private function buildCalendar(
        IFSCSeasonYear $selectedSeason,
        array $formats,
        OutputInterface $output,
    ): BuildCalendarResponse {
        $output->writeln('[+] Started building calendar...');

        return $this->buildCalendarUseCase->execute(
            new BuildCalendarRequest(
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

    private function askForSeason(array $seasons, InputInterface $input, OutputInterface $output): int
    {
        $seasonNames = array_keys($seasons);
        $seasonNames = array_slice($seasonNames, 0, 3);

        $question = new ChoiceQuestion(
            "Please select your season (defaults to $seasonNames[0])",
            $seasonNames,
            0
        );
        $question->setErrorMessage('Season %s is invalid.');

        return (int) $this->getHelper('question')->ask($input, $output, $question);
    }

    private function getSelectedSeason(array $seasons, InputInterface $input, OutputInterface $output): IFSCSeasonYear
    {
        $selectedSeason = $input->getOption('season');

        if (!$selectedSeason) {
            $selectedSeason = $this->askForSeason($seasons, $input, $output);
        } elseif ($selectedSeason === 'current') {
            $selectedSeason = key($seasons);
        }

        return IFSCSeasonYear::from((int) $selectedSeason);
    }

    /** @param IFSCCalendarFormat[] $formats */
    private function saveCalendarToFile(
        BuildCalendarResponse $response,
        array $formats,
        InputInterface $input,
        OutputInterface $output,
    ): void {
        $pathInfo = pathinfo($input->getOption('output'));

        foreach ($formats as $format) {
            $fileName = sprintf('%s/%s.%s', $pathInfo['dirname'], $pathInfo['filename'], $format->value);

            $filesystem = new Filesystem();
            $filesystem->dumpFile($fileName, $response->calendarContents[$format->value]);

            $output->writeln("[+] Saved file as {$fileName}");
        }
    }

    private function getFormats(InputInterface $input): array
    {
        return array_map($this->createFormats(), explode(',', $input->getOption('format')));
    }

    private function createFormats(): Closure
    {
        return static fn (string $format): IFSCCalendarFormat => IFSCCalendarFormat::from($format);
    }
}

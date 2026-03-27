<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@ifsc.stream>
 */
namespace SportClimbing\IfscCalendar\Application\Command;

use Closure;
use JsonException;
use SportClimbing\IfscCalendar\Application\UseCase\BuildCalendar\BuildCalendarRequest;
use SportClimbing\IfscCalendar\Application\UseCase\BuildCalendar\BuildCalendarResponse;
use SportClimbing\IfscCalendar\Application\UseCase\BuildCalendar\BuildCalendarUseCase;
use SportClimbing\IfscCalendar\Domain\Calendar\IFSCCalendarFormat;
use SportClimbing\IfscCalendar\Domain\Event\Exceptions\InvalidURLException;
use SportClimbing\IfscCalendar\Domain\Season\IFSCSeasonYear;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Filesystem\Filesystem;

class BuildCommand extends Command
{
    public function __construct(
        private readonly BuildCalendarUseCase $buildCalendarUseCase,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('nicoswd:build-ifsc-calender')
            ->setDescription('Build a custom IFSC calender (.ics)')
            ->addOption('with-schedule', mode: InputOption::VALUE_REQUIRED, description: 'Path to a schedule JSON file')
            ->addOption('season', mode: InputOption::VALUE_OPTIONAL, description: 'IFSC Season')
            ->addOption('format', mode: InputOption::VALUE_OPTIONAL, description: 'Output format', default: 'ics')
            ->addOption('output', mode: InputOption::VALUE_OPTIONAL, description: '.ics output file name', default: 'ifsc-calendar.ics')
        ;
    }

    /** @throws InvalidURLException */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->validateSchedulePath($input, $output)) {
            return self::FAILURE;
        }

        $schedulePath = $this->getSchedulePath($input);
        $seasons = [2026];
        $selectedSeason = $this->getSelectedSeason($seasons, $input, $output);
        $formats = $this->getFormats($input);

        $calendar = $this->buildCalendar($selectedSeason, $formats, $schedulePath, $output);
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
        string $schedulePath,
        OutputInterface $output,
    ): BuildCalendarResponse {
        $output->writeln('[+] Started building calendar...');

        return $this->buildCalendarUseCase->execute(
            new BuildCalendarRequest(
                season: $selectedSeason,
                leagues: ['World Cups and World Championships', 'Games', 'IFSC Paraclimbing'],
                formats: $formats,
                schedulePath: $schedulePath,
            )
        );
    }

    /** @param array<int, int> $seasons */
    private function askForSeason(array $seasons, InputInterface $input, OutputInterface $output): int
    {
        $question = new ChoiceQuestion(
            "Please select your season (defaults to $seasons[0])",
            $seasons,
            0
        );
        $question->setErrorMessage('Season %s is invalid.');

        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        return (int) $helper->ask($input, $output, $question);
    }

    /** @param array<int, int> $seasons */
    private function getSelectedSeason(array $seasons, InputInterface $input, OutputInterface $output): IFSCSeasonYear
    {
        $selectedSeason = $input->getOption('season');

        if (!$selectedSeason) {
            $selectedSeason = $this->askForSeason($seasons, $input, $output);
        } elseif ($selectedSeason === 'current') {
            $selectedSeason = current($seasons);
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

    /** @return IFSCCalendarFormat[] */
    private function getFormats(InputInterface $input): array
    {
        return array_map($this->createFormats(), explode(',', $input->getOption('format')));
    }

    private function createFormats(): Closure
    {
        return static fn (string $format): IFSCCalendarFormat => IFSCCalendarFormat::from($format);
    }

    private function validateSchedulePath(InputInterface $input, OutputInterface $output): bool
    {
        $schedulePath = $input->getOption('with-schedule');

        if (!is_string($schedulePath) || trim($schedulePath) === '') {
            $output->writeln('<error>[x] Missing required option --with-schedule=/path/to/schedule.json</error>');

            return false;
        }

        if (strtolower((string) pathinfo($schedulePath, PATHINFO_EXTENSION)) !== 'json') {
            $output->writeln("<error>[x] Schedule file must be a .json file: {$schedulePath}</error>");

            return false;
        }

        if (!is_file($schedulePath)) {
            $output->writeln("<error>[x] Schedule file not found: {$schedulePath}</error>");

            return false;
        }

        if (!is_readable($schedulePath)) {
            $output->writeln("<error>[x] Schedule file is not readable: {$schedulePath}</error>");

            return false;
        }

        $json = file_get_contents($schedulePath);
        if ($json === false) {
            $output->writeln("<error>[x] Could not read schedule file: {$schedulePath}</error>");

            return false;
        }

        try {
            json_decode($json, associative: true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $output->writeln(sprintf('<error>[x] Invalid schedule JSON file: %s (%s)</error>', $schedulePath, $e->getMessage()));

            return false;
        }

        return true;
    }

    private function getSchedulePath(InputInterface $input): string
    {
        return (string) $input->getOption('with-schedule');
    }
}

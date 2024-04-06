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
use nicoSWD\IfscCalendar\Domain\Calendar\IFSCCalendarFormat;
use nicoSWD\IfscCalendar\Domain\Event\Exceptions\InvalidURLException;
use nicoSWD\IfscCalendar\Domain\Season\IFSCSeasonYear;
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
            ->addOption('season', mode: InputOption::VALUE_OPTIONAL, description: 'IFSC Season')
            ->addOption('format', mode: InputOption::VALUE_OPTIONAL, description: 'Output format', default: 'ics')
            ->addOption('output', mode: InputOption::VALUE_OPTIONAL, description: '.ics output file name', default: 'ifsc-calendar.ics')
        ;
    }

    /** @throws InvalidURLException */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $seasons = [2024];
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
                leagues: ['World Cups and World Championships', 'Games', 'IFSC Paraclimbing'],
                formats: $formats,
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
}

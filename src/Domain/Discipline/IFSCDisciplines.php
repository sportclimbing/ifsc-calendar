<?php declare(strict_types=1);

/**
 * @license  http://opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/nicoSWD
 * @author   Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\IfscCalendar\Domain\Discipline;

final readonly  class IFSCDisciplines
{
    /** @param IFSCDiscipline[] $disciplines */
    public function __construct(
        private array $disciplines,
    ) {
    }

    public function isSpeed(): bool
    {
        return $this->hasDiscipline(IFSCDiscipline::SPEED);
    }

    /** @return IFSCDiscipline[] */
    public function all(): array
    {
        return $this->disciplines;
    }

    public function hasDiscipline(IFSCDiscipline $discipline): bool
    {
        return in_array($discipline, $this->disciplines, strict: true);
    }
}

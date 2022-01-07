<?php
declare(strict_types=1);

namespace Otto\Infra\Logger;

use Argo\Domain\Log;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Stringable;

class Stdlog extends AbstractLogger
{
    protected array $stderrLevels = [
        LogLevel::EMERGENCY,
        LogLevel::ALERT,
        LogLevel::CRITICAL,
        LogLevel::ERROR,
    ];

    public function __construct(
        protected /* resource */ $stdout = null,
        protected /* resource */ $stderr = null,
    ) {
        $this->stdout ??= fopen('php://stdout', 'w');
        $this->stderr ??= fopen('php://stderr', 'w');
    }

    public function log(
        mixed $level,
        Stringable|string $message,
        array $context = [],
    ) : void
    {
        $handle = $this->stdout;

        if (in_array($level, $this->stderrLevels)) {
            $handle = $this->stderr;
        }

        fwrite($handle, $this->interpolate($message, $context));
    }

    protected function interpolate(
        Stringable|string $message,
        array $context
    ) : string
    {
        if (is_array($message)) {
            $message = implode(PHP_EOL, $message);
        }

        $replace = [];

        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }

        return strtr($message, $replace) . PHP_EOL;
    }
}

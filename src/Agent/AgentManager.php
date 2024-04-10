<?php

namespace B24\Devtools\Agent;

class AgentManager implements SerializerInterface
{
    private int $interval = 86400;
    private string $moduleId = '';
    private array $args = [];
    private string $className;
    private string $functionName;
    private ?string $date = null;

    public function __construct(
        private readonly string $periodic
    ) {}

    public function setInterval(int $interval): static
    {
        $this->interval = $interval;
        return $this;
    }

    public function getInterval()
    {
        return $this->interval;
    }

    public function isPeriodic(): bool
    {
        return $this->periodic === 'Y';
    }

    public function setModuleId(string $moduleId): static
    {
        $this->moduleId = $moduleId;
        return $this;
    }

    public function getModuleId(): string
    {
        return $this->moduleId;
    }

    public function setArguments(array $args): static
    {
        $this->args = $args;
        return $this;
    }

    public function getArguments(): array
    {
        return $this->args;
    }

    public function setHandler(string $className, string $staticFunctionName = 'handle'): static
    {
        $this->className = $className;
        $this->functionName = $staticFunctionName;
        return $this;
    }

    public function getFunctionName(): string
    {
        return $this->functionName;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function setDate(string $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getConsoleCommand(): string
    {
        $this->date = $this->date ?? date('d.m.Y H:i:s');

        $name = $this->className . '::' . $this->functionName . '(';

        if (!empty($this->args)) {
            $this->prepareArgs();

            $name .= implode(', ',  $this->args);
        }

        $name .= ');';

        return $name;
    }

    public function updateDate(): static
    {
        $this->date = date('d.m.Y H:i:s');
        return $this;
    }

    public function create(): void
    {
        $name = $this->getConsoleCommand();

        \CAgent::AddAgent(
            $name,
            $this->moduleId,
            $this->periodic,
            $this->interval,
            $this->date,
            'Y',
            $this->date
        );
    }

    private function prepareArgs(): void
    {
        foreach ($this->args as $key => $arg) {
            $this->args[$key] = $this->prepare($arg);
        }
    }

    private function prepare(mixed &$arg): mixed
    {
        if (is_string($arg)) {
            $value = "'" . $arg . "'";
        } elseif (is_int($arg) || is_float($arg)) {
            $value = $arg;
        } elseif (is_object($arg)) {
            Serializer::isSerialize($arg);
            $value = "'" . Serializer::serialize($arg) . "'";
        } else {
            throw new \Exception('allowed types int|float|string|object');
        }

        return $value;
    }

    public function __serialize(): array
    {
        return [
            'periodic' => $this->periodic,
            'interval' => $this->interval,
            'args' => $this->args,
            'moduleId' => $this->moduleId,
            'className' => $this->className,
            'functionName' => $this->functionName,
            'date' => $this->date,
        ];
    }

    public function __unserialize(array $data): void
    {
        foreach ($data as $k => $v) {
            $this->$k = $v;
        }
    }
}
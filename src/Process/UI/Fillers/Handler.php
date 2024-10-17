<?php

namespace B24\Devtools\Process\UI\Fillers;

class Handler extends AbstractFiller
{
    const StateChanged = 'StateChanged';
    const RequestStart = 'RequestStart';
    const RequestStop = 'RequestStop';
    const RequestFinalize = 'RequestFinalize';
    const StepCompleted = 'StepCompleted';

    const CALLBACK_TYPES = [
        self::StateChanged,
        self::RequestStart,
        self::RequestStop,
        self::RequestFinalize,
        self::StepCompleted,
    ];

    public readonly string $callbackType;
    public readonly string $callableBody;


    public function __construct(
        string $callbackType,
        string $body
    )
    {
        $this->callableBody = trim($body);
        $this->prepareCallbackType($callbackType);
    }

    private function prepareCallable(callable $callable)
    {
        debug(json_encode($callable(...)));
    }

    private function prepareCallbackType(string $callbackType): void
    {
        if (!in_array($callbackType, self::CALLBACK_TYPES)) {
            $callbackType = self::StateChanged;
        }

        $this->callbackType = $callbackType;
    }
}
<?php

namespace B24\Devtools\Process\UI;

use B24\Devtools\Process\UI\Fillers\AbstractFiller;
use B24\Devtools\Process\UI\Fillers\Buttons;
use B24\Devtools\Process\UI\Fillers\Field;
use B24\Devtools\Process\UI\Fillers\FillerInterface;
use B24\Devtools\Process\UI\Fillers\Handler;
use B24\Devtools\Process\UI\Fillers\Messages;
use B24\Devtools\Process\UI\Fillers\Queue;
use Bitrix\Main\Web\Json;
use Bitrix\Main\UI\Extension;

class StepProcessing extends AbstractFiller
{
    private array $queue = [];
    private ?array $params = null;
    private ?array $optionsFields = null;
    private ?array $handlers = null;

    public function __construct(
        private string $id,
        private string $controller,
        private Messages $messages
    )
    {
        Extension::load([
            'ui.stepprocessing'
        ]);
    }

    public function setQueue(Queue ...$queues): static
    {
        foreach ($queues as $queue) {
            $this->queue[] = $queue->toArray();
        }

        return $this;
    }

    public function setHandlers(Handler ...$handlers): static
    {
        $this->handlers = $handlers;
        return $this;
    }

    public function setButtons(Buttons $buttons): static
    {
        $this->showButtons = $buttons;
        return $this;
    }

    public function optionsFields(Field ...$fields): static
    {
        $data = [];
        foreach ($fields as $field) {
            $data = array_merge($data, $field->toArray());
        }

        $this->optionsFields = $data;
        return $this;
    }

    public function initJS(): void
    {
        echo '<script>' . $this->toJsObject() . '</script>';
    }

    public function showDialog(): string
    {
        return "BX.UI.StepProcessing.ProcessManager.get('" . $this->id . "').showDialog()";
    }

    public function toJsObject(): string
    {
        $string = 'BX.UI.StepProcessing.ProcessManager.create(' . $this->toJson() . ')';

        if ($this->handlers !== null) {
            foreach ($this->handlers as $handler) {
                $string .= '.setHandler(' . "\n";
                /**
                 * @var Handler $handler
                 */
                $string .= "'$handler->callbackType'" . ',' . "\n";
                $string .= $handler->callableBody . "\n";

                $string .= ')';
            }
        }

        return $string;
    }

    public function setParams(array $params): static
    {
        $this->params = $params;
        return $this;
    }

    public function toArray(): array
    {
        $data = [];

        foreach ($this as $k => $v) {
            if ($v === null || $k === 'handlers' || $k === 'toSnakeCase') {
                continue;
            }

            if (is_array($v)) {
                $v = $this->recursiveToArray($v);
            }

            if ($v instanceof FillerInterface) {
                $v = $v->toArray();
            }

            $data[$k] = $v;
        }

        return $data;
    }

    public function toJson(): string
    {
        return Json::encode($this->toArray());
    }

    private function recursiveToArray(array $array): array
    {
        $data = [];

        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $v = $this->recursiveToArray($v);
            } elseif ($v instanceof FillerInterface) {
                $v = $v->toArray();
            }

            $data[$k] = $v;
        }

        return $data;
    }
}
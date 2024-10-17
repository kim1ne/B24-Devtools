<?php

namespace B24\Devtools\Process\UI\Fillers;

class Messages extends AbstractFiller
{
    public function __construct(
        public readonly string $DialogTitle,
        public readonly string $DialogSummary,
        public readonly string $DialogStartButton,
        public readonly string $DialogStopButton,
        public readonly string $DialogCloseButton,
        public readonly string $RequestCanceling,
        public readonly string $RequestCanceled,
        public readonly string $RequestCompleted,
        public readonly string $DialogExportDownloadButton,
        public readonly string $DialogExportClearButton,
    ) {}
}
<?php

namespace App\Http\Controllers\Actions\Panel;

use App\Http\Validators\ValidatorInterface;
use Ecotone\Modelling\CommandBus;
use Hyn\Tenancy\Facades\TenancyFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

abstract class PanelCommandActionBase extends PanelActionBase
{
    protected const bool TRANSACTION = true;

    protected CommandBus $commandBus;

    protected string $connection;

    public function __construct(Request $request)
    {
        $this->commandBus = app(CommandBus::class);
        $this->connection = TenancyFacade::website() ? 'tenant' : 'system';

        parent::__construct($request);
    }

    public function __invoke(): void
    {
        $this->getValidator()?->run();

        static::TRANSACTION ? $this->handleWithTransaction() : $this->handle();
    }

    protected function getValidator(): ?ValidatorInterface
    {
        return null;
    }

    protected function handleWithTransaction(): void
    {
        DB::connection($this->connection)->transaction(function () {
            $this->handle();
        });
    }
}

<?php

namespace Filament\Tables\Testing;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\Column;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Testing\Assert;
use Livewire\Testing\TestableLivewire;

/**
 * @method HasTable instance()
 *
 * @mixin TestableLivewire
 */
class TestsRecords
{
    public function assertCanSeeTableRecords(): Closure
    {
        return function (array | Collection $records, bool $inOrder = false): static {
            $livewire = $this->instance();

            $html = array_map(
                function ($record) use ($livewire) {
                    if ($record instanceof Model) {
                        $record = $livewire->getTableRecordKey($record);
                    }

                    return "wire:key=\"table.records.{$record}\"";
                },
                $records instanceof Collection ? $records->all() : $records,
            );

            if ($inOrder) {
                $this->assertSeeHtmlInOrder($html);
            } else {
                $this->assertSeeHtml($html);
            }

            return $this;
        };
    }

    public function assertCanNotSeeTableRecords(): Closure
    {
        return function (array | Collection $records): static {
            $livewire = $this->instance();

            $html = array_map(
                function ($record) use ($livewire) {
                    if ($record instanceof Model) {
                        $record = $livewire->getTableRecordKey($record);
                    }

                    return "wire:key=\"table.records.{$record}\"";
                },
                $records instanceof Collection ? $records->all() : $records,
            );

            $this->assertDontSeeHtml($html);

            return $this;
        };
    }

    public function assertCountTableRecords(): Closure
    {
        return function (int $count): static {
            $tableClass = $this->instance()::class;

            Assert::assertSame(
                $count,
                $this->instance()->getAllTableRecordsCount(),
                message: "Failed asserting that the [{$tableClass}] table has [{$count}] records in total.",
            );

            return $this;
        };
    }
}

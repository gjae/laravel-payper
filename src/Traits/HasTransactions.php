<?php

namespace Gjae\LaravelPayper\Traits;

trait HasTransactions
{
    public function transactions()
    {
        return $this->morphMany(\Gjae\LaravelPayper\Models\ModelHasTransaction::class, 'model');
    }
}
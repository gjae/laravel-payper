<?php

namespace Gjae\LaravelPayper\Contracts;

use Gjae\LaravelPayper\Contracts\IPayable;
interface PayperRepository {

    public function findById(int $id);
    public function save(array $data, IPayable $payable);
    public function update(array $data, int $id);
    public function findByUUID(string $uuid);
    public function delete($id);

}
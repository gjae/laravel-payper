<?php

namespace Gjae\LaravelPayper\Repositorys;
use Gjae\LaravelPayper\Contracts\PayperRepository as Repository; 
use Gjae\LaravelPayper\Contracts\IPayable;
use Gjae\LaravelPayper\Models\PayperPayment;
class PayperRepository implements Repository{

    private $model = null;

    public function __construct()
    {
        $this->model = new PayperPayment();
    }

    public function findById($id){

    }

    public function save(array $data, IPayable $payable)
    {
        return $this->model = $payable->payments()->save(
            new PayperPayment($data)
        );
    }

    public function update(array $data, int $id)
    {

    }

    public function findByUUID(string $uuid)
    {
        return $this->model = $this->model->whereReferencia( $uuid )->first();
    }

    public function delete($id)
    {

    }

    public function getValor()
    {
        return $this->model->valor;
    }

    public function getStatus()
    {

    }

    public function reference() : string
    {
        return $this->model->referencia;
    }

}
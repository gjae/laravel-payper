<?php

namespace Gjae\LaravelPayper\Contracts;

interface IPayer {

    public function getCustomerFirstname(): ?string;
    public function getCustomerLastname(): ?string;
    public function getCustomerEmail(): ?string;
    public function getCustomerAddress(): ?string;
    public function getCustomerPhone(): ?string;

}
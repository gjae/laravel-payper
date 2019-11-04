<?php

namespace Gjae\LaravelPayper\Contracts;

interface GatewayInterface 
{
    public function getResponseStatusCode();
    public function getAuthResponse();
    public function getBatchId();
    public function getExtraData();
    public function getResponseDescription();
    public function getTranNbr();
}
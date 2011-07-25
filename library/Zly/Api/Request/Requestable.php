<?php

namespace Slys\Api\Request;

interface Requestable
{
    public function onRequest(\Slys\Api\Request $request);
}
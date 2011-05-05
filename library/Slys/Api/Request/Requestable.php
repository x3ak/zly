<?php

namespace Slys\Api\Request;

interface Requestable
{
    public function onRequest(Request $request);
}
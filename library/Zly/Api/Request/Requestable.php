<?php

namespace Zly\Api\Request;

interface Requestable
{
    public function onRequest(\Zly\Api\Request $request);
}
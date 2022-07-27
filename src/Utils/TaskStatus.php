<?php

namespace App\Utils;

enum TaskStatus: string
{
    case finish = 'finish';
    case active = 'active';
    case notFinish = 'notFinish';
}
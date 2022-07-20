<?php

namespace App\Utils;

enum PriorityTask : int
{
    case Low = 1;
    case Medium = 2;
    case High = 3;
}
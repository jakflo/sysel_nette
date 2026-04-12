<?php
namespace App;

enum BootstrapType
{
    case normal;
    case test;
    case ciTest;
    case console;
}

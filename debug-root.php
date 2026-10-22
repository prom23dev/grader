<?php

header('Content-Type: text/plain; charset=utf-8');

echo "fresh_install debug\n";
echo "__DIR__: " . __DIR__ . "\n";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? '[not set]') . "\n";
echo "SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? '[not set]') . "\n";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? '[not set]') . "\n";
echo "SERVER_SOFTWARE: " . ($_SERVER['SERVER_SOFTWARE'] ?? '[not set]') . "\n";


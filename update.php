<?php
/**
 * Script to self update disposible email address domains
 *
 * $ composer update;
 * $ php update.php;
 */
require ('vendor/autoload.php');

$climate = new League\CLImate\CLImate();
$domains = new EmailData\Domains();

// Load Domains From Sources
$climate->info('Updating...');
$domains->fetch();
$climate->br();

// Process
$climate->info('Processing...');
$domains->process();
$climate->br();

// Write To Files
$climate->info('Writing to Files...');
$domains->export();
$climate->br();

// Report
$climate->info('Report');
$climate->commentColumns([
    ['Total Domains', $domains->getCount()],
    ['New Domains', $domains->getNewCount()],
]);

$climate->br();
$climate->info('Done.');

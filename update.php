<?php
/**
 * Script to self update disposible email address domains
 *
 * $ composer update;
 * $ php update.php;
 */

require ('vendor/autoload.php');

$climate = new League\CLImate\CLImate();
$domains = new EmailData\Domains(require('data/disposable.php'));

// =============================================================================
// Sources
// =============================================================================
$sourcesNewlines = [
    'https://gist.githubusercontent.com/adamloving/4401361/raw/66688cf8ad890433b917f3230f44489aa90b03b7/temporary-email-address-domains',
    'https://raw.githubusercontent.com/martenson/disposable-email-domains/master/disposable_email_blacklist.conf',
    'https://raw.githubusercontent.com/lavab/disposable/master/domains.txt',
    'https://raw.githubusercontent.com/aaronbassett/DisposableEmailChecker/master/disposable_email_domains.txt',
    'https://raw.githubusercontent.com/MattKetmo/EmailChecker/master/res/throwaway_domains.txt',
    'https://raw.githubusercontent.com/flotwig/disposable-email-addresses/master/domains.txt',
];

$sourcesJson = [
    'https://raw.githubusercontent.com/ivolo/disposable-email-domains/master/index.json',
    'https://raw.githubusercontent.com/mawelous/disposable/master/source/database/all.json'
];

// =============================================================================
// Load Domains From Sources
// =============================================================================
$climate->info('Updating...');
// Newline
foreach ($sourcesNewlines as $url) {
    $climate->comment($url);
    $domains->add(EmailData\Getter\Newline::get($url));
}

// JSON
foreach ($sourcesJson as $url) {
    $climate->comment($url);
    $domains->add(EmailData\Getter\Json::get($url));
}
$climate->br();

// =============================================================================
// Process
// =============================================================================
$climate->info('Processing...');
$domains->process();

$climate->br();

// =============================================================================
// Write To Files
// =============================================================================
$climate->info('Writing to Files...');

$climate->comment('data/disposable.php');
EmailData\Exporter\Php::save('data/disposable.php', $domains->getDomains());

$climate->comment('data/disposable.txt');
EmailData\Exporter\Text::save('data/disposable.txt', $domains->getDomains());

$climate->comment('data/disposable.json');
EmailData\Exporter\Json::save('data/disposable.json', $domains->getDomains());

$climate->br();

// =============================================================================
// Report
// =============================================================================
$climate->info('Report');
$climate->commentColumns([
    ['Total Domains', $domains->getCount()],
    ['New Domains', $domains->getNewCount()],
]);

$climate->br();
$climate->info('Done.');

<?php

/**
 * Script to self update disposible email address domains
 *
 * $ composer update;
 * $ php update.php;
 */

require ('vendor/autoload.php');
use \Curl\Curl;

function output($str = '') {
	echo "\t" . $str . PHP_EOL;
}

$curl = new Curl();

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
// Load Existing Domains
// =============================================================================
$domains = require('data/disposable.php');

// =============================================================================
// Intro
// =============================================================================
output();
output('Updating disposable email address domains...');
output();


// =============================================================================
// Load Domains From Sources
// =============================================================================
// Newlines
output('Plain Text');
foreach ($sourcesNewlines as $url) {
	output('Loading: ' . $url);
	$content = $curl->get($url);
	if ($content) {
		$newDomains = preg_split('/\r\n|\r|\n/', $content);
		$domains = array_merge($domains, $newDomains);
	}
}
output();

// JSON
output('JSON');
foreach ($sourcesJson as $url) {
	output('Loading: ' . $url);
	$content = $curl->get($url);
	if ($content) {
		$newDomains = json_decode($content, true);

		if (isset($newDomains['hosts'])) {
			$newDomains = $newDomains['hosts'];
		}
	}

	$domains = array_merge($domains, $newDomains);
}
output();

// =============================================================================
// Sanitise
// =============================================================================
foreach ($domains as $key => $domain) {
	// Replace *.example.com with just example
	if (strpos($domain, '*.') === 0) {
		$domain = substr($domain, 2);
	}

	// Doesn't handle wild cards yet.
	if (strpos($domain, '*') !== false) {
		unset($domains[$key]);
		continue;
	}

	// Trim and lowercase
	$domains[$key] = trim(strtolower($domain));
}

// =============================================================================
// Dedupe
// =============================================================================
$preDedupeCount = count($domains);
$domains = array_unique($domains);
$postDedupeCount = count($domains);

// Remove indexes
$domains = array_values($domains);

// =============================================================================
// Write To Files
// =============================================================================
// - New Line Seperated
$domainsTxt = implode("\n", $domains);
file_put_contents('data/disposable.txt', $domainsTxt);

// - PHP array
// The funky code removes array indexes
$domainsPhp = '<?php ' . PHP_EOL;
$domainsPhp .= 'return ' . preg_replace('/\s+[0-9]+\s+=>\s+/i', PHP_EOL . '    ', var_export($domains, true));
$domainsPhp .= ';' . PHP_EOL;;
file_put_contents('data/disposable.php', $domainsPhp);

// - JSON
file_put_contents('data/disposable.json', json_encode($domains));


// =============================================================================
// Report
// =============================================================================
output($preDedupeCount . ' raw domains found');
output();
output($postDedupeCount . ' distinct domains found');
output();
output('File Written To');
foreach (glob('data/*.*') as $file) {
	output($file);
}
output();
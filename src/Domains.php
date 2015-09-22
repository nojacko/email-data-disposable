<?php
namespace EmailData;

class Domains
{
    private $pathBin = '';
    private $pathData = '';

    private $domains = [];
    private $domainCountStart = 0;

    private $whitelist = [];

    private $domainsProcessed = [];
    private $domainsProcessedCount = 0;

    public function __construct()
    {
        $this->data = new Data();
        $this->climate = new \League\CLImate\CLImate();

        $this->domains = $this->data->loadDomains();
        $this->domainCountStart = count($this->domains);

        $this->whitelist = $this->data->loadWhitelist();
    }

    public function fetch()
    {
        // Sources
        $sourcesNewlines = $this->data->loadSources('blacklist', 'newline');
        $sourcesJson = $this->data->loadSources('blacklist', 'json');
        $whitelistsNewlines = $this->data->loadSources('whitelist', 'newline');
        $sources = count($sourcesNewlines) + count($sourcesJson) + count($whitelistsNewlines);

        $progress = $this->climate->progress()->total($sources);

        // Blacklists
        // - Newline
        foreach ($sourcesNewlines as $url) {
            $this->domains = array_merge($this->domains, Getter\Newline::get($url));
            $progress->advance();
        }

        // - JSON
        foreach ($sourcesJson as $url) {
            $this->domains = array_merge($this->domains, Getter\Json::get($url));
            $progress->advance();
        }

        // Blacklists
        // - Newline
        foreach ($whitelistsNewlines as $url) {
            $this->whitelist = array_merge($this->whitelist, Getter\Newline::get($url));
            $progress->advance();
        }
    }

    public function process()
    {
        $this->domainsProcessed = [];

        // Sanitise
        $this->climate->comment('Sanitising...');
        $this->santise();

        // Sort
        $this->climate->comment('Sorting...');
        asort($this->domainsProcessed);

        // De-duplicate
        $this->climate->comment('De-duplicating...');
        $this->domainsProcessed = array_unique($this->domainsProcessed);

        // Remove Index Keys
        $this->climate->comment('Removing indexes...');
        $this->domainsProcessed = array_values($this->domainsProcessed);

        // Remove Whitelisted
        $this->climate->comment('Remove whitelists...');
        $this->domainsProcessed = array_diff($this->domainsProcessed, $this->whitelist);

        // Count
        $this->domainsProcessedCount = count($this->domainsProcessed);
    }

    private function santise()
    {
        foreach ($this->domains as $key => $domain) {
            // Replace *.example.com with just example.com
            if (strpos($domain, '*.') === 0) {
                $domain = substr($domain, 2);
            }

            // Doesn't handle wild cards yet.
            if (strpos($domain, '*') !== false) {
                continue;
            }

            // Minimum Length
            // x.yy is sortest domain possible.
            if (empty($domain) || mb_strlen($domain) < 4) {
                continue;
            }

            // Trim and lowercase
            $this->domainsProcessed[] = trim(strtolower($domain));
        }

        $this->domainsProcessedCount = count($this->domainsProcessed);
    }

    public function export()
    {
        $this->climate->comment('data/disposable.php');
        Exporter\Php::save('data/disposable.php', $this->domainsProcessed);

        $this->climate->comment('data/disposable.txt');
        Exporter\Text::save('data/disposable.txt', $this->domainsProcessed);

        $this->climate->comment('data/disposable.json');
        Exporter\Json::save('data/disposable.json', $this->domainsProcessed);
    }

    public function getCount()
    {
        return $this->domainsProcessedCount;
    }

    public function getNewCount()
    {
        return $this->domainsProcessedCount - $this->domainCountStart;
    }
}

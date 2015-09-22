<?php
namespace EmailData;

class Domains
{
    private $domains = [];
    private $domainCountStart = 0;

    private $domainsProcessed = [];
    private $domainsProcessedCount = 0;

    public function __construct($domains)
    {
        $this->domains = $domains;
        $this->domainCountStart = count($this->domains);
    }

    public function add($domains)
    {
        $this->domains = array_merge($this->domains, $domains);
    }

    public function process()
    {
        $this->domainsProcessed = [];

        // Sanitise
        $this->santise();

        // Sort
        asort($this->domainsProcessed);

        // De-duplicate
        $this->domainsProcessed = array_unique($this->domainsProcessed);

        // Remove Index Keys
        $this->domainsProcessed  = array_values($this->domainsProcessed);

        // Count
        $this->domainsProcessedCount = count($this->domainsProcessed);
    }

    public function getDomains()
    {
        return $this->domainsProcessed;
    }

    public function getCount()
    {
        return $this->domainsProcessedCount;
    }

    public function getNewCount()
    {
        return $this->domainsProcessedCount - $this->domainCountStart;
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
}

<?php

namespace VanillaCookieConsent\Tasks;
use SilverStripe\Dev\BuildTask;
use SilverStripe\PolyExecution\PolyOutput;
use SilverStripe\SiteConfig\SiteConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use VanillaCookieConsent\Models\Insight;

class ClearConsentInsightsTask extends BuildTask
{

    protected function execute(InputInterface $input, PolyOutput $output): int
    {
        $savePeriod = SiteConfig::current_site_config()->SavePeriodForInsights;
        $insights = Insight::get();

        if(!$savePeriod || $savePeriod <= 0) {
            $this->msg('No save period set, skipping task - Insights disabled start to delete all existing insights...');
            $insights->removeAll();
            $this->msg('All existing insights deleted.');
            return Command::SUCCESS;
        }

        $insights = $insights->filter([
            'Created:LessThan' => date('Y-m-d H:i:s', strtotime('-' . $savePeriod . ' days'))
        ]);

        $count = $insights->count();
        if ($count > 0) {
            $insights->removeAll();
            $this->msg('Insights older than ' . $savePeriod . ' days cleaned up (' . $count . ')');
        } else {
            $this->msg('No insights older than ' . $savePeriod . ' days found.');
        }

        return Command::SUCCESS;
    }

    protected function msg($msg) {
        // Handle message output if CLI or web
        if (php_sapi_name() === 'cli') {
            echo $msg . PHP_EOL;
        } else {
            echo '<p>' . $msg . '</p>';
        }
    }
}

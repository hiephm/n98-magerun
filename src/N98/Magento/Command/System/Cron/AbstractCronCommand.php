<?php

namespace N98\Magento\Command\System\Cron;

use N98\Magento\Command\AbstractMagentoCommand;

abstract class AbstractCronCommand extends AbstractMagentoCommand
{
    /**
     * @return array
     */
    protected  function getJobs()
    {
        $table = array();

        foreach (\Mage::getConfig()->getNode('crontab/jobs')->children() as $job) {
            $table[(string) $job->getName()] = array('Job'  => (string) $job->getName()) + $this->getSchedule($job);
        }

        ksort($table);

        return $table;
    }

    /**
     * @param $job
     * @return array
     */
    protected function getSchedule($job)
    {
        $expr = (string) $job->schedule->cron_expr;
        if ($expr) {
            if ($expr == 'always') {
                return array('m' => '*', 'h' => '*', 'D' => '*', 'M' => '*', 'WD' => '*');
            }

            $schedule = $this->_getModel('cron/schedule', 'Mage_Cron_Model_Schedule');
            $schedule->setCronExpr($expr);
            $array = $schedule->getCronExprArr();
            return array(
                'm'  => $array[0],
                'h'  => $array[1],
                'D'  => $array[2],
                'M'  => $array[3],
                'WD' => $array[4]
            );
        }

        return array('m' => '  ', 'h' => '  ', 'D' => '  ', 'M' => '  ', 'WD' => '  ');
    }
}
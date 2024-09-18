<?php

namespace Mautic\CoreBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendCampaignCommand extends Command
{
    protected static $defaultName = 'mautic:send-emails';

    private $emailModel;
    private $entityId;
    private $lists;
    private $limit;
    private $batch;

    public function __construct($entityId, $lists, $limit, $batch)
    {
        $this->entityId = $entityId;
        $this->lists    = $lists;
        $this->limit    = $limit;
        $this->batch    = $batch;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Send emails to lists')
            ->addArgument('entity', InputArgument::REQUIRED, 'The ID of the email entity')
            ->addArgument('lists', InputArgument::REQUIRED, 'The lists to send emails to')
            ->addArgument('limit', InputArgument::OPTIONAL, 'The limit of emails to send')
            ->addArgument('batch', InputArgument::OPTIONAL, 'The batch size for sending emails');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityId = $input->getArgument('entityId');
        $lists    = json_decode($input->getArgument('lists'), true);
        $limit    = $input->getArgument('limit');
        $batch    = $input->getArgument('batch');

        $entity = $this->emailModel->getEntity($entityId);

        if (null === $entity || !$entity->isPublished()) {
            $output->writeln('Entity not found or not published');

            return Command::FAILURE;
        }

        [$count, $failed] = $this->emailModel->sendEmailToLists($entity, $lists, $limit, $batch);

        $output->writeln("Sent $count emails, $failed failed");

        return Command::SUCCESS;
    }
}

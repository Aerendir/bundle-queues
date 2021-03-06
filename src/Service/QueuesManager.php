<?php

declare(strict_types=1);

/*
 * This file is part of the Serendipity HQ Commands Queues Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SerendipityHQ\Bundle\CommandsQueuesBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use RuntimeException;
use Safe\Exceptions\StringsException;
use SerendipityHQ\Bundle\CommandsQueuesBundle\Entity\Daemon;
use SerendipityHQ\Bundle\CommandsQueuesBundle\Entity\Job;
use SerendipityHQ\Bundle\CommandsQueuesBundle\Repository\JobRepository;
use SerendipityHQ\Bundle\CommandsQueuesBundle\Util\InputParser;

/**
 * Manages the commands_queues.
 */
final class QueuesManager
{
    /** @var EntityManager */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        // This is to make static analysis pass
        if ( ! $entityManager instanceof EntityManager) {
            throw new RuntimeException('You need to pass an EntityManager instance.');
        }

        $this->entityManager = $entityManager;
    }

    /**
     * Checks if a Job is scheduled given a Job instance.
     *
     * It uses the given Job's parameters to find one already scheduled.
     *
     * It uses command, arguments and queue and searches only for Jobs not already executed.
     *
     * Returns (bool) false if it doesn't exist or the scheduled Job if it exists.
     *
     * @param Job  $job
     * @param bool $fullSearch If false, searches only not yet started jobs
     *
     * @throws StringsException
     *
     * @return bool
     */
    public function jobExists(Job $job, bool $fullSearch = false): bool
    {
        return $this->exists($job->getCommand(), $job->getInput(), $job->getQueue(), $fullSearch);
    }

    /**
     * Finds a Job given a Job instance.
     *
     * It uses the given Job's parameters to find one already scheduled.
     *
     * It uses command, arguments and queue and searches only for Jobs not already executed.
     *
     * Returns null if it doesn't exist or the scheduled Job if it exists.
     *
     * @param Job  $job
     * @param bool $fullSearch If false, searches only not yet started jobs
     *
     * @throws StringsException
     *
     * @return array|null
     */
    public function findByJob(Job $job, bool $fullSearch = false): ?array
    {
        return $this->find($job->getCommand(), $job->getInput(), $job->getQueue(), $fullSearch);
    }

    /**
     * Given Job parameters, checks if it exists or not.
     *
     * Returns (bool) false if it doesn't exist or the scheduled Job if it exists.
     *
     * @param string            $command
     * @param array|string|null $input
     * @param string            $queue
     * @param bool              $fullSearch If false, searches only not yet started jobs
     *
     * @throws StringsException
     *
     * @return bool
     */
    public function exists(string $command, $input = null, string $queue = Daemon::DEFAULT_QUEUE_NAME, bool $fullSearch = false): bool
    {
        $exists = $this->find($command, $input, $queue, $fullSearch);

        return ! empty($exists);
    }

    /**
     * Finds a Job given its parameters.
     *
     * Returns null if it doesn't exist or the scheduled Job if it exists.
     *
     * @param string|null       $command
     * @param array|string|null $input
     * @param string|null       $queue
     * @param bool              $fullSearch If false, searches only not yet started jobs
     *
     * @throws StringsException
     *
     * @return array|null
     */
    public function find(string $command = null, $input = null, ?string $queue = Daemon::DEFAULT_QUEUE_NAME, bool $fullSearch = false): ?array
    {
        /** @var JobRepository $jobsRepo */
        $jobsRepo = $this->entityManager->getRepository(Job::class);

        // Check and prepare arguments of the command
        if (null !== $input) {
            $input = InputParser::parseInput($input);
        }

        return $jobsRepo->findBySearch($command, $input, $queue, $fullSearch);
    }

    /**
     * persists a job and flushes it to the database.
     *
     * @param Job $job
     *
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function schedule(Job $job): void
    {
        $this->entityManager->persist($job);
        $this->entityManager->flush($job);
    }
}

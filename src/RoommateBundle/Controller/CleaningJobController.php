<?php

namespace RoommateBundle\Controller;

use RoommateBundle\Provider\AuthenticatedUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CleaningJobController extends Controller
{
    public function viewAction()
    {
        $cleaningJobDbalRepository = $this->get('roommate.repositories.cleaning_job_dbal_repository');
        $jobs = $cleaningJobDbalRepository->fetchJobsForHouse($this->getCurrentHouseId());

        $jobs = array_map(
            function (array $job) use ($cleaningJobDbalRepository) {
                $job['assignees'] = $cleaningJobDbalRepository->fetchJobAssignees($job['id']);
                return $job;
            },
            $jobs
        );

        return $this->render('RoommateBundle:CleaningJob:view.html.twig', [
            'jobs' => $jobs,
            'startDate' => new \DateTime($this->getParameter('cleaning_start_year') . '-01 monday'),
        ]);
    }

    private function getCurrentHouseId()
    {
        /** @var AuthenticatedUser $user */
        $user = $this->getUser();
        return $user->getHouseId();
    }
}

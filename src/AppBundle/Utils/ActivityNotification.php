<?php

namespace AppBundle\Utils;

use AppBundle\Entity\Activity;
use AppBundle\Entity\Issue;
use AppBundle\Entity\User;
use AppBundle\Event\ActivityEvent;
use Symfony\Component\Templating\EngineInterface;

class ActivityNotification
{
    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var ActivityNotification
     */
    private $mailer;

    /**
     * @var string
     */
    private $emailFrom;

    /**
     * @param EngineInterface $templating
     * @param ActivityNotification $mailer
     * @param string $emailFrom
     */
    public function __construct(EngineInterface $templating, \Swift_Mailer $mailer, $emailFrom)
    {
        $this->templating = $templating;
        $this->mailer     = $mailer;
        $this->emailFrom  = $emailFrom;
    }

    /**
     * @param Issue $issue
     * @param Activity $activity
     */
    public function onActivityCreated(ActivityEvent $activityEvent)
    {
        $activity = $activityEvent->getActivity();
        $issue    = $activity->getIssue();

        $emails = $issue->getCollaborators()->map(function (User $user) {
            return $user->getEmail();
        })->toArray();

        /** @var  $message */
        $message = \Swift_Message::newInstance()
            ->setSubject('Luminaire Activity')
            ->setFrom($this->emailFrom)
            ->setTo($emails)
            ->setBody(
                $this->templating->render(
                    'AppBundle:Activity:email.html.twig',
                    ['entity' => $activity]
                ),
                'text/html'
            );

        $this->mailer->send($message);
    }
}

<?php

/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 * @author Syakur Rahman <shaqman2004@yahoo.com>
 */

namespace shaqman\mailer\queuemailer\jobs;

use shaqman\mailer\queuemailer\Mailer;
use yii\di\Instance;
use yii\base\BaseObject;
use \yii\queue\Job;
use yii\mail\MessageInterface;

class SendMessageJob extends BaseObject implements Job {

    /** @var MessageInterface */
    public $message;

    /** @var string|array */
    public $mailer = 'mailer';

    public function execute($queue) {
        $this->mailer = Instance::ensure($this->mailer, Mailer::class);
        $this->message = Instance::ensure($this->message, MessageInterface::class);

        $this->message->send($this->mailer->syncMailer);
    }

}

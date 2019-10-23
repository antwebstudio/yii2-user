<?php

/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 * @author Syakur Rahman <shaqman2004@yahoo.com>
 */

namespace shaqman\mailer\queuemailer\jobs;

use shaqman\mailer\queuemailer\Mailer;
use yii\base\BaseObject;
use yii\di\Instance;
use \yii\queue\Job;
use yii\mail\MessageInterface;

class SendMultipleMessagesJob extends BaseObject implements Job {

    /** @var MessageInterface */
    public $messages;

    /** @var string|array */
    public $mailer = 'mailer';

    public function execute($queue) {
        $this->mailer = Instance::ensure($this->mailer, Mailer::class);
        return $this->message->sendMultiple($this->mailer);
    }

}

# shaqman/yii2-queue-mailer
Background mailer for Yii2 using any queue interface. This extension was tested with [yii2 queue extension](https://github.com/yiisoft/yii2-queue/).

This extension was tested only with [swiftmailer extension](http://www.yiiframework.com/doc-2.0/yii-swiftmailer-mailer.html), though it should work with any compatible mailer extension

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist shaqman/yii2-queue-mailer
```

or add

```json
"shaqman/yii2-queue-mailer": "*"
```

## Usage

Configure `queue` component of your application.

You can find the details here: https://github.com/yiisoft/yii2-queue/

Configure `\shaqman\mailer\queuemailer\Mailer` as your primary mailer.

```
  'mailer' => [
      'class' => \shaqman\mailer\queuemailer\Mailer::class,
      'queue' => 'queue' // name of queue component, or a valid array configuration for it.
      'syncMailer' => [ // Any valid mailer should work
          'class' => 'yii\swiftmailer\Mailer',
          'useFileTransport' => true,
      ],
  ],
```

Now you can send your emails as usual.
```
$message = \Yii::$app->mailer->compose()
  ->setSubject('test subject')
  ->setFrom('test@example.org')
  ->setHtmlBody('test body')
  ->setTo('user@example.org')
  ->send($message);
```

One small difference should be noted compared to the default `send`. It now returns an `int` identifying the queue id that the mail was assigned to.
This is to be expected since there is no way to know whether the mail was successfully sent at the time of the command was executed.
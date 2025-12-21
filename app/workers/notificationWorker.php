<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../core/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../core/notifications.php';

// Load all notification channels, recipients, and recipient providers
foreach (glob(__DIR__ . '/../notifications/recipients/*.php') as $filename) {
    require_once $filename;
}

foreach (glob(__DIR__ . '/../notifications/recipientProviders/*.php') as $filename) {
    require_once $filename;
}

foreach (glob(__DIR__ . '/../notifications/channels/*.php') as $filename) {
    require_once $filename;
}


class NotificationWorker {

    use Model;
    private array $channels = [];
    protected string $table = "notification_jobs";
    private int $jobFetchLimit = 10;

    public function __construct(array $arrayOfChannelNames) {
        // Register notification channels
        foreach($arrayOfChannelNames as $channelName){
            $this->registerChannel($channelName);
        }
    }

    public function registerChannel(string $channelName): void {
        $realClassName = $channelName . "Channel";
        $this->channels[$channelName] = new $realClassName();
    }

    public function executeQueuedNotifications(): void {

        foreach($this->getLatestNotificationJobData() as $jobData){

            $notification = $this->buildObjectFromClassName(Notification::class, $jobData['notificationData']);
            $recipientProvider = $this->buildObjectFromClassName(
                $jobData['recipientProviderData']['className'],
                $jobData['recipientProviderData']['constructorInputs']
            );

            $this->notify(
                $notification,
                $recipientProvider,
                $jobData['channelNames']
            );

            $this->jobFinished($jobData['id']);
        }
    }

    private function buildObjectFromClassName(string $className, array $data): object {
        $reflectionClass = new ReflectionClass($className);
        $constructor = $reflectionClass->getConstructor();
        $parameters = $constructor->getParameters();
        $args = [];

        foreach ($parameters as $parameter) {
            $paramName = $parameter->getName();
            if (!array_key_exists($paramName, $data)) {
                throw new Exception("Missing data for parameter: {$paramName}");
            }

            $args[] = $data[$paramName];
        }

        return $reflectionClass->newInstanceArgs($args);
    }

    private function getLatestNotificationJobData(): iterable {
        $offset = 0;

        while (true) {
            $jobs = $this->getNextNotificationJobDataBatch($offset);

            if (empty($jobs)) {
                break;
            }

            foreach ($jobs as $job) {
                yield $this->mapDBData($job);
            }

            // If we fetched fewer jobs than the limit = last page.
            if (count($jobs) < $this->jobFetchLimit) {
                break;
            }

            $offset += $this->jobFetchLimit;
        }
    }

    private function getNextNotificationJobDataBatch(int $offset): array {
        return $this->where(
            conditions: [['status', '=', 'queued']],
            orderBy: ['created_at' => 'ASC'],
            limit: $this->jobFetchLimit,
            offset: $offset
        );
    }

    private function mapDBData(object $dbData): array {
        return [
            'id' => $dbData->id,
            'notificationData' => [
                'type' => $dbData->notification_type,
                'title' => $dbData->notification_title,
                'message' => $dbData->notification_message,
                'metadata' => json_decode($dbData->notification_metadata)
            ],
            'recipientProviderData' => [
                'className' => $dbData->recipient_provider_class,
                'constructorInputs' => json_decode($dbData->recipient_provider_constructor_inputs, true)
            ],
            'channelNames' => json_decode($dbData->channel_names, true)
        ];
    }

    private function notify(Notification $notification, RecipientProviderInterface $provider, array $channelNames): void {

        foreach ($channelNames as $channelName) {

            if (isset($this->channels[$channelName])) {
                $channel = $this->channels[$channelName];

                // New instance for each channel to avoid state carryover
                $notificationCopy = clone $notification;
                
                foreach ($provider->getRecipients() as $recipient) {
                    $channel->send($notificationCopy, $recipient);
                }

            }else{
                error_log("Notification channel {$channelName} not registered.");
            }
        }
    }

    private function jobFinished(int $id){
        $this->update($id, ['status' => 'completed'], 'id');
    }
    
}



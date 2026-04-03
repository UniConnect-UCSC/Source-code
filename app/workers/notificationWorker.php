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
        error_log("[NotificationWorker] __construct called");
        // Register notification channels
        foreach($arrayOfChannelNames as $channelName){
            $this->registerChannel($channelName);
        }
    }

    public function registerChannel(string $channelName): void {
        $realClassName = $channelName . "Channel";
        error_log("[NotificationWorker] Registering channel: {$realClassName}");
        $channelInstance = new $realClassName();
        $this->channels[$channelInstance->getChannelName()] = $channelInstance;
    }

    public function executeQueuedNotifications(): void {
        error_log("[NotificationWorker] executeQueuedNotifications called");
        foreach($this->getLatestNotificationJobData() as $jobData){
            error_log("[NotificationWorker] Processing job ID: " . ($jobData['id'] ?? 'unknown'));
            try{
                $notification = $this->buildObjectFromClassName(Notification::class, $jobData['notificationData']);
                error_log("[NotificationWorker] Notification object built");
                $recipientProvider = $this->buildObjectFromClassName(
                    $jobData['recipientProviderData']['className'],
                    $jobData['recipientProviderData']['constructorInputs']
                );
                error_log("[NotificationWorker] RecipientProvider object built");
                $this->notify(
                    $notification,
                    $recipientProvider,
                    $jobData['channelNames']
                );
                error_log("[NotificationWorker] notify() completed for job ID: " . $jobData['id']);
            }catch(Exception $e){
                error_log("[NotificationWorker] Error processing notification job ID {$jobData['id']}: " . $e->getMessage());
                $this->jobFailed($jobData['id']);
                continue;
            }
            $this->jobFinished($jobData['id']);
            error_log("[NotificationWorker] jobFinished called for job ID: " . $jobData['id']);
        }
    }

    private function buildObjectFromClassName(string $className, array $data): object {
        error_log("[NotificationWorker] buildObjectFromClassName called for class: {$className}");
        $reflectionClass = new ReflectionClass($className);
        $constructor = $reflectionClass->getConstructor();
        $parameters = $constructor->getParameters();
        $args = [];

        foreach ($parameters as $parameter) {
            $paramName = $parameter->getName();
            if (!array_key_exists($paramName, $data)) {
                error_log("[NotificationWorker] Missing data for parameter: {$paramName} in class: {$className}");
                throw new Exception("Missing data for parameter: {$paramName}");
            }
            $args[] = $data[$paramName];
        }

        return $reflectionClass->newInstanceArgs($args);
    }

    private function getLatestNotificationJobData(): iterable {
        error_log("[NotificationWorker] getLatestNotificationJobData called");
        $offset = 0;

        while (true) {
            $jobs = $this->getNextNotificationJobDataBatch($offset);
            error_log("[NotificationWorker] getNextNotificationJobDataBatch returned " . count($jobs) . " jobs");
            if (empty($jobs)) {
                break;
            }
            foreach ($jobs as $job) {
                error_log("[NotificationWorker] Yielding job with ID: " . ($job->id ?? 'unknown'));
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
        error_log("[NotificationWorker] getNextNotificationJobDataBatch called with offset: {$offset}");
        $tempData = $this->where(
            conditions: [['status', '=', 'queued']],
            orderBy: ['created_at' => 'ASC'],
            limit: $this->jobFetchLimit,
            offset: $offset
        );
        if ($tempData === false || $tempData === null) {
            error_log("[NotificationWorker] where() returned false or null");
            return [];
        }
        error_log("[NotificationWorker] where() returned " . count($tempData) . " records");
        return $tempData;
    }

    private function mapDBData(object $dbData): array {
        error_log("[NotificationWorker] mapDBData called for job ID: " . ($dbData->id ?? 'unknown'));
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
        error_log("[NotificationWorker] notify called");
        foreach ($channelNames as $channelName) {
            error_log("[NotificationWorker] Notifying via channel: {$channelName}");
            if (isset($this->channels[$channelName])) {
                $channel = $this->channels[$channelName];
                // New instance for each channel to avoid state carryover
                $notificationCopy = clone $notification;
                foreach ($provider->getRecipients() as $recipient) {
                    error_log("[NotificationWorker] Sending notification to recipient: " . (is_object($recipient) && method_exists($recipient, 'getId') ? $recipient->getId() : json_encode($recipient)));
                    $channel->send($notificationCopy, $recipient);
                }
            }else{
                error_log("[NotificationWorker] Channel not registered: {$channelName}");
                // Warning: if a valid channel is executed before an invalid one, the valid one will still process
                throw new Exception("Notification channel {$channelName} not registered.");
            }
        }
    }

    private function jobFinished(int $id){
        error_log("[NotificationWorker] jobFinished called for job ID: {$id}");
        $this->update($id, ['status' => 'completed'], 'id');
    }

    private function jobFailed(int $id){
        error_log("[NotificationWorker] jobFailed called for job ID: {$id}");
        $this->update($id, ['status' => 'failed'], 'id');
    }
    
}



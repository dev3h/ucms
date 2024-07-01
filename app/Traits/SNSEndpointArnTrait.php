<?php

namespace App\Traits;

use App\Models\DeviceToken;
use Aws\Credentials\Credentials;
use Aws\Sns\SnsClient;

trait SNSEndpointArnTrait
{
    /**
     * The client sns.
     */
    protected $clientSns;

    /**
     * SNSEndpointArn constructor.
     */
    public function createClientSns()
    {
        $this->clientSns = new SnsClient([
            'region' => config('aws.region'),
            'credentials' => new Credentials(
                config('aws.credentials.key'),
                config('aws.credentials.secret')
            ),
            'version' => config('aws.version'),
        ]);
    }

    /**
     * Get sns endpoint arn
     *
     * @param string $token
     */
    public function getSnsEndpointArn(string $token)
    {
        $platformApplicationArn = config('aws.sns_arn');

        $result = $this->clientSns->createPlatformEndpoint([
            'PlatformApplicationArn' => $platformApplicationArn,
            'Token' => $token
        ]);

        return $result['EndpointArn'];
    }

    /**
     * List topic
     */
    public function listTopic()
    {
        $result = $this->clientSns->listTopics();

        return $result['Topics'];
    }

    /**
     * Create topic
     *
     * @param string $topicname
     */
    public function createTopic(string $topicname)
    {
        $result = $this->clientSns->createTopic([
            'Name' => $topicname,
        ]);

        return $result['TopicArn'];
    }

    /**
     * Delete topic
     *
     * @param string $topicArn
     */
    public function deleteTopic(string $topicArn)
    {
        $result = $this->clientSns->deleteTopic([
            'TopicArn' => $topicArn,
        ]);

        return $result['TopicArn'];
    }

    /**
     * Create subcribe device token to topic
     *
     * @param string $endPointArn
     * @param string $topicArn
     */
    public function subscribeDeviceTokenToTopic(string $endPointArn, string $topicArn)
    {
        $result = $this->clientSns->subscribe([
            'Endpoint' => $endPointArn,
            'Protocol' => 'application',
            'TopicArn' => $topicArn,
        ]);

        return $result['SubscriptionArn'] ?? '';
    }

    /**
     * Unsubcribe device token to topic
     *
     * @param string $subscriptionArn
     */
    public function unsubscribeDeviceTokenToTopic(string $subscriptionArn)
    {
        $result = $this->clientSns->unsubscribe([
            'SubscriptionArn' => $subscriptionArn,
        ]);

        return $result;
    }

    /**
     * Publish notification to topic arn
     *
     * @param array $dataNotification
     * @param string $topicArn
     */
    public function publishToTopic(array $dataNotification, string $topicArn)
    {
        $message = $this->getDataMessage($dataNotification);
        $result = $this->clientSns->publish([
            'Message' => $message,
            'MessageStructure' => 'json',
            'TopicArn' => $topicArn,
        ]);

        return $result['MessageId'] ?? '';
    }

    /**
     * Publish notification to endpoint arn
     *
     * @param \App\Models\DeviceToken $deviceToken
     * @param array $dataNotification
     */
    public function pushNotification(DeviceToken $deviceToken, array $dataNotification)
    {
        $endPointArn = [
            'EndpointArn' => $deviceToken->endpoint_arn
        ];
        $message = $this->getDataMessage($dataNotification);

        $endpointAtt = $this->clientSns->getEndpointAttributes($endPointArn);
        if ($endpointAtt != 'failed' && $endpointAtt['Attributes']['Enabled']) {
            $result = $this->clientSns->publish([
                'TargetArn' => $deviceToken->endpoint_arn,
                'Message' => $message,
                'MessageStructure' => 'json'
            ]);
        }

        return $result ?? null;
    }

    /**
     * Get data message notification
     *
     * @param array $dataNotification
     * @return array
     */
    public function getDataMessage(array $dataNotification)
    {
        $fcmPayload = json_encode([
            'notification' => [
                'title' => $dataNotification['title'] ?? null,
                "body" => $dataNotification['content'] ?? null,
                'image' => $dataNotification['icon_url'] ?? null,
                'picture' => $dataNotification['icon_url'] ?? null,
                'sound' => 'default'
            ],
            'data' => $dataNotification ?? []
        ]);

        return json_encode([
            'default' => $dataNotification['content'] ?? null,
            'GCM' => $fcmPayload
        ]);
    }
}

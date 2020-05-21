<?php

namespace Spatie\WebhookClient\Tests;

use Spatie\WebhookClient\Exceptions\InvalidConfig;
use Spatie\WebhookClient\Models\WebhookCall;
use Spatie\WebhookClient\SignatureValidator\DefaultSignatureValidator;
use Spatie\WebhookClient\Tests\TestClasses\ProcessWebhookJobTestClass;
use Spatie\WebhookClient\WebhookConfig;
use Spatie\WebhookClient\WebhookProfile\ProcessEverythingWebhookProfile;
use Spatie\WebhookClient\WebhookResponse\DefaultRespondsTo;

class WebhookConfigTest extends TestCase
{
    /** @test */
    public function it_can_handle_a_valid_configuration()
    {
        $configArray = $this->getValidConfig();

        $webhookConfig = new WebhookConfig($configArray);

        $this->assertEquals($configArray['name'], $webhookConfig->name);
        $this->assertEquals($configArray['signing_secret'], $webhookConfig->signingSecret);
        $this->assertEquals($configArray['signature_header_name'], $webhookConfig->signatureHeaderName);
        $this->assertInstanceOf($configArray['signature_validator'], $webhookConfig->signatureValidator);
        $this->assertInstanceOf($configArray['webhook_profile'], $webhookConfig->webhookProfile);
        $this->assertEquals($configArray['webhook_model'], $webhookConfig->webhookModel);
        $this->assertEquals($configArray['process_webhook_job'], $webhookConfig->processWebhookJobClass);
    }

    /** @test */
    public function it_validates_the_signature_validator()
    {
        $config = $this->getValidConfig();
        $config['signature_validator'] = 'invalid-signature-validator';

        $this->expectException(InvalidConfig::class);

        new WebhookConfig($config);
    }

    /** @test */
    public function it_validates_the_webhook_profile()
    {
        $config = $this->getValidConfig();
        $config['webhook_profile'] = 'invalid-webhook-profile';

        $this->expectException(InvalidConfig::class);

        new WebhookConfig($config);
    }

    /** @test */
    public function it_validates_the_webhook_response()
    {
        $config = $this->getValidConfig();
        $config['webhook_response'] = 'invalid-webhook-response';

        $this->expectException(InvalidConfig::class);

        new WebhookConfig($config);
    }

    /** @test */
    public function it_uses_the_default_webhook_response_if_none_provided()
    {
        $config = $this->getValidConfig();
        $config['webhook_response'] = null;

        $this->assertInstanceOf(DefaultRespondsTo::class, (new WebhookConfig($config))->webhookResponse);
    }

    /** @test */
    public function it_validates_the_process_webhook_job()
    {
        $config = $this->getValidConfig();
        $config['process_webhook_job'] = 'invalid-process-webhook-job';

        $this->expectException(InvalidConfig::class);

        new WebhookConfig($config);
    }

    protected function getValidConfig(): array
    {
        return [
            'name' => 'default',
            'signing_secret' => 'my-secret',
            'signature_header_name' => 'Signature',
            'signature_validator' => DefaultSignatureValidator::class,
            'webhook_profile' => ProcessEverythingWebhookProfile::class,
            'webhook_response' => DefaultRespondsTo::class,
            'webhook_model' => WebhookCall::class,
            'process_webhook_job' => ProcessWebhookJobTestClass::class,
        ];
    }
}

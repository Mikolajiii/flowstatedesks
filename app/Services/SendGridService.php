<?php

namespace App\Services;

use SendGrid;
use SendGrid\Mail\Mail;
use Illuminate\Support\Facades\Log;

class SendGridService
{
    protected $sendgrid;

    public function __construct()
    {

        $options = [];

        // Disable SSL verification ONLY in local environment
        if (app()->environment('local')) {
            $options['verify_ssl'] = true;
        }

        $this->sendgrid = new \SendGrid(env('SENDGRID_API_KEY'), $options);
    }

    /**
     * Resolve a template ID from config/sendgrid.php
     *
     * @param string $templateKey
     * @return string
     * @throws \Exception
     */
    protected function resolveTemplateId(string $templateKey): string
    {
        $templateId = config("sendgrid.templates.$templateKey");

        if (!$templateId) {
            Log::error("SendGrid template key [$templateKey] not found in config/sendgrid.php");
            throw new \Exception("Template key [$templateKey] not found in config/sendgrid.php");
        }

        return $templateId;
    }

    /**
     * Send a dynamic template email via SendGrid
     *
     * @param string $templateKey  Key from config/sendgrid.php (e.g. 'INVOICE_EMAIL')
     * @param string $toEmail      Recipient email
     * @param string|null $toName  Recipient name
     * @param array $dynamicData   Key-value pairs for template variables
     * @return bool
     */
    public function sendTemplate(string $templateKey, string $toEmail, ?string $toName, array $dynamicData = []): bool
    {
        try {
            $email = new Mail();

            $email->setFrom(config('mail.from.address'), config('mail.from.name'));
            $email->addTo($toEmail, $toName ?? 'User');

            // Resolve template ID safely
            $templateId = $this->resolveTemplateId($templateKey);
            $email->setTemplateId($templateId);

            // Add dynamic data
            foreach ($dynamicData as $key => $value) {
                $email->addDynamicTemplateData($key, (string) $value);
            }

            Log::debug('SendGrid payload', $email->jsonSerialize());

            $response = $this->sendgrid->send($email);

            Log::info('SendGrid response', [
                'status' => $response->statusCode(),
                'body'   => $response->body(),
            ]);

            return $response->statusCode() >= 200 && $response->statusCode() < 300;
        } catch (\Exception $e) {
            Log::error('SendGrid error: ' . $e->getMessage());
            return false;
        }
    }
}

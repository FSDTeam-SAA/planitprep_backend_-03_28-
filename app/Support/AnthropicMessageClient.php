<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;

class AnthropicMessageClient
{
    public static function send(
        string $systemMessage,
        string $userMessage,
        int $maxTokens = 1500,
        array $httpOptions = [],
        array $extraPayload = []
    ): array {
        $apiKey = config('app.anthropic_api_key');

        if (!$apiKey) {
            return [
                'status' => 'error',
                'message' => 'Anthropic API key is not configured.',
                'code' => 500,
            ];
        }

        $requestTimeout = (int) ($httpOptions['timeout'] ?? 180);
        unset($httpOptions['timeout']);

        // Claude diet-plan responses can take longer than the default local PHP limit.
        @ini_set('max_execution_time', (string) max(180, $requestTimeout + 30));
        if (function_exists('set_time_limit')) {
            @set_time_limit(max(180, $requestTimeout + 30));
        }

        $payload = array_merge([
            'model' => config('app.anthropic_model', 'claude-sonnet-4-20250514'),
            'max_tokens' => $maxTokens,
            'system' => $systemMessage,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $userMessage,
                ],
            ],
        ], $extraPayload);

        $response = Http::withOptions($httpOptions)
            ->connectTimeout(20)
            ->timeout($requestTimeout)
            ->withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => config('app.anthropic_version', '2023-06-01'),
                'content-type' => 'application/json',
            ])
            ->post('https://api.anthropic.com/v1/messages', $payload);

        if (! $response->successful()) {
            $error = $response->json();

            return [
                'status' => 'error',
                'message' => $error['error']['message'] ?? 'Failed to connect to Claude.',
                'code' => $response->status(),
                'raw_response' => $response->body(),
            ];
        }

        $data = $response->json();
        $content = self::extractText($data['content'] ?? []);

        if ($content === null) {
            return [
                'status' => 'error',
                'message' => 'Claude returned an empty response.',
                'code' => 500,
                'raw_response' => $data,
            ];
        }

        return [
            'status' => 'success',
            'content' => self::normalizeJsonText($content),
            'stop_reason' => $data['stop_reason'] ?? null,
            'usage' => $data['usage'] ?? null,
            'raw_response' => $data,
        ];
    }

    private static function extractText(array $contentBlocks): ?string
    {
        foreach ($contentBlocks as $block) {
            if (($block['type'] ?? null) === 'text' && isset($block['text'])) {
                return $block['text'];
            }
        }

        return null;
    }

    private static function normalizeJsonText(string $content): string
    {
        $content = trim($content);

        if (preg_match('/^```(?:json)?\s*(.*?)\s*```$/is', $content, $matches)) {
            $content = trim($matches[1]);
        }

        $firstBrace = strpos($content, '{');
        $firstBracket = strpos($content, '[');

        $starts = array_filter([
            $firstBrace === false ? null : $firstBrace,
            $firstBracket === false ? null : $firstBracket,
        ], static fn ($value) => $value !== null);

        if ($starts === []) {
            return $content;
        }

        $start = min($starts);
        $content = substr($content, $start);

        $lastBrace = strrpos($content, '}');
        $lastBracket = strrpos($content, ']');
        $endCandidates = array_filter([
            $lastBrace === false ? null : $lastBrace,
            $lastBracket === false ? null : $lastBracket,
        ], static fn ($value) => $value !== null);

        if ($endCandidates === []) {
            return trim($content);
        }

        $end = max($endCandidates);

        return trim(substr($content, 0, $end + 1));
    }
}

<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * @see https://github.com/rezaulhreza/hugging-face-for-laravel
 */
class CAI_Service_HuggingFace extends CAI_ServiceAbstract {
    protected $apiKey;

    protected $client;

    protected $baseUrl;

    protected array $models;

    protected array $modelTypes;

    public function __construct($options = []) {
        $apiKey = carr::get($options, 'api_key');
        $baseUrl = carr::get($options, 'base_url', 'https://api-inference.huggingface.co/models/');
        if (empty(trim($apiKey))) {
            throw new InvalidArgumentException('HuggingFace API token cannot be empty');
        }
        $this->apiKey = $apiKey;
        $this->client = new Client();
        $this->models = CF::config('ai.hugging-face.models', []);
        $this->modelTypes = CF::config('ai.hugging-face.model_types', []);
        $this->baseUrl = $baseUrl;
    }

    /**
     * Get response from any HuggingFace model.
     *
     * @param string $prompt  The input prompt or data
     * @param string $model   The model identifier
     * @param array  $options Additional options for the request
     *
     * @return null|array|string Response data, base64 image string for images, or null on failure
     */
    public function getResponse(string $prompt, string $model, array $options = []) {
        try {
            $modelConfig = $this->resolveModelConfig($model, $options);
            if (empty($prompt)) {
                throw new InvalidArgumentException('Prompt cannot be empty');
            }

            $payload = $this->buildPayload($prompt, $options);
            $response = CHTTP::client()->withToken($this->apiKey)
                ->timeout(30)
                ->retry(2, 1000)
                ->post($this->baseUrl . $model, $payload);
            if ($response->failed()) {
                $this->handleError($response);

                return null;
            }

            return $this->processResponse($response, $modelConfig['type']);
        } catch (\Throwable $e) {
            $this->logException($e);

            return null;
        }
    }

    /**
     * Handle API errors.
     */
    protected function handleError(CHTTP_Client_Response $response): void {
        $statusCode = $response->status();
        $errorData = [
            'status_code' => $statusCode,
            'error' => $response->json()['error'] ?? 'Unknown error',
            'response_body' => $response->body(),
            'request_url' => c::optional($response->effectiveUri())->__toString(),
            'request_method' => c::optional(c::optional($response->transferStats)->getRequest())->getMethod(),
        ];

        c::logger()->error('HuggingFace API Error', $errorData);
        if ($statusCode == 401) {
            throw new \RuntimeException('Invalid or expired API token: ' . $errorData['error'], 401);
        }
        if ($statusCode == 429) {
            throw new \RuntimeException('Rate limit exceeded: ' . $errorData['error'], 429);
        }
        if ($statusCode == 500) {
            throw new \RuntimeException('HuggingFace service is unavailable: ' . $errorData['error'], 500);
        }

        throw new \RuntimeException("API request failed with status {$statusCode}: " . $errorData['error'], $statusCode);
    }

    /**
     * Process the response based on the model type.
     */
    protected function processResponse(CHTTP_Client_Response $response, string $type) {
        try {
            if ($type === 'image') {
                return 'data:image/png;base64,' . base64_encode($response->body());
            }

            $data = $response->json();

            if (!$data) {
                return [
                    'text' => $response->body(),
                    'raw' => $data,
                ];
            }

            // Handle chat completion format
            if (isset($data['choices'][0]['message']['content'])) {
                return [
                    'text' => $data['choices'][0]['message']['content'],
                    'raw' => $data,
                ];
            }

            // Handle array response format
            if (isset($data[0])) {
                $firstResult = $data[0];

                return [
                    'text' => $firstResult['generated_text']
                        ?? $firstResult['answer']
                        ?? $firstResult['translation_text']
                        ?? $firstResult['summary_text']
                        ?? json_encode($firstResult),
                    'raw' => $data,
                ];
            }

            // Handle object response format
            return [
                'text' => $data['generated_text']
                    ?? $data['answer']
                    ?? $data['translation_text']
                    ?? $data['summary_text']
                    ?? json_encode($data),
                'raw' => $data,
            ];
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function logException(Throwable $e): void {
        c::logger()->error('HuggingFace Service Error', [
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);
    }

    /**
     * Build the request payload based on the prompt and options.
     */
    protected function buildPayload(string $prompt, array $options): array {
        $payload = ['inputs' => $prompt];

        // Add any additional parameters from options
        if (isset($options['parameters'])) {
            $payload = array_merge($payload, $options['parameters']);
        }

        return $payload;
    }

    /**
     * Generate text from a given prompt.
     *
     * @throws CAI_Exception_ClientException
     *
     * @return array
     */
    public function ask(array $options = []) {
        $prompt = carr::get($options, 'prompt', $this->prompt);

        try {
            $retryCount = 0;
            $maxRetries = 5; // You can set a limit for retries
            $waitTime = 10; // Wait time in seconds before retrying (can be dynamic based on response)

            while ($retryCount < $maxRetries) {
                $response = $this->client->post('https://api-inference.huggingface.co/models/gpt2', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                    ],
                    'json' => [
                        'inputs' => $prompt,
                    ],
                ]);

                $responseData = json_decode($response->getBody()->getContents(), true);

                // Check if the response indicates that the model is still loading
                if (isset($responseData['error']) && strpos($responseData['error'], 'currently loading') !== false) {
                    $retryCount++;
                    $estimatedTime = $responseData['estimated_time'] ?? $waitTime;
                    sleep($estimatedTime); // Wait for the estimated time before retrying

                    continue;
                }

                // If no error, break out of the loop
                break;
            }

            // If retries exceeded, throw an error
            if ($retryCount >= $maxRetries) {
                throw new CAI_Exception_ClientException('Model loading took too long. Try again later.');
            }

            // Check if the response contains the necessary data
            if (isset($responseData[0]['generated_text'])) {
                $generatedText = $responseData[0]['generated_text'];

                return $generatedText;
            }

            throw new CAI_Exception_ClientException('Text generation failed. No "generated_text" in response.');
        } catch (RequestException $e) {
            throw new CAI_Exception_ClientException('Request failed: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new CAI_Exception_ClientException('Unexpected error: ' . $e->getMessage());
        }
    }

    /**
     * Generate an image from a prompt.
     *
     * @throws CAI_Exception_ClientException
     *
     * @return array
     */
    public function image(array $options = []) {
        $prompt = $this->prompt;
        $uri = carr::get($options, 'uri', 'https://api-inference.huggingface.co/models/stabilityai/stable-diffusion-2');

        try {
            $retryCount = 0;
            $maxRetries = 5; // You can set a limit for retries
            $waitTime = 10; // Wait time in seconds before retrying (can be dynamic based on response)

            while ($retryCount < $maxRetries) {
                $response = $this->client->get($uri, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                    ],
                    'query' => [
                        'inputs' => $prompt,
                    ],
                ]);

                $responseData = json_decode($response->getBody()->getContents(), true);

                // Check if the response indicates that the model is still loading
                if (isset($responseData['error']) && strpos($responseData['error'], 'currently loading') !== false) {
                    $retryCount++;
                    $estimatedTime = $responseData['estimated_time'] ?? $waitTime;
                    sleep($estimatedTime); // Wait for the estimated time before retrying

                    continue;
                }

                // If no error, break out of the loop
                break;
            }

            // If retries exceeded, throw an error
            if ($retryCount >= $maxRetries) {
                throw new CAI_Exception_ClientException('Model loading took too long. Try again later.');
            }

            // Process the response (if model is ready)
            if ($response->getHeader('Content-Type')[0] === 'image/jpeg') {
                $imageStream = $response->getBody();

                return $imageStream;
            }

            throw new CAI_Exception_ClientException('Image generation failed. Unexpected response format.');
        } catch (RequestException $e) {
            throw new CAI_Exception_ClientException('Request failed: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new CAI_Exception_ClientException('Unexpected error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Resolve the model configuration.
     */
    protected function resolveModelConfig(string $model, array $options): array {
        // Check if it's a pre-configured model
        if (isset($this->models[$model])) {
            return $this->models[$model];
        }

        // If type is provided in options, use it
        if (isset($options['type'])) {
            return [
                'type' => $options['type'],
                'url' => $model,
            ];
        }

        try {
            // Try to fetch model info from HuggingFace
            $modelInfo = CHttp::client()->withToken($this->apiKey)
                ->get("https://huggingface.co/api/models/{$model}")
                ->json();

            $taskType = $modelInfo['pipeline_tag'] ?? null;

            return [
                'type' => $this->determineModelType($taskType),
                'url' => $model,
            ];
        } catch (\Throwable $e) {
            // Default to text type if we can't determine the type
            return [
                'type' => 'text',
                'url' => $model,
            ];
        }
    }

    /**
     * Determine the model type based on the task.
     */
    protected function determineModelType(?string $taskType): string {
        if ($taskType && isset($this->modelTypes[$taskType])) {
            return $this->modelTypes[$taskType];
        }

        return 'text';
    }

    /**
     * Check if a model is supported.
     */
    public function isModelSupported(string $model): bool {
        return array_key_exists($model, $this->models);
    }
}

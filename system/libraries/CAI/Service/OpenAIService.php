<?php

use OpenAI\OpenAI;

class CAI_Service_OpenAIService extends CAI_ServiceAbstract {
    protected $apiKey;

    protected $organization;

    /**
     * @var \OpenAI\Client
     */
    protected $openAI;

    public function __construct($options = []) {
        $this->apiKey = carr::get($options, 'api_key');
        $this->organization = carr::get($options, 'organization');
        $project = carr::get($options, 'project');
        $openAI = OpenAI::factory()
            ->withApiKey($this->apiKey)
            ->withOrganization($this->organization)
            ->withProject($project)
            // ->withHttpHeader('OpenAI-Beta', 'assistants=v2')
            ->withHttpClient(new \GuzzleHttp\Client(['timeout' => CF::config('ai.openai.request_timeout', 30)]))
            ->make();
        $this->openAI = $openAI;
    }

    public function ask(array $options = []) {
        $result = $this->openAI->chat()->create([
            'model' => $this->model,
            'temperature' => $this->temperature,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $this->prompt,
                ],
            ],
        ]);

        return c::optional($result)->choices[0]->message->content;
    }

    public function image(array $options = []) {
    }

    public function getOpenAI() {
        return $this->openAI;
    }
}

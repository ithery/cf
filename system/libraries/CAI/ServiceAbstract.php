<?php

abstract class CAI_ServiceAbstract {
    /**
     * LLM model.
     */
    protected string $model;

    /**
     * LLM agent.
     */
    protected string $agent;

    /**
     * Stream Response.
     */
    protected bool $stream = false;

    /**
     * Raw Response.
     */
    protected bool $raw = true;

    /**
     * Raw Response.
     */
    protected float $temperature = 0.7;

    /**
     * LLM prompt text.
     */
    protected string $prompt;

    /**
     * Set LLM model.
     */
    public function setModel(string $model): self {
        $this->model = $model;

        return $this;
    }

    /**
     * Set LLM agent.
     */
    public function setAgent(string $agent): self {
        $this->agent = $agent;

        return $this;
    }

    /**
     * Set stream response.
     */
    public function setStream(bool $stream): self {
        $this->stream = $stream;

        return $this;
    }

    /**
     * Set response raw.
     */
    public function setRaw(bool $raw): self {
        $this->raw = $raw;

        return $this;
    }

    /**
     * Set LLM prompt text.
     */
    public function setTemperature(float $temperature): self {
        $this->temperature = $temperature;

        return $this;
    }

    /**
     * Set LLM prompt text.
     */
    public function setPrompt(string $prompt): self {
        $this->prompt = $prompt;

        return $this;
    }

    /**
     * Set LLM prompt text.
     */
    abstract public function ask();

    /**
     * Generate Images.
     */
    abstract public function images(array $options);
}

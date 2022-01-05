<?php

use PHPUnit\Framework\Assert as PHPUnit;

trait CComponent_Testing_Concern_MakesAssertionsTrait {
    public function assertSet($name, $value, $strict = false) {
        $actual = $this->get($name);

        if (!is_string($value) && is_callable($value)) {
            PHPUnit::assertTrue($value($actual));
        } else {
            $strict ? PHPUnit::assertSame($value, $actual) : PHPUnit::assertEquals($value, $actual);
        }

        return $this;
    }

    public function assertNotSet($name, $value, $strict = false) {
        $actual = $this->get($name);

        $strict ? PHPUnit::assertNotSame($value, $actual) : PHPUnit::assertNotEquals($value, $actual);

        return $this;
    }

    public function assertCount($name, $value) {
        PHPUnit::assertCount($value, $this->get($name));

        return $this;
    }

    public function assertPayloadSet($name, $value) {
        if (is_callable($value)) {
            PHPUnit::assertTrue($value(c::get($this->payload['serverMemo']['data'], $name)));
        } else {
            PHPUnit::assertEquals($value, c::get($this->payload['serverMemo']['data'], $name));
        }

        return $this;
    }

    public function assertPayloadNotSet($name, $value) {
        if (is_callable($value)) {
            PHPUnit::assertFalse($value(c::get($this->payload['serverMemo']['data'], $name)));
        } else {
            PHPUnit::assertNotEquals($value, c::get($this->payload['serverMemo']['data'], $name));
        }

        return $this;
    }

    public function assertSee($values, $escape = true) {
        foreach (carr::wrap($values) as $value) {
            PHPUnit::assertStringContainsString(
                $escape ? c::e($value) : $value,
                $this->stripOutInitialData($this->lastRenderedDom)
            );
        }

        return $this;
    }

    public function assertDontSee($values, $escape = true) {
        foreach (carr::wrap($values) as $value) {
            PHPUnit::assertStringNotContainsString(
                $escape ? c::e($value) : $value,
                $this->stripOutInitialData($this->lastRenderedDom)
            );
        }

        return $this;
    }

    public function assertSeeHtml($values) {
        foreach (carr::wrap($values) as $value) {
            PHPUnit::assertStringContainsString(
                $value,
                $this->stripOutInitialData($this->lastRenderedDom)
            );
        }

        return $this;
    }

    public function assertDontSeeHtml($values) {
        foreach (carr::wrap($values) as $value) {
            PHPUnit::assertStringNotContainsString(
                $value,
                $this->stripOutInitialData($this->lastRenderedDom)
            );
        }

        return $this;
    }

    public function assertSeeHtmlInOrder(array $values) {
        PHPUnit::assertThat(
            $values,
            new CTesting_Constraint_SeeInOrder($this->stripOutInitialData($this->lastRenderedDom))
        );

        return $this;
    }

    public function assertSeeInOrder(array $values) {
        PHPUnit::assertThat(
            array_map([c::class, 'e'], ($values)),
            new CTesting_Constraint_SeeInOrder($this->stripOutInitialData($this->lastRenderedDom))
        );

        return $this;
    }

    protected function stripOutInitialData($subject) {
        $subject = preg_replace('/((?:[\n\s+]+)?wire:initial-data=\".+}"\n?|(?:[\n\s+]+)?wire:id=\"[^"]*"\n?)/m', '', $subject);

        return CCompponent_Feature_SupportRootElementTracking::stripOutEndingMarker($subject);
    }

    public function assertEmitted($value, ...$params) {
        $result = $this->testEmitted($value, $params);

        PHPUnit::assertTrue($result['test'], "Failed asserting that an event [{$value}] was fired{$result['assertionSuffix']}");

        return $this;
    }

    public function assertNotEmitted($value, ...$params) {
        $result = $this->testEmitted($value, $params);

        PHPUnit::assertFalse($result['test'], "Failed asserting that an event [{$value}] was not fired{$result['assertionSuffix']}");

        return $this;
    }

    public function assertEmittedTo($target, $value, ...$params) {
        $this->assertEmitted($value, ...$params);
        $result = $this->testEmittedTo($target, $value);

        PHPUnit::assertTrue($result, "Failed asserting that an event [{$value}] was fired to {$target}.");

        return $this;
    }

    public function assertEmittedUp($value, ...$params) {
        $this->assertEmitted($value, ...$params);
        $result = $this->testEmittedUp($value);

        PHPUnit::assertTrue($result, "Failed asserting that an event [{$value}] was fired up.");

        return $this;
    }

    protected function testEmitted($value, $params) {
        $assertionSuffix = '.';

        if (empty($params)) {
            $test = c::collect(c::get($this->payload, 'effects.emits'))->contains('event', '=', $value);
        } elseif (!is_string($params[0]) && is_callable($params[0])) {
            $event = c::collect(c::get($this->payload, 'effects.emits'))->first(function ($item) use ($value) {
                return $item['event'] === $value;
            });

            $test = $event && $params[0]($event['event'], $event['params']);
        } else {
            $test = (bool) c::collect(c::get($this->payload, 'effects.emits'))->first(function ($item) use ($value, $params) {
                return $item['event'] === $value
                    && $item['params'] === $params;
            });

            $encodedParams = json_encode($params);
            $assertionSuffix = " with parameters: {$encodedParams}";
        }

        return [
            'test' => $test,
            'assertionSuffix' => $assertionSuffix,
        ];
    }

    protected function testEmittedTo($target, $value) {
        return (bool) c::collect(c::get($this->payload, 'effects.emits'))->first(function ($item) use ($target, $value) {
            return $item['event'] === $value
                && $item['to'] === $target;
        });
    }

    protected function testEmittedUp($value) {
        return (bool) c::collect(c::get($this->payload, 'effects.emits'))->first(function ($item) use ($value) {
            return $item['event'] === $value
                && $item['ancestorsOnly'] === true;
        });
    }

    public function assertDispatchedBrowserEvent($name, $data = null) {
        $assertionSuffix = '.';

        if (is_null($data)) {
            $test = c::collect($this->payload['effects']['dispatches'])->contains('event', '=', $name);
        } elseif (is_callable($data)) {
            $event = c::collect($this->payload['effects']['dispatches'])->first(function ($item) use ($name) {
                return $item['event'] === $name;
            });

            $test = $event && $data($event['event'], $event['data']);
        } else {
            $test = (bool) c::collect($this->payload['effects']['dispatches'])->first(function ($item) use ($name, $data) {
                return $item['event'] === $name
                    && $item['data'] === $data;
            });
            $encodedData = json_encode($data);
            $assertionSuffix = " with parameters: {$encodedData}";
        }

        PHPUnit::assertTrue($test, "Failed asserting that an event [{$name}] was fired{$assertionSuffix}");

        return $this;
    }

    public function assertHasErrors($keys = []) {
        $errors = $this->lastErrorBag;

        PHPUnit::assertTrue($errors->isNotEmpty(), 'Component has no errors.');

        $keys = (array) $keys;

        foreach ($keys as $key => $value) {
            if (is_int($key)) {
                PHPUnit::assertTrue($errors->has($value), "Component missing error: ${value}");
            } else {
                $failed = c::optional($this->lastValidator)->failed() ?: [];
                $rules = array_keys(carr::get($failed, $key, []));

                foreach ((array) $value as $rule) {
                    PHPUnit::assertContains(cstr::studly($rule), $rules, "Component has no [{$rule}] errors for [{$key}] attribute.");
                }
            }
        }

        return $this;
    }

    public function assertHasNoErrors($keys = []) {
        $errors = $this->lastErrorBag;

        if (empty($keys)) {
            PHPUnit::assertTrue($errors->isEmpty(), 'Component has errors: "' . implode('", "', $errors->keys()) . '"');

            return $this;
        }

        $keys = (array) $keys;

        foreach ($keys as $key => $value) {
            if (is_int($key)) {
                PHPUnit::assertFalse($errors->has($value), "Component has error: ${value}");
            } else {
                $failed = c::optional($this->lastValidator)->failed() ?: [];
                $rules = array_keys(carr::get($failed, $key, []));

                foreach ((array) $value as $rule) {
                    PHPUnit::assertNotContains(cstr::studly($rule), $rules, "Component has [{$rule}] errors for [{$key}] attribute.");
                }
            }
        }

        return $this;
    }

    public function assertRedirect($uri = null) {
        PHPUnit::assertArrayHasKey(
            'redirect',
            $this->payload['effects'],
            'Component did not perform a redirect.'
        );

        if (!is_null($uri)) {
            PHPUnit::assertSame(c::url($uri), c::url($this->payload['effects']['redirect']));
        }

        return $this;
    }

    public function assertNoRedirect() {
        PHPUnit::assertTrue(!isset($this->payload['effects']['redirect']));

        return $this;
    }

    public function assertViewIs($name) {
        PHPUnit::assertEquals($name, $this->lastRenderedView->getName());

        return $this;
    }

    public function assertViewHas($key, $value = null) {
        if (is_null($value)) {
            PHPUnit::assertArrayHasKey($key, $this->lastRenderedView->gatherData());
        } elseif ($value instanceof \Closure) {
            PHPUnit::assertTrue($value($this->lastRenderedView->gatherData()[$key]));
        } elseif ($value instanceof CModel) {
            PHPUnit::assertTrue($value->is($this->lastRenderedView->gatherData()[$key]));
        } else {
            PHPUnit::assertEquals($value, $this->lastRenderedView->gatherData()[$key]);
        }

        return $this;
    }

    public function assertFileDownloaded($filename = null, $content = null, $contentType = null) {
        $downloadEffect = c::get($this->lastResponse, 'original.effects.download');

        if ($filename) {
            PHPUnit::assertEquals(
                $filename,
                c::get($downloadEffect, 'name')
            );
        } else {
            PHPUnit::assertNotNull($downloadEffect);
        }

        if ($content) {
            $downloadedContent = c::get($this->lastResponse, 'original.effects.download.content');

            PHPUnit::assertEquals(
                $content,
                base64_decode($downloadedContent)
            );
        }

        if ($contentType) {
            PHPUnit::assertEquals(
                $contentType,
                c::get($this->lastResponse, 'original.effects.download.contentType')
            );
        }

        return $this;
    }
}

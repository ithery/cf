<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 7, 2018, 8:06:26 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElastic_Connection_Strategy_StrategyFactory {

    /**
     * @param mixed|callable|string|StrategyInterface $strategyName
     *
     * @throws CElastic_Exception_InvalidException
     *
     * @return CElastic_Connection_Strategy_StrategyInterface
     */
    public static function create($strategyName) {
        if ($strategyName instanceof CElastic_Connection_Strategy_StrategyInterface) {
            return $strategyName;
        }
        if (CElastic_Connection_Strategy_CallbackStrategy::isValid($strategyName)) {
            return new CElastic_Connection_Strategy_CallbackStrategy($strategyName);
        }
        if (is_string($strategyName)) {
            $requiredInterface = 'CElastic_Connection_Strategy_StrategyInterface';
            $predefinedStrategy = 'CElastic_Connection_Strategy_' . $strategyName;
            if (class_exists($predefinedStrategy) && class_implements($predefinedStrategy, $requiredInterface)) {
                return new $predefinedStrategy();
            }
            if (class_exists($strategyName) && class_implements($strategyName, $requiredInterface)) {
                return new $strategyName();
            }
        }
        throw new CElastic_Exception_InvalidException('Can\'t create strategy ' . $strategyName . ' instance by given argument');
    }

}

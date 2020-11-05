<?php

/**
 * Description of ConstraintTrait
 *
 * @author Hery
 */
trait CQC_UnitTest_Trait_ConstraintTrait {

    /**
     * @return CQC_UnitTest_Constraint_LogicalAnd
     * @throws Exception
     */
    public function logicalAnd() {
        $constraints = func_get_args();

        $constraint = new LogicalAnd;
        $constraint->setConstraints($constraints);

        return $constraint;
    }

    /**
     * @return CQC_UnitTest_Constraint_LogicalOr
     */
    public function logicalOr() {
        $constraints = func_get_args();

        $constraint = new LogicalOr;
        $constraint->setConstraints($constraints);

        return $constraint;
    }

    /**
     * @return CQC_UnitTest_Constraint_LogicalNot
     */
    public function logicalNot(Constraint $constraint) {
        return new LogicalNot($constraint);
    }

    /**
     * @return CQC_UnitTest_Constraint_LogicalXor
     */
    public function logicalXor() {
        $constraints = func_get_args();

        $constraint = new LogicalXor;
        $constraint->setConstraints($constraints);

        return $constraint;
    }

    /**
     * @return CQC_UnitTest_Constraint_IsAnything
     */
    public function anything() {
        return new IsAnything;
    }

    /**
     * @return CQC_UnitTest_Constraint_IsTrue
     */
    public function isTrue() {
        return new IsTrue;
    }

    /**
     * @return CQC_UnitTest_Constraint_Callback
     */
    public function callback(callable $callback) {
        return new Callback($callback);
    }

    /**
     * @return CQC_UnitTest_Constraint_IsFalse
     */
    public function isFalse() {
        return new IsFalse;
    }

    /**
     * @return CQC_UnitTest_Constraint_String_IsJson
     */
    public function isJson() {
        return new CQC_UnitTest_Constraint_String_IsJson();
    }

    /**
     * @return CQC_UnitTest_Constraint_IsNull
     */
    public function isNull() {
        return new IsNull;
    }

    /**
     * @return CQC_UnitTest_Constraint_IsFinite
     */
    public function isFinite() {
        return new IsFinite;
    }

    /**
     * @return CQC_UnitTest_Constraint_IsInfinite
     */
    public function isInfinite() {
        return new IsInfinite;
    }

    /**
     * @return CQC_UnitTest_Constraint_IsNan
     */
    public function isNan() {
        return new IsNan;
    }

    /**
     * @return CQC_UnitTest_Constraint_TraversableContainsEqual
     */
    public function containsEqual($value) {
        return new TraversableContainsEqual($value);
    }

    /**
     * @return CQC_UnitTest_Constraint_TraversableContainsIdentical
     */
    public function containsIdentical($value) {
        return new TraversableContainsIdentical($value);
    }

    /**
     * @return CQC_UnitTest_Constraint_TraversableContainsOnly
     */
    public function containsOnly($type) {
        return new TraversableContainsOnly($type);
    }

    /**
     * @return CQC_UnitTest_Constraint_TraversableContainsOnly
     */
    public function containsOnlyInstancesOf($className) {
        return new TraversableContainsOnly($className, false);
    }

    /**
     * @param int|string $key
     * @return CQC_UnitTest_Constraint_ArrayHasKey
     */
    public function arrayHasKey($key) {
        return new ArrayHasKey($key);
    }

    /**
     * @return CQC_UnitTest_Constraint_IsEqual
     */
    public function equalTo($value) {
        return new IsEqual($value, 0.0, false, false);
    }

    /**
     * @return CQC_UnitTest_Constraint_IsEqualCanonicalizing
     */
    public function equalToCanonicalizing($value) {
        return new IsEqualCanonicalizing($value);
    }

    /**
     * @return CQC_UnitTest_Constraint_IsEqualIgnoringCase
     */
    public function equalToIgnoringCase($value) {
        return new IsEqualIgnoringCase($value);
    }

    /**
     * @return CQC_UnitTest_Constraint_IsEqualWithDelta
     */
    public function equalToWithDelta($value, float $delta) {
        return new IsEqualWithDelta($value, $delta);
    }

    /**
     * @return CQC_UnitTest_Constraint_IsEmpty
     */
    public function isEmpty() {
        return new IsEmpty;
    }

    /**
     * @return CQC_UnitTest_Constraint_IsWritable
     */
    public function isWritable() {
        return new IsWritable;
    }

    /**
     * @return CQC_UnitTest_Constraint_IsReadable
     */
    public function isReadable() {
        return new IsReadable;
    }

    /**
     * @return CQC_UnitTest_Constraint_DirectoryExists
     */
    public function directoryExists() {
        return new DirectoryExists;
    }

    /**
     * @return CQC_UnitTest_Constraint_FileExists
     */
    public function fileExists() {
        return new FileExists;
    }

    /**
     * @return CQC_UnitTest_Constraint_GreaterThan
     */
    public function greaterThan($value) {
        return new GreaterThan($value);
    }

    /**
     * @return CQC_UnitTest_Constraint_LogicalOr
     */
    public function greaterThanOrEqual($value) {
        return $this->logicalOr(
                        new IsEqual($value), new GreaterThan($value)
        );
    }

    /**
     * @return CQC_UnitTest_Constraint_ClassHasAttribute
     */
    public function classHasAttribute($attributeName) {
        return new ClassHasAttribute($attributeName);
    }

    /**
     * @return CQC_UnitTest_Constraint_ClassHasStaticAttribute
     */
    public function classHasStaticAttribute($attributeName) {
        return new ClassHasStaticAttribute($attributeName);
    }

    /**
     * @return CQC_UnitTest_Constraint_ObjectHasAttribute
     */
    public function objectHasAttribute($attributeName) {
        return new ObjectHasAttribute($attributeName);
    }

    /**
     * @return CQC_UnitTest_Constraint_IsIdentical
     */
    public function identicalTo($value) {
        return new IsIdentical($value);
    }

    /**
     * @return CQC_UnitTest_Constraint_IsInstanceOf
     */
    public function isInstanceOf($className) {
        return new IsInstanceOf($className);
    }

    /**
     * @return CQC_UnitTest_Constraint_IsType
     */
    public function isType($type) {
        return new IsType($type);
    }

    /**
     * @return CQC_UnitTest_Constraint_LessThan
     */
    public function lessThan($value) {
        return new LessThan($value);
    }

    /**
     * @return CQC_UnitTest_Constraint_LogicalOr
     */
    public function lessThanOrEqual($value) {
        return $this->logicalOr(
                        new IsEqual($value), new LessThan($value)
        );
    }

    /**
     * @return CQC_UnitTest_Constraint_RegularExpression
     */
    public function matchesRegularExpression($pattern) {
        return new RegularExpression($pattern);
    }

    /**
     * @return CQC_UnitTest_Constraint_StringMatchesFormatDescription
     */
    public function matches($string) {
        return new StringMatchesFormatDescription($string);
    }

    /**
     * @return CQC_UnitTest_Constraint_StringStartsWith
     */
    public function stringStartsWith($prefix) {
        return new StringStartsWith($prefix);
    }

    /**
     * @return CQC_UnitTest_Constraint_StringContains
     */
    public function stringContains($string, $case = true) {
        return new StringContains($string, $case);
    }

    /**
     * @return CQC_UnitTest_Constraint_StringEndsWith
     */
    public function stringEndsWith($suffix) {
        return new StringEndsWith($suffix);
    }

    /**
     * @return CQC_UnitTest_Constraint_Count
     */
    public function countOf(int $count) {
        return new Count($count);
    }

    /**
     * @return CQC_UnitTest_Constraint_ObjectEquals
     */
    public function objectEquals(object $object, $method = 'equals') {
        return new ObjectEquals($object, $method);
    }

}

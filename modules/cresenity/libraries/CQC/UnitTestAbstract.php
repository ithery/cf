<?php

/**
 * Description of UnitTestAbstract
 *
 * @author Hery
 */
abstract class CQC_UnitTestAbstract extends CQC_QCAbstract {

    /**
     *
     * @var CQC_UnitTest_AssertResultCollection
     */
    protected $assertResults;

    /**
     * This method is called before each test.
     * @return void
     */
    public function setUp() {
        
    }

    /**
     * This method is called after each test.
     * @return void
     */
    public function tearDown() {
        
    }

    public function build() {
        if ($this->assertResults) {
            unset($this->assertResults);
        }
        $this->assertResults = new CQC_UnitTest_AssertResultCollection();
    }

    public function destroy() {
        
    }

    public function result() {
        return $this->assertResults;
    }

}

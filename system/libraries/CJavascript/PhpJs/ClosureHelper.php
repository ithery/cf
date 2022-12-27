<?php
use PhpParser\Node;
use PhpParser\Node\Stmt;

class CJavascript_PhpJs_ClosureHelper {
    private $arrayIndex = null;

    private $arrayIndexStack = [];

    private $isNamespace = false;

    /**
     * @var Node\Name
     */
    private $namespace;

    /**
     * @var Stmt\ClassConst[]
     */
    private $classConstants = [];

    /**
     * @var Stmt\ClassMethod[]
     */
    private $classPublicMethods = [];

    private $classPrivateMethods = [];

    private $classHasConstructor = false;

    private $classConstructorParams = null;

    /**
     * @var Stmt\PropertyProperty[]
     */
    private $classStaticProperties = [];

    private $classIsInterface = false;

    private $nextClassIsInterface = false;

    private $classStack = [];

    private $currentClassName = '';

    private $currentMethodName = '';

    private $currentFunctionName = '';

    private $classHasMagicMethods = false;

    private $classPrivatePropertiesNames = [];

    private $classPrivateMethodsNames = [];

    private $isInsidePrivateMethod = false;

    private $loopIndex = 0;

    private $varStack = [];

    private $varScopeStack = [];

    private $usedVarStack = [];

    private $usedVarScopeScack = [];

    private $isDefScope = false;

    public function pushArrayIndex() {
        $this->arrayIndexStack[] = $this->arrayIndex;
        $this->arrayIndex = 0;
    }

    public function popArrayIndex() {
        $this->arrayIndex = array_pop($this->arrayIndexStack);
    }

    public function arrayIndex() {
        return $this->arrayIndex++;
    }

    public function setNamespace($is, $namespace) {
        $this->isNamespace = $is;
        $this->namespace = $namespace;
    }

    public function isNamespace() {
        return $this->isNamespace;
    }

    public function pushClass($className) {
        $this->classStack[] = [
            0 => $this->classConstants,
            1 => $this->classPublicMethods,
            2 => $this->classConstructorParams,
            3 => $this->classStaticProperties,
            4 => $this->classIsInterface,
            5 => $this->currentClassName,
            6 => $this->currentMethodName,
            7 => $this->currentFunctionName,
            8 => $this->classHasMagicMethods,
            9 => $this->classHasConstructor,
            10 => $this->classPrivatePropertiesNames,
            11 => $this->classPrivateMethodsNames,
            12 => $this->classPrivateMethods,
            13 => $this->isInsidePrivateMethod,
            14 => $this->loopIndex
        ];
        $this->classConstants = [];
        $this->classPublicMethods = [];
        $this->classConstructorParams = null;
        $this->classStaticProperties = [];
        $this->classIsInterface = $this->nextClassIsInterface;
        $this->nextClassIsInterface = false;
        $this->currentClassName = $className;
        $this->currentMethodName = '';
        $this->currentFunctionName = '';
        $this->classHasMagicMethods = false;
        $this->classHasConstructor = false;
        $this->classPrivatePropertiesNames = [];
        $this->classPrivateMethodsNames = [];
        $this->classPrivateMethods = [];
        $this->isInsidePrivateMethod = false;
        $this->loopIndex = 0;
    }

    public function popClass() {
        $data = array_pop($this->classStack);
        $this->classConstants = $data[0];
        $this->classPublicMethods = $data[1];
        $this->classConstructorParams = $data[2];
        $this->classStaticProperties = $data[3];
        $this->classIsInterface = $data[4];
        $this->currentClassName = $data[5];
        $this->currentMethodName = $data[6];
        $this->currentFunctionName = $data[7];
        $this->classHasMagicMethods = $data[8];
        $this->classHasConstructor = $data[9];
        $this->classPrivatePropertiesNames = $data[10];
        $this->classPrivateMethodsNames = $data[11];
        $this->classPrivateMethods = $data[12];
        $this->isInsidePrivateMethod = $data[13];
        $this->loopIndex = $data[14];
    }

    /**
     * @return Stmt\ClassConst[]
     */
    public function getClassConstants() {
        return $this->classConstants;
    }

    /**
     * @return Stmt\ClassMethod
     */
    public function getClassConstructor() {
        return $this->classConstructorParams;
    }

    /**
     * @return Stmt\ClassMethod[]
     */
    public function getClassPublicMethods() {
        return $this->classPublicMethods;
    }

    /**
     * @return Stmt\ClassMethod[]
     */
    public function getClassPrivateMethods() {
        return $this->classPrivateMethods;
    }

    public function setIsInsidePrivateMethod($isOrNot) {
        $this->isInsidePrivateMethod = $isOrNot;
    }

    public function isInsidePrivateMethod() {
        return $this->isInsidePrivateMethod;
    }

    /**
     * @return Stmt\PropertyProperty[]
     */
    public function getClassStaticProperties() {
        return $this->classStaticProperties;
    }

    /**
     * @return bool
     */
    public function classIsInterface() {
        return $this->classIsInterface;
    }

    /**
     * @param Stmt\ClassConst $classConstant
     */
    public function addClassConstants(Stmt\ClassConst $classConstant) {
        $this->classConstants[] = $classConstant;
    }

    public function addClassPrivatePropertyName($name) {
        $this->classPrivatePropertiesNames[] = $name;
    }

    public function addClassPrivateMethodName($name) {
        $this->classPrivateMethodsNames[] = $name;
    }

    public function addClassPrivateMethod($method) {
        $this->classPrivateMethods[] = $method;
    }

    public function isClassPrivateProperty($name) {
        return in_array($name, $this->classPrivatePropertiesNames);
    }

    public function isClassPrivateMethod($name) {
        return in_array($name, $this->classPrivateMethodsNames);
    }

    public function hasClassPrivateMethodsOrProperties() {
        return count($this->classPrivatePropertiesNames) > 0 || count($this->classPrivateMethodsNames) > 0;
    }

    /**
     * @param $classConstructorParams
     */
    public function setClassConstructorParams($classConstructorParams) {
        $this->classHasConstructor = true;
        $this->classConstructorParams = $classConstructorParams;
    }

    public function getClassConstructorParams() {
        return $this->classConstructorParams;
    }

    public function classHasConstructor() {
        return $this->classHasConstructor;
    }

    /**
     * @param bool $isInterface
     */
    public function setClassIsInterface($isInterface) {
        $this->classIsInterface = $isInterface;
    }

    public function setNextClassIsInterface() {
        $this->nextClassIsInterface = true;
    }

    public function setClassHasMagicMethods() {
        $this->classHasMagicMethods = true;
    }

    public function getClassHasMagicMethods() {
        return $this->classHasMagicMethods;
    }

    public function getClassName() {
        $className = '';
        if ($this->isNamespace) {
            $className .= $this->getNamespaceName();
            $className .= '\\\\';
        }
        $className .= $this->currentClassName;

        return $className;
    }

    public function getSimpleClassName() {
        return $this->currentClassName;
    }

    public function getNamespaceName() {
        $name = '';
        if ($this->isNamespace) {
            $name .= join('\\\\', $this->namespace->parts);
        }

        return $name;
    }

    public function getMethodName() {
        $methodName = '';
        if ($this->isNamespace) {
            $methodName .= $this->getNamespaceName();
            $methodName .= '\\\\';
        }
        $methodName .= $this->currentClassName;
        $methodName .= '::';
        $methodName .= $this->currentMethodName;

        return $methodName;
    }

    public function getFunctionName() {
        $functionName = '';
        if ($this->isNamespace) {
            $functionName .= $this->getNamespaceName();
            $functionName .= '\\\\';
        }
        $functionName .= $this->currentFunctionName;

        return $functionName;
    }

    public function setMethodName($name) {
        $this->currentMethodName = $name;
    }

    public function setFunctionName($name) {
        $this->currentFunctionName = $name;
    }

    /**
     * @param Stmt\ClassMethod $classMethod
     */
    public function addClassPublicMethod(Stmt\ClassMethod $classMethod) {
        $this->classPublicMethods[] = $classMethod;
    }

    /**
     * @param Stmt\PropertyProperty $classStaticProperty
     */
    public function addClassStaticProperty($classStaticProperty) {
        $this->classStaticProperties[] = $classStaticProperty;
    }

    public function pushVarScope() {
        $this->varScopeStack[] = $this->varStack;
        $this->usedVarScopeScack[] = $this->usedVarStack;
        $this->varStack = [];
        $this->usedVarStack = [];
    }

    public function popVarScope() {
        if (count($this->varStack)) {
            throw new \Exception('var stack is not empty `' . join(',', $this->varStack) . '`');
        }
        $this->varStack = array_pop($this->varScopeStack);
        $this->usedVarStack = array_pop($this->usedVarScopeScack);
    }

    public function isDefScope($isOrNot) {
        $this->isDefScope = $isOrNot;
    }

    public function pushVar($name) {
        if ($name == 'this') {
            return;
        }
        if ($this->isDefScope) {
            $this->usedVarStack[] = $name;

            return;
        }
        if (!in_array($name, $this->varStack) && !in_array($name, $this->usedVarStack)) {
            $this->varStack[] = $name;
        }
    }

    public function useVar($name) {
        $this->usedVarStack[] = $name;
    }

    public function fileExtend(CJavascript_PhpJs_ClosureHelper $helper) {
        $this->usedVarStack = $helper->usedVarStack;
    }

    public function globalVar() {
    }

    public function getVarsDef() {
        $ret = $this->varStack;
        $this->usedVarStack = array_merge($this->usedVarStack, $this->varStack);
        $this->varStack = [];

        return $ret;
    }

    public function pushLoop() {
        $this->loopIndex++;

        return "__loop{$this->loopIndex}";
    }

    public function popLoop() {
        $this->loopIndex--;
    }

    public function getLoopName($num) {
        $index = $this->loopIndex - $num + 1;

        return "__loop{$index}";
    }
}

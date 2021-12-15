
(function(l, r) { if (!l || l.getElementById('livereloadscript')) return; r = l.createElement('script'); r.async = 1; r.src = '//' + (self.location.host || 'localhost').split(':')[0] + ':12345/livereload.js?snipver=1'; r.id = 'livereloadscript'; l.getElementsByTagName('head')[0].appendChild(r) })(self.document);
(function (factory) {
	typeof define === 'function' && define.amd ? define(factory) :
	factory();
})((function () { 'use strict';

	var commonjsGlobal = typeof globalThis !== 'undefined' ? globalThis : typeof window !== 'undefined' ? window : typeof global !== 'undefined' ? global : typeof self !== 'undefined' ? self : {};

	function commonjsRequire (path) {
		throw new Error('Could not dynamically require "' + path + '". Please configure the dynamicRequireTargets or/and ignoreDynamicRequires option of @rollup/plugin-commonjs appropriately for this require call to work.');
	}

	var ceil = Math.ceil;
	var floor = Math.floor;

	// `ToInteger` abstract operation
	// https://tc39.es/ecma262/#sec-tointeger
	var toInteger$4 = function (argument) {
	  return isNaN(argument = +argument) ? 0 : (argument > 0 ? floor : ceil)(argument);
	};

	var check = function (it) {
	  return it && it.Math == Math && it;
	};

	// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
	var global$n =
	  // eslint-disable-next-line es/no-global-this -- safe
	  check(typeof globalThis == 'object' && globalThis) ||
	  check(typeof window == 'object' && window) ||
	  // eslint-disable-next-line no-restricted-globals -- safe
	  check(typeof self == 'object' && self) ||
	  check(typeof commonjsGlobal == 'object' && commonjsGlobal) ||
	  // eslint-disable-next-line no-new-func -- fallback
	  (function () { return this; })() || Function('return this')();

	var shared$3 = {exports: {}};

	var global$m = global$n;

	var setGlobal$3 = function (key, value) {
	  try {
	    // eslint-disable-next-line es/no-object-defineproperty -- safe
	    Object.defineProperty(global$m, key, { value: value, configurable: true, writable: true });
	  } catch (error) {
	    global$m[key] = value;
	  } return value;
	};

	var global$l = global$n;
	var setGlobal$2 = setGlobal$3;

	var SHARED = '__core-js_shared__';
	var store$5 = global$l[SHARED] || setGlobal$2(SHARED, {});

	var sharedStore = store$5;

	var store$4 = sharedStore;

	(shared$3.exports = function (key, value) {
	  return store$4[key] || (store$4[key] = value !== undefined ? value : {});
	})('versions', []).push({
	  version: '3.18.0',
	  mode: 'global',
	  copyright: '© 2021 Denis Pushkarev (zloirock.ru)'
	});

	// `RequireObjectCoercible` abstract operation
	// https://tc39.es/ecma262/#sec-requireobjectcoercible
	var requireObjectCoercible$4 = function (it) {
	  if (it == undefined) throw TypeError("Can't call method on " + it);
	  return it;
	};

	var requireObjectCoercible$3 = requireObjectCoercible$4;

	// `ToObject` abstract operation
	// https://tc39.es/ecma262/#sec-toobject
	var toObject$6 = function (argument) {
	  return Object(requireObjectCoercible$3(argument));
	};

	var toObject$5 = toObject$6;

	var hasOwnProperty = {}.hasOwnProperty;

	var has$9 = Object.hasOwn || function hasOwn(it, key) {
	  return hasOwnProperty.call(toObject$5(it), key);
	};

	var id = 0;
	var postfix = Math.random();

	var uid$2 = function (key) {
	  return 'Symbol(' + String(key === undefined ? '' : key) + ')_' + (++id + postfix).toString(36);
	};

	// `isCallable` abstract operation
	// https://tc39.es/ecma262/#sec-iscallable
	var isCallable$i = function (argument) {
	  return typeof argument === 'function';
	};

	var global$k = global$n;
	var isCallable$h = isCallable$i;

	var aFunction = function (argument) {
	  return isCallable$h(argument) ? argument : undefined;
	};

	var getBuiltIn$9 = function (namespace, method) {
	  return arguments.length < 2 ? aFunction(global$k[namespace]) : global$k[namespace] && global$k[namespace][method];
	};

	var getBuiltIn$8 = getBuiltIn$9;

	var engineUserAgent = getBuiltIn$8('navigator', 'userAgent') || '';

	var global$j = global$n;
	var userAgent$3 = engineUserAgent;

	var process$3 = global$j.process;
	var Deno = global$j.Deno;
	var versions = process$3 && process$3.versions || Deno && Deno.version;
	var v8 = versions && versions.v8;
	var match, version;

	if (v8) {
	  match = v8.split('.');
	  version = match[0] < 4 ? 1 : match[0] + match[1];
	} else if (userAgent$3) {
	  match = userAgent$3.match(/Edge\/(\d+)/);
	  if (!match || match[1] >= 74) {
	    match = userAgent$3.match(/Chrome\/(\d+)/);
	    if (match) version = match[1];
	  }
	}

	var engineV8Version = version && +version;

	var fails$b = function (exec) {
	  try {
	    return !!exec();
	  } catch (error) {
	    return true;
	  }
	};

	/* eslint-disable es/no-symbol -- required for testing */

	var V8_VERSION$1 = engineV8Version;
	var fails$a = fails$b;

	// eslint-disable-next-line es/no-object-getownpropertysymbols -- required for testing
	var nativeSymbol = !!Object.getOwnPropertySymbols && !fails$a(function () {
	  var symbol = Symbol();
	  // Chrome 38 Symbol has incorrect toString conversion
	  // `get-own-property-symbols` polyfill symbols converted to object are not Symbol instances
	  return !String(symbol) || !(Object(symbol) instanceof Symbol) ||
	    // Chrome 38-40 symbols are not inherited from DOM collections prototypes to instances
	    !Symbol.sham && V8_VERSION$1 && V8_VERSION$1 < 41;
	});

	/* eslint-disable es/no-symbol -- required for testing */

	var NATIVE_SYMBOL$1 = nativeSymbol;

	var useSymbolAsUid = NATIVE_SYMBOL$1
	  && !Symbol.sham
	  && typeof Symbol.iterator == 'symbol';

	var global$i = global$n;
	var shared$2 = shared$3.exports;
	var has$8 = has$9;
	var uid$1 = uid$2;
	var NATIVE_SYMBOL = nativeSymbol;
	var USE_SYMBOL_AS_UID$1 = useSymbolAsUid;

	var WellKnownSymbolsStore = shared$2('wks');
	var Symbol$1 = global$i.Symbol;
	var createWellKnownSymbol = USE_SYMBOL_AS_UID$1 ? Symbol$1 : Symbol$1 && Symbol$1.withoutSetter || uid$1;

	var wellKnownSymbol$h = function (name) {
	  if (!has$8(WellKnownSymbolsStore, name) || !(NATIVE_SYMBOL || typeof WellKnownSymbolsStore[name] == 'string')) {
	    if (NATIVE_SYMBOL && has$8(Symbol$1, name)) {
	      WellKnownSymbolsStore[name] = Symbol$1[name];
	    } else {
	      WellKnownSymbolsStore[name] = createWellKnownSymbol('Symbol.' + name);
	    }
	  } return WellKnownSymbolsStore[name];
	};

	var wellKnownSymbol$g = wellKnownSymbol$h;

	var TO_STRING_TAG$3 = wellKnownSymbol$g('toStringTag');
	var test = {};

	test[TO_STRING_TAG$3] = 'z';

	var toStringTagSupport = String(test) === '[object z]';

	var toString$6 = {}.toString;

	var classofRaw$1 = function (it) {
	  return toString$6.call(it).slice(8, -1);
	};

	var TO_STRING_TAG_SUPPORT$2 = toStringTagSupport;
	var isCallable$g = isCallable$i;
	var classofRaw = classofRaw$1;
	var wellKnownSymbol$f = wellKnownSymbol$h;

	var TO_STRING_TAG$2 = wellKnownSymbol$f('toStringTag');
	// ES3 wrong here
	var CORRECT_ARGUMENTS = classofRaw(function () { return arguments; }()) == 'Arguments';

	// fallback for IE11 Script Access Denied error
	var tryGet = function (it, key) {
	  try {
	    return it[key];
	  } catch (error) { /* empty */ }
	};

	// getting tag from ES6+ `Object.prototype.toString`
	var classof$8 = TO_STRING_TAG_SUPPORT$2 ? classofRaw : function (it) {
	  var O, tag, result;
	  return it === undefined ? 'Undefined' : it === null ? 'Null'
	    // @@toStringTag case
	    : typeof (tag = tryGet(O = Object(it), TO_STRING_TAG$2)) == 'string' ? tag
	    // builtinTag case
	    : CORRECT_ARGUMENTS ? classofRaw(O)
	    // ES3 arguments fallback
	    : (result = classofRaw(O)) == 'Object' && isCallable$g(O.callee) ? 'Arguments' : result;
	};

	var classof$7 = classof$8;

	var toString$5 = function (argument) {
	  if (classof$7(argument) === 'Symbol') throw TypeError('Cannot convert a Symbol value to a string');
	  return String(argument);
	};

	var toInteger$3 = toInteger$4;
	var toString$4 = toString$5;
	var requireObjectCoercible$2 = requireObjectCoercible$4;

	// `String.prototype.codePointAt` methods implementation
	var createMethod$3 = function (CONVERT_TO_STRING) {
	  return function ($this, pos) {
	    var S = toString$4(requireObjectCoercible$2($this));
	    var position = toInteger$3(pos);
	    var size = S.length;
	    var first, second;
	    if (position < 0 || position >= size) return CONVERT_TO_STRING ? '' : undefined;
	    first = S.charCodeAt(position);
	    return first < 0xD800 || first > 0xDBFF || position + 1 === size
	      || (second = S.charCodeAt(position + 1)) < 0xDC00 || second > 0xDFFF
	        ? CONVERT_TO_STRING ? S.charAt(position) : first
	        : CONVERT_TO_STRING ? S.slice(position, position + 2) : (first - 0xD800 << 10) + (second - 0xDC00) + 0x10000;
	  };
	};

	var stringMultibyte = {
	  // `String.prototype.codePointAt` method
	  // https://tc39.es/ecma262/#sec-string.prototype.codepointat
	  codeAt: createMethod$3(false),
	  // `String.prototype.at` method
	  // https://github.com/mathiasbynens/String.prototype.at
	  charAt: createMethod$3(true)
	};

	var isCallable$f = isCallable$i;
	var store$3 = sharedStore;

	var functionToString = Function.toString;

	// this helper broken in `core-js@3.4.1-3.4.4`, so we can't use `shared` helper
	if (!isCallable$f(store$3.inspectSource)) {
	  store$3.inspectSource = function (it) {
	    return functionToString.call(it);
	  };
	}

	var inspectSource$4 = store$3.inspectSource;

	var global$h = global$n;
	var isCallable$e = isCallable$i;
	var inspectSource$3 = inspectSource$4;

	var WeakMap$2 = global$h.WeakMap;

	var nativeWeakMap = isCallable$e(WeakMap$2) && /native code/.test(inspectSource$3(WeakMap$2));

	var isCallable$d = isCallable$i;

	var isObject$a = function (it) {
	  return typeof it === 'object' ? it !== null : isCallable$d(it);
	};

	var fails$9 = fails$b;

	// Detect IE8's incomplete defineProperty implementation
	var descriptors = !fails$9(function () {
	  // eslint-disable-next-line es/no-object-defineproperty -- required for testing
	  return Object.defineProperty({}, 1, { get: function () { return 7; } })[1] != 7;
	});

	var objectDefineProperty = {};

	var global$g = global$n;
	var isObject$9 = isObject$a;

	var document$3 = global$g.document;
	// typeof document.createElement is 'object' in old IE
	var EXISTS$1 = isObject$9(document$3) && isObject$9(document$3.createElement);

	var documentCreateElement$2 = function (it) {
	  return EXISTS$1 ? document$3.createElement(it) : {};
	};

	var DESCRIPTORS$8 = descriptors;
	var fails$8 = fails$b;
	var createElement$1 = documentCreateElement$2;

	// Thank's IE8 for his funny defineProperty
	var ie8DomDefine = !DESCRIPTORS$8 && !fails$8(function () {
	  // eslint-disable-next-line es/no-object-defineproperty -- requied for testing
	  return Object.defineProperty(createElement$1('div'), 'a', {
	    get: function () { return 7; }
	  }).a != 7;
	});

	var isObject$8 = isObject$a;

	// `Assert: Type(argument) is Object`
	var anObject$b = function (argument) {
	  if (isObject$8(argument)) return argument;
	  throw TypeError(String(argument) + ' is not an object');
	};

	var isCallable$c = isCallable$i;
	var getBuiltIn$7 = getBuiltIn$9;
	var USE_SYMBOL_AS_UID = useSymbolAsUid;

	var isSymbol$2 = USE_SYMBOL_AS_UID ? function (it) {
	  return typeof it == 'symbol';
	} : function (it) {
	  var $Symbol = getBuiltIn$7('Symbol');
	  return isCallable$c($Symbol) && Object(it) instanceof $Symbol;
	};

	var tryToString$2 = function (argument) {
	  try {
	    return String(argument);
	  } catch (error) {
	    return 'Object';
	  }
	};

	var isCallable$b = isCallable$i;
	var tryToString$1 = tryToString$2;

	// `Assert: IsCallable(argument) is true`
	var aCallable$7 = function (argument) {
	  if (isCallable$b(argument)) return argument;
	  throw TypeError(tryToString$1(argument) + ' is not a function');
	};

	var aCallable$6 = aCallable$7;

	// `GetMethod` abstract operation
	// https://tc39.es/ecma262/#sec-getmethod
	var getMethod$3 = function (V, P) {
	  var func = V[P];
	  return func == null ? undefined : aCallable$6(func);
	};

	var isCallable$a = isCallable$i;
	var isObject$7 = isObject$a;

	// `OrdinaryToPrimitive` abstract operation
	// https://tc39.es/ecma262/#sec-ordinarytoprimitive
	var ordinaryToPrimitive$1 = function (input, pref) {
	  var fn, val;
	  if (pref === 'string' && isCallable$a(fn = input.toString) && !isObject$7(val = fn.call(input))) return val;
	  if (isCallable$a(fn = input.valueOf) && !isObject$7(val = fn.call(input))) return val;
	  if (pref !== 'string' && isCallable$a(fn = input.toString) && !isObject$7(val = fn.call(input))) return val;
	  throw TypeError("Can't convert object to primitive value");
	};

	var isObject$6 = isObject$a;
	var isSymbol$1 = isSymbol$2;
	var getMethod$2 = getMethod$3;
	var ordinaryToPrimitive = ordinaryToPrimitive$1;
	var wellKnownSymbol$e = wellKnownSymbol$h;

	var TO_PRIMITIVE = wellKnownSymbol$e('toPrimitive');

	// `ToPrimitive` abstract operation
	// https://tc39.es/ecma262/#sec-toprimitive
	var toPrimitive$1 = function (input, pref) {
	  if (!isObject$6(input) || isSymbol$1(input)) return input;
	  var exoticToPrim = getMethod$2(input, TO_PRIMITIVE);
	  var result;
	  if (exoticToPrim) {
	    if (pref === undefined) pref = 'default';
	    result = exoticToPrim.call(input, pref);
	    if (!isObject$6(result) || isSymbol$1(result)) return result;
	    throw TypeError("Can't convert object to primitive value");
	  }
	  if (pref === undefined) pref = 'number';
	  return ordinaryToPrimitive(input, pref);
	};

	var toPrimitive = toPrimitive$1;
	var isSymbol = isSymbol$2;

	// `ToPropertyKey` abstract operation
	// https://tc39.es/ecma262/#sec-topropertykey
	var toPropertyKey$3 = function (argument) {
	  var key = toPrimitive(argument, 'string');
	  return isSymbol(key) ? key : String(key);
	};

	var DESCRIPTORS$7 = descriptors;
	var IE8_DOM_DEFINE$1 = ie8DomDefine;
	var anObject$a = anObject$b;
	var toPropertyKey$2 = toPropertyKey$3;

	// eslint-disable-next-line es/no-object-defineproperty -- safe
	var $defineProperty = Object.defineProperty;

	// `Object.defineProperty` method
	// https://tc39.es/ecma262/#sec-object.defineproperty
	objectDefineProperty.f = DESCRIPTORS$7 ? $defineProperty : function defineProperty(O, P, Attributes) {
	  anObject$a(O);
	  P = toPropertyKey$2(P);
	  anObject$a(Attributes);
	  if (IE8_DOM_DEFINE$1) try {
	    return $defineProperty(O, P, Attributes);
	  } catch (error) { /* empty */ }
	  if ('get' in Attributes || 'set' in Attributes) throw TypeError('Accessors not supported');
	  if ('value' in Attributes) O[P] = Attributes.value;
	  return O;
	};

	var createPropertyDescriptor$5 = function (bitmap, value) {
	  return {
	    enumerable: !(bitmap & 1),
	    configurable: !(bitmap & 2),
	    writable: !(bitmap & 4),
	    value: value
	  };
	};

	var DESCRIPTORS$6 = descriptors;
	var definePropertyModule$5 = objectDefineProperty;
	var createPropertyDescriptor$4 = createPropertyDescriptor$5;

	var createNonEnumerableProperty$6 = DESCRIPTORS$6 ? function (object, key, value) {
	  return definePropertyModule$5.f(object, key, createPropertyDescriptor$4(1, value));
	} : function (object, key, value) {
	  object[key] = value;
	  return object;
	};

	var shared$1 = shared$3.exports;
	var uid = uid$2;

	var keys = shared$1('keys');

	var sharedKey$3 = function (key) {
	  return keys[key] || (keys[key] = uid(key));
	};

	var hiddenKeys$4 = {};

	var NATIVE_WEAK_MAP = nativeWeakMap;
	var global$f = global$n;
	var isObject$5 = isObject$a;
	var createNonEnumerableProperty$5 = createNonEnumerableProperty$6;
	var objectHas = has$9;
	var shared = sharedStore;
	var sharedKey$2 = sharedKey$3;
	var hiddenKeys$3 = hiddenKeys$4;

	var OBJECT_ALREADY_INITIALIZED = 'Object already initialized';
	var WeakMap$1 = global$f.WeakMap;
	var set$2, get$1, has$7;

	var enforce = function (it) {
	  return has$7(it) ? get$1(it) : set$2(it, {});
	};

	var getterFor = function (TYPE) {
	  return function (it) {
	    var state;
	    if (!isObject$5(it) || (state = get$1(it)).type !== TYPE) {
	      throw TypeError('Incompatible receiver, ' + TYPE + ' required');
	    } return state;
	  };
	};

	if (NATIVE_WEAK_MAP || shared.state) {
	  var store$2 = shared.state || (shared.state = new WeakMap$1());
	  var wmget = store$2.get;
	  var wmhas = store$2.has;
	  var wmset = store$2.set;
	  set$2 = function (it, metadata) {
	    if (wmhas.call(store$2, it)) throw new TypeError(OBJECT_ALREADY_INITIALIZED);
	    metadata.facade = it;
	    wmset.call(store$2, it, metadata);
	    return metadata;
	  };
	  get$1 = function (it) {
	    return wmget.call(store$2, it) || {};
	  };
	  has$7 = function (it) {
	    return wmhas.call(store$2, it);
	  };
	} else {
	  var STATE = sharedKey$2('state');
	  hiddenKeys$3[STATE] = true;
	  set$2 = function (it, metadata) {
	    if (objectHas(it, STATE)) throw new TypeError(OBJECT_ALREADY_INITIALIZED);
	    metadata.facade = it;
	    createNonEnumerableProperty$5(it, STATE, metadata);
	    return metadata;
	  };
	  get$1 = function (it) {
	    return objectHas(it, STATE) ? it[STATE] : {};
	  };
	  has$7 = function (it) {
	    return objectHas(it, STATE);
	  };
	}

	var internalState = {
	  set: set$2,
	  get: get$1,
	  has: has$7,
	  enforce: enforce,
	  getterFor: getterFor
	};

	var objectGetOwnPropertyDescriptor = {};

	var objectPropertyIsEnumerable = {};

	var $propertyIsEnumerable = {}.propertyIsEnumerable;
	// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
	var getOwnPropertyDescriptor$3 = Object.getOwnPropertyDescriptor;

	// Nashorn ~ JDK8 bug
	var NASHORN_BUG = getOwnPropertyDescriptor$3 && !$propertyIsEnumerable.call({ 1: 2 }, 1);

	// `Object.prototype.propertyIsEnumerable` method implementation
	// https://tc39.es/ecma262/#sec-object.prototype.propertyisenumerable
	objectPropertyIsEnumerable.f = NASHORN_BUG ? function propertyIsEnumerable(V) {
	  var descriptor = getOwnPropertyDescriptor$3(this, V);
	  return !!descriptor && descriptor.enumerable;
	} : $propertyIsEnumerable;

	var fails$7 = fails$b;
	var classof$6 = classofRaw$1;

	var split$1 = ''.split;

	// fallback for non-array-like ES3 and non-enumerable old V8 strings
	var indexedObject = fails$7(function () {
	  // throws an error in rhino, see https://github.com/mozilla/rhino/issues/346
	  // eslint-disable-next-line no-prototype-builtins -- safe
	  return !Object('z').propertyIsEnumerable(0);
	}) ? function (it) {
	  return classof$6(it) == 'String' ? split$1.call(it, '') : Object(it);
	} : Object;

	// toObject with fallback for non-array-like ES3 strings
	var IndexedObject$2 = indexedObject;
	var requireObjectCoercible$1 = requireObjectCoercible$4;

	var toIndexedObject$5 = function (it) {
	  return IndexedObject$2(requireObjectCoercible$1(it));
	};

	var DESCRIPTORS$5 = descriptors;
	var propertyIsEnumerableModule$1 = objectPropertyIsEnumerable;
	var createPropertyDescriptor$3 = createPropertyDescriptor$5;
	var toIndexedObject$4 = toIndexedObject$5;
	var toPropertyKey$1 = toPropertyKey$3;
	var has$6 = has$9;
	var IE8_DOM_DEFINE = ie8DomDefine;

	// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
	var $getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;

	// `Object.getOwnPropertyDescriptor` method
	// https://tc39.es/ecma262/#sec-object.getownpropertydescriptor
	objectGetOwnPropertyDescriptor.f = DESCRIPTORS$5 ? $getOwnPropertyDescriptor : function getOwnPropertyDescriptor(O, P) {
	  O = toIndexedObject$4(O);
	  P = toPropertyKey$1(P);
	  if (IE8_DOM_DEFINE) try {
	    return $getOwnPropertyDescriptor(O, P);
	  } catch (error) { /* empty */ }
	  if (has$6(O, P)) return createPropertyDescriptor$3(!propertyIsEnumerableModule$1.f.call(O, P), O[P]);
	};

	var redefine$7 = {exports: {}};

	var DESCRIPTORS$4 = descriptors;
	var has$5 = has$9;

	var FunctionPrototype = Function.prototype;
	// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
	var getDescriptor = DESCRIPTORS$4 && Object.getOwnPropertyDescriptor;

	var EXISTS = has$5(FunctionPrototype, 'name');
	// additional protection from minified / mangled / dropped function names
	var PROPER = EXISTS && (function something() { /* empty */ }).name === 'something';
	var CONFIGURABLE = EXISTS && (!DESCRIPTORS$4 || (DESCRIPTORS$4 && getDescriptor(FunctionPrototype, 'name').configurable));

	var functionName = {
	  EXISTS: EXISTS,
	  PROPER: PROPER,
	  CONFIGURABLE: CONFIGURABLE
	};

	var global$e = global$n;
	var isCallable$9 = isCallable$i;
	var has$4 = has$9;
	var createNonEnumerableProperty$4 = createNonEnumerableProperty$6;
	var setGlobal$1 = setGlobal$3;
	var inspectSource$2 = inspectSource$4;
	var InternalStateModule$3 = internalState;
	var CONFIGURABLE_FUNCTION_NAME$1 = functionName.CONFIGURABLE;

	var getInternalState$3 = InternalStateModule$3.get;
	var enforceInternalState = InternalStateModule$3.enforce;
	var TEMPLATE = String(String).split('String');

	(redefine$7.exports = function (O, key, value, options) {
	  var unsafe = options ? !!options.unsafe : false;
	  var simple = options ? !!options.enumerable : false;
	  var noTargetGet = options ? !!options.noTargetGet : false;
	  var name = options && options.name !== undefined ? options.name : key;
	  var state;
	  if (isCallable$9(value)) {
	    if (String(name).slice(0, 7) === 'Symbol(') {
	      name = '[' + String(name).replace(/^Symbol\(([^)]*)\)/, '$1') + ']';
	    }
	    if (!has$4(value, 'name') || (CONFIGURABLE_FUNCTION_NAME$1 && value.name !== name)) {
	      createNonEnumerableProperty$4(value, 'name', name);
	    }
	    state = enforceInternalState(value);
	    if (!state.source) {
	      state.source = TEMPLATE.join(typeof name == 'string' ? name : '');
	    }
	  }
	  if (O === global$e) {
	    if (simple) O[key] = value;
	    else setGlobal$1(key, value);
	    return;
	  } else if (!unsafe) {
	    delete O[key];
	  } else if (!noTargetGet && O[key]) {
	    simple = true;
	  }
	  if (simple) O[key] = value;
	  else createNonEnumerableProperty$4(O, key, value);
	// add fake Function#toString for correct work wrapped methods / constructors with methods like LoDash isNative
	})(Function.prototype, 'toString', function toString() {
	  return isCallable$9(this) && getInternalState$3(this).source || inspectSource$2(this);
	});

	var objectGetOwnPropertyNames = {};

	var toInteger$2 = toInteger$4;

	var min$2 = Math.min;

	// `ToLength` abstract operation
	// https://tc39.es/ecma262/#sec-tolength
	var toLength$7 = function (argument) {
	  return argument > 0 ? min$2(toInteger$2(argument), 0x1FFFFFFFFFFFFF) : 0; // 2 ** 53 - 1 == 9007199254740991
	};

	var toInteger$1 = toInteger$4;

	var max = Math.max;
	var min$1 = Math.min;

	// Helper for a popular repeating case of the spec:
	// Let integer be ? ToInteger(index).
	// If integer < 0, let result be max((length + integer), 0); else let result be min(integer, length).
	var toAbsoluteIndex$1 = function (index, length) {
	  var integer = toInteger$1(index);
	  return integer < 0 ? max(integer + length, 0) : min$1(integer, length);
	};

	var toIndexedObject$3 = toIndexedObject$5;
	var toLength$6 = toLength$7;
	var toAbsoluteIndex = toAbsoluteIndex$1;

	// `Array.prototype.{ indexOf, includes }` methods implementation
	var createMethod$2 = function (IS_INCLUDES) {
	  return function ($this, el, fromIndex) {
	    var O = toIndexedObject$3($this);
	    var length = toLength$6(O.length);
	    var index = toAbsoluteIndex(fromIndex, length);
	    var value;
	    // Array#includes uses SameValueZero equality algorithm
	    // eslint-disable-next-line no-self-compare -- NaN check
	    if (IS_INCLUDES && el != el) while (length > index) {
	      value = O[index++];
	      // eslint-disable-next-line no-self-compare -- NaN check
	      if (value != value) return true;
	    // Array#indexOf ignores holes, Array#includes - not
	    } else for (;length > index; index++) {
	      if ((IS_INCLUDES || index in O) && O[index] === el) return IS_INCLUDES || index || 0;
	    } return !IS_INCLUDES && -1;
	  };
	};

	var arrayIncludes = {
	  // `Array.prototype.includes` method
	  // https://tc39.es/ecma262/#sec-array.prototype.includes
	  includes: createMethod$2(true),
	  // `Array.prototype.indexOf` method
	  // https://tc39.es/ecma262/#sec-array.prototype.indexof
	  indexOf: createMethod$2(false)
	};

	var has$3 = has$9;
	var toIndexedObject$2 = toIndexedObject$5;
	var indexOf = arrayIncludes.indexOf;
	var hiddenKeys$2 = hiddenKeys$4;

	var objectKeysInternal = function (object, names) {
	  var O = toIndexedObject$2(object);
	  var i = 0;
	  var result = [];
	  var key;
	  for (key in O) !has$3(hiddenKeys$2, key) && has$3(O, key) && result.push(key);
	  // Don't enum bug & hidden keys
	  while (names.length > i) if (has$3(O, key = names[i++])) {
	    ~indexOf(result, key) || result.push(key);
	  }
	  return result;
	};

	// IE8- don't enum bug keys
	var enumBugKeys$3 = [
	  'constructor',
	  'hasOwnProperty',
	  'isPrototypeOf',
	  'propertyIsEnumerable',
	  'toLocaleString',
	  'toString',
	  'valueOf'
	];

	var internalObjectKeys$1 = objectKeysInternal;
	var enumBugKeys$2 = enumBugKeys$3;

	var hiddenKeys$1 = enumBugKeys$2.concat('length', 'prototype');

	// `Object.getOwnPropertyNames` method
	// https://tc39.es/ecma262/#sec-object.getownpropertynames
	// eslint-disable-next-line es/no-object-getownpropertynames -- safe
	objectGetOwnPropertyNames.f = Object.getOwnPropertyNames || function getOwnPropertyNames(O) {
	  return internalObjectKeys$1(O, hiddenKeys$1);
	};

	var objectGetOwnPropertySymbols = {};

	// eslint-disable-next-line es/no-object-getownpropertysymbols -- safe
	objectGetOwnPropertySymbols.f = Object.getOwnPropertySymbols;

	var getBuiltIn$6 = getBuiltIn$9;
	var getOwnPropertyNamesModule = objectGetOwnPropertyNames;
	var getOwnPropertySymbolsModule$1 = objectGetOwnPropertySymbols;
	var anObject$9 = anObject$b;

	// all object keys, includes non-enumerable and symbols
	var ownKeys$1 = getBuiltIn$6('Reflect', 'ownKeys') || function ownKeys(it) {
	  var keys = getOwnPropertyNamesModule.f(anObject$9(it));
	  var getOwnPropertySymbols = getOwnPropertySymbolsModule$1.f;
	  return getOwnPropertySymbols ? keys.concat(getOwnPropertySymbols(it)) : keys;
	};

	var has$2 = has$9;
	var ownKeys = ownKeys$1;
	var getOwnPropertyDescriptorModule = objectGetOwnPropertyDescriptor;
	var definePropertyModule$4 = objectDefineProperty;

	var copyConstructorProperties$1 = function (target, source) {
	  var keys = ownKeys(source);
	  var defineProperty = definePropertyModule$4.f;
	  var getOwnPropertyDescriptor = getOwnPropertyDescriptorModule.f;
	  for (var i = 0; i < keys.length; i++) {
	    var key = keys[i];
	    if (!has$2(target, key)) defineProperty(target, key, getOwnPropertyDescriptor(source, key));
	  }
	};

	var fails$6 = fails$b;
	var isCallable$8 = isCallable$i;

	var replacement = /#|\.prototype\./;

	var isForced$2 = function (feature, detection) {
	  var value = data$1[normalize(feature)];
	  return value == POLYFILL ? true
	    : value == NATIVE ? false
	    : isCallable$8(detection) ? fails$6(detection)
	    : !!detection;
	};

	var normalize = isForced$2.normalize = function (string) {
	  return String(string).replace(replacement, '.').toLowerCase();
	};

	var data$1 = isForced$2.data = {};
	var NATIVE = isForced$2.NATIVE = 'N';
	var POLYFILL = isForced$2.POLYFILL = 'P';

	var isForced_1 = isForced$2;

	var global$d = global$n;
	var getOwnPropertyDescriptor$2 = objectGetOwnPropertyDescriptor.f;
	var createNonEnumerableProperty$3 = createNonEnumerableProperty$6;
	var redefine$6 = redefine$7.exports;
	var setGlobal = setGlobal$3;
	var copyConstructorProperties = copyConstructorProperties$1;
	var isForced$1 = isForced_1;

	/*
	  options.target      - name of the target object
	  options.global      - target is the global object
	  options.stat        - export as static methods of target
	  options.proto       - export as prototype methods of target
	  options.real        - real prototype method for the `pure` version
	  options.forced      - export even if the native feature is available
	  options.bind        - bind methods to the target, required for the `pure` version
	  options.wrap        - wrap constructors to preventing global pollution, required for the `pure` version
	  options.unsafe      - use the simple assignment of property instead of delete + defineProperty
	  options.sham        - add a flag to not completely full polyfills
	  options.enumerable  - export as enumerable property
	  options.noTargetGet - prevent calling a getter on target
	  options.name        - the .name of the function if it does not match the key
	*/
	var _export = function (options, source) {
	  var TARGET = options.target;
	  var GLOBAL = options.global;
	  var STATIC = options.stat;
	  var FORCED, target, key, targetProperty, sourceProperty, descriptor;
	  if (GLOBAL) {
	    target = global$d;
	  } else if (STATIC) {
	    target = global$d[TARGET] || setGlobal(TARGET, {});
	  } else {
	    target = (global$d[TARGET] || {}).prototype;
	  }
	  if (target) for (key in source) {
	    sourceProperty = source[key];
	    if (options.noTargetGet) {
	      descriptor = getOwnPropertyDescriptor$2(target, key);
	      targetProperty = descriptor && descriptor.value;
	    } else targetProperty = target[key];
	    FORCED = isForced$1(GLOBAL ? key : TARGET + (STATIC ? '.' : '#') + key, options.forced);
	    // contained in target
	    if (!FORCED && targetProperty !== undefined) {
	      if (typeof sourceProperty === typeof targetProperty) continue;
	      copyConstructorProperties(sourceProperty, targetProperty);
	    }
	    // add a flag to not completely full polyfills
	    if (options.sham || (targetProperty && targetProperty.sham)) {
	      createNonEnumerableProperty$3(sourceProperty, 'sham', true);
	    }
	    // extend global
	    redefine$6(target, key, sourceProperty, options);
	  }
	};

	var internalObjectKeys = objectKeysInternal;
	var enumBugKeys$1 = enumBugKeys$3;

	// `Object.keys` method
	// https://tc39.es/ecma262/#sec-object.keys
	// eslint-disable-next-line es/no-object-keys -- safe
	var objectKeys$3 = Object.keys || function keys(O) {
	  return internalObjectKeys(O, enumBugKeys$1);
	};

	var DESCRIPTORS$3 = descriptors;
	var definePropertyModule$3 = objectDefineProperty;
	var anObject$8 = anObject$b;
	var objectKeys$2 = objectKeys$3;

	// `Object.defineProperties` method
	// https://tc39.es/ecma262/#sec-object.defineproperties
	// eslint-disable-next-line es/no-object-defineproperties -- safe
	var objectDefineProperties = DESCRIPTORS$3 ? Object.defineProperties : function defineProperties(O, Properties) {
	  anObject$8(O);
	  var keys = objectKeys$2(Properties);
	  var length = keys.length;
	  var index = 0;
	  var key;
	  while (length > index) definePropertyModule$3.f(O, key = keys[index++], Properties[key]);
	  return O;
	};

	var getBuiltIn$5 = getBuiltIn$9;

	var html$2 = getBuiltIn$5('document', 'documentElement');

	/* global ActiveXObject -- old IE, WSH */

	var anObject$7 = anObject$b;
	var defineProperties = objectDefineProperties;
	var enumBugKeys = enumBugKeys$3;
	var hiddenKeys = hiddenKeys$4;
	var html$1 = html$2;
	var documentCreateElement$1 = documentCreateElement$2;
	var sharedKey$1 = sharedKey$3;

	var GT = '>';
	var LT = '<';
	var PROTOTYPE = 'prototype';
	var SCRIPT = 'script';
	var IE_PROTO$1 = sharedKey$1('IE_PROTO');

	var EmptyConstructor = function () { /* empty */ };

	var scriptTag = function (content) {
	  return LT + SCRIPT + GT + content + LT + '/' + SCRIPT + GT;
	};

	// Create object with fake `null` prototype: use ActiveX Object with cleared prototype
	var NullProtoObjectViaActiveX = function (activeXDocument) {
	  activeXDocument.write(scriptTag(''));
	  activeXDocument.close();
	  var temp = activeXDocument.parentWindow.Object;
	  activeXDocument = null; // avoid memory leak
	  return temp;
	};

	// Create object with fake `null` prototype: use iframe Object with cleared prototype
	var NullProtoObjectViaIFrame = function () {
	  // Thrash, waste and sodomy: IE GC bug
	  var iframe = documentCreateElement$1('iframe');
	  var JS = 'java' + SCRIPT + ':';
	  var iframeDocument;
	  iframe.style.display = 'none';
	  html$1.appendChild(iframe);
	  // https://github.com/zloirock/core-js/issues/475
	  iframe.src = String(JS);
	  iframeDocument = iframe.contentWindow.document;
	  iframeDocument.open();
	  iframeDocument.write(scriptTag('document.F=Object'));
	  iframeDocument.close();
	  return iframeDocument.F;
	};

	// Check for document.domain and active x support
	// No need to use active x approach when document.domain is not set
	// see https://github.com/es-shims/es5-shim/issues/150
	// variation of https://github.com/kitcambridge/es5-shim/commit/4f738ac066346
	// avoid IE GC bug
	var activeXDocument;
	var NullProtoObject = function () {
	  try {
	    activeXDocument = new ActiveXObject('htmlfile');
	  } catch (error) { /* ignore */ }
	  NullProtoObject = typeof document != 'undefined'
	    ? document.domain && activeXDocument
	      ? NullProtoObjectViaActiveX(activeXDocument) // old IE
	      : NullProtoObjectViaIFrame()
	    : NullProtoObjectViaActiveX(activeXDocument); // WSH
	  var length = enumBugKeys.length;
	  while (length--) delete NullProtoObject[PROTOTYPE][enumBugKeys[length]];
	  return NullProtoObject();
	};

	hiddenKeys[IE_PROTO$1] = true;

	// `Object.create` method
	// https://tc39.es/ecma262/#sec-object.create
	var objectCreate = Object.create || function create(O, Properties) {
	  var result;
	  if (O !== null) {
	    EmptyConstructor[PROTOTYPE] = anObject$7(O);
	    result = new EmptyConstructor();
	    EmptyConstructor[PROTOTYPE] = null;
	    // add "__proto__" for Object.getPrototypeOf polyfill
	    result[IE_PROTO$1] = O;
	  } else result = NullProtoObject();
	  return Properties === undefined ? result : defineProperties(result, Properties);
	};

	var fails$5 = fails$b;

	var correctPrototypeGetter = !fails$5(function () {
	  function F() { /* empty */ }
	  F.prototype.constructor = null;
	  // eslint-disable-next-line es/no-object-getprototypeof -- required for testing
	  return Object.getPrototypeOf(new F()) !== F.prototype;
	});

	var has$1 = has$9;
	var isCallable$7 = isCallable$i;
	var toObject$4 = toObject$6;
	var sharedKey = sharedKey$3;
	var CORRECT_PROTOTYPE_GETTER = correctPrototypeGetter;

	var IE_PROTO = sharedKey('IE_PROTO');
	var ObjectPrototype = Object.prototype;

	// `Object.getPrototypeOf` method
	// https://tc39.es/ecma262/#sec-object.getprototypeof
	// eslint-disable-next-line es/no-object-getprototypeof -- safe
	var objectGetPrototypeOf = CORRECT_PROTOTYPE_GETTER ? Object.getPrototypeOf : function (O) {
	  var object = toObject$4(O);
	  if (has$1(object, IE_PROTO)) return object[IE_PROTO];
	  var constructor = object.constructor;
	  if (isCallable$7(constructor) && object instanceof constructor) {
	    return constructor.prototype;
	  } return object instanceof Object ? ObjectPrototype : null;
	};

	var fails$4 = fails$b;
	var isCallable$6 = isCallable$i;
	var getPrototypeOf$2 = objectGetPrototypeOf;
	var redefine$5 = redefine$7.exports;
	var wellKnownSymbol$d = wellKnownSymbol$h;

	var ITERATOR$5 = wellKnownSymbol$d('iterator');
	var BUGGY_SAFARI_ITERATORS$1 = false;

	// `%IteratorPrototype%` object
	// https://tc39.es/ecma262/#sec-%iteratorprototype%-object
	var IteratorPrototype$2, PrototypeOfArrayIteratorPrototype, arrayIterator;

	/* eslint-disable es/no-array-prototype-keys -- safe */
	if ([].keys) {
	  arrayIterator = [].keys();
	  // Safari 8 has buggy iterators w/o `next`
	  if (!('next' in arrayIterator)) BUGGY_SAFARI_ITERATORS$1 = true;
	  else {
	    PrototypeOfArrayIteratorPrototype = getPrototypeOf$2(getPrototypeOf$2(arrayIterator));
	    if (PrototypeOfArrayIteratorPrototype !== Object.prototype) IteratorPrototype$2 = PrototypeOfArrayIteratorPrototype;
	  }
	}

	var NEW_ITERATOR_PROTOTYPE = IteratorPrototype$2 == undefined || fails$4(function () {
	  var test = {};
	  // FF44- legacy iterators case
	  return IteratorPrototype$2[ITERATOR$5].call(test) !== test;
	});

	if (NEW_ITERATOR_PROTOTYPE) IteratorPrototype$2 = {};

	// `%IteratorPrototype%[@@iterator]()` method
	// https://tc39.es/ecma262/#sec-%iteratorprototype%-@@iterator
	if (!isCallable$6(IteratorPrototype$2[ITERATOR$5])) {
	  redefine$5(IteratorPrototype$2, ITERATOR$5, function () {
	    return this;
	  });
	}

	var iteratorsCore = {
	  IteratorPrototype: IteratorPrototype$2,
	  BUGGY_SAFARI_ITERATORS: BUGGY_SAFARI_ITERATORS$1
	};

	var defineProperty$1 = objectDefineProperty.f;
	var has = has$9;
	var wellKnownSymbol$c = wellKnownSymbol$h;

	var TO_STRING_TAG$1 = wellKnownSymbol$c('toStringTag');

	var setToStringTag$3 = function (it, TAG, STATIC) {
	  if (it && !has(it = STATIC ? it : it.prototype, TO_STRING_TAG$1)) {
	    defineProperty$1(it, TO_STRING_TAG$1, { configurable: true, value: TAG });
	  }
	};

	var iterators = {};

	var IteratorPrototype$1 = iteratorsCore.IteratorPrototype;
	var create$2 = objectCreate;
	var createPropertyDescriptor$2 = createPropertyDescriptor$5;
	var setToStringTag$2 = setToStringTag$3;
	var Iterators$4 = iterators;

	var returnThis$1 = function () { return this; };

	var createIteratorConstructor$1 = function (IteratorConstructor, NAME, next) {
	  var TO_STRING_TAG = NAME + ' Iterator';
	  IteratorConstructor.prototype = create$2(IteratorPrototype$1, { next: createPropertyDescriptor$2(1, next) });
	  setToStringTag$2(IteratorConstructor, TO_STRING_TAG, false);
	  Iterators$4[TO_STRING_TAG] = returnThis$1;
	  return IteratorConstructor;
	};

	var isCallable$5 = isCallable$i;

	var aPossiblePrototype$1 = function (argument) {
	  if (typeof argument === 'object' || isCallable$5(argument)) return argument;
	  throw TypeError("Can't set " + String(argument) + ' as a prototype');
	};

	/* eslint-disable no-proto -- safe */

	var anObject$6 = anObject$b;
	var aPossiblePrototype = aPossiblePrototype$1;

	// `Object.setPrototypeOf` method
	// https://tc39.es/ecma262/#sec-object.setprototypeof
	// Works with __proto__ only. Old v8 can't work with null proto objects.
	// eslint-disable-next-line es/no-object-setprototypeof -- safe
	var objectSetPrototypeOf = Object.setPrototypeOf || ('__proto__' in {} ? function () {
	  var CORRECT_SETTER = false;
	  var test = {};
	  var setter;
	  try {
	    // eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
	    setter = Object.getOwnPropertyDescriptor(Object.prototype, '__proto__').set;
	    setter.call(test, []);
	    CORRECT_SETTER = test instanceof Array;
	  } catch (error) { /* empty */ }
	  return function setPrototypeOf(O, proto) {
	    anObject$6(O);
	    aPossiblePrototype(proto);
	    if (CORRECT_SETTER) setter.call(O, proto);
	    else O.__proto__ = proto;
	    return O;
	  };
	}() : undefined);

	var $$f = _export;
	var FunctionName = functionName;
	var isCallable$4 = isCallable$i;
	var createIteratorConstructor = createIteratorConstructor$1;
	var getPrototypeOf$1 = objectGetPrototypeOf;
	var setPrototypeOf$2 = objectSetPrototypeOf;
	var setToStringTag$1 = setToStringTag$3;
	var createNonEnumerableProperty$2 = createNonEnumerableProperty$6;
	var redefine$4 = redefine$7.exports;
	var wellKnownSymbol$b = wellKnownSymbol$h;
	var Iterators$3 = iterators;
	var IteratorsCore = iteratorsCore;

	var PROPER_FUNCTION_NAME = FunctionName.PROPER;
	var CONFIGURABLE_FUNCTION_NAME = FunctionName.CONFIGURABLE;
	var IteratorPrototype = IteratorsCore.IteratorPrototype;
	var BUGGY_SAFARI_ITERATORS = IteratorsCore.BUGGY_SAFARI_ITERATORS;
	var ITERATOR$4 = wellKnownSymbol$b('iterator');
	var KEYS = 'keys';
	var VALUES = 'values';
	var ENTRIES = 'entries';

	var returnThis = function () { return this; };

	var defineIterator$2 = function (Iterable, NAME, IteratorConstructor, next, DEFAULT, IS_SET, FORCED) {
	  createIteratorConstructor(IteratorConstructor, NAME, next);

	  var getIterationMethod = function (KIND) {
	    if (KIND === DEFAULT && defaultIterator) return defaultIterator;
	    if (!BUGGY_SAFARI_ITERATORS && KIND in IterablePrototype) return IterablePrototype[KIND];
	    switch (KIND) {
	      case KEYS: return function keys() { return new IteratorConstructor(this, KIND); };
	      case VALUES: return function values() { return new IteratorConstructor(this, KIND); };
	      case ENTRIES: return function entries() { return new IteratorConstructor(this, KIND); };
	    } return function () { return new IteratorConstructor(this); };
	  };

	  var TO_STRING_TAG = NAME + ' Iterator';
	  var INCORRECT_VALUES_NAME = false;
	  var IterablePrototype = Iterable.prototype;
	  var nativeIterator = IterablePrototype[ITERATOR$4]
	    || IterablePrototype['@@iterator']
	    || DEFAULT && IterablePrototype[DEFAULT];
	  var defaultIterator = !BUGGY_SAFARI_ITERATORS && nativeIterator || getIterationMethod(DEFAULT);
	  var anyNativeIterator = NAME == 'Array' ? IterablePrototype.entries || nativeIterator : nativeIterator;
	  var CurrentIteratorPrototype, methods, KEY;

	  // fix native
	  if (anyNativeIterator) {
	    CurrentIteratorPrototype = getPrototypeOf$1(anyNativeIterator.call(new Iterable()));
	    if (CurrentIteratorPrototype !== Object.prototype && CurrentIteratorPrototype.next) {
	      if (getPrototypeOf$1(CurrentIteratorPrototype) !== IteratorPrototype) {
	        if (setPrototypeOf$2) {
	          setPrototypeOf$2(CurrentIteratorPrototype, IteratorPrototype);
	        } else if (!isCallable$4(CurrentIteratorPrototype[ITERATOR$4])) {
	          redefine$4(CurrentIteratorPrototype, ITERATOR$4, returnThis);
	        }
	      }
	      // Set @@toStringTag to native iterators
	      setToStringTag$1(CurrentIteratorPrototype, TO_STRING_TAG, true);
	    }
	  }

	  // fix Array.prototype.{ values, @@iterator }.name in V8 / FF
	  if (PROPER_FUNCTION_NAME && DEFAULT == VALUES && nativeIterator && nativeIterator.name !== VALUES) {
	    if (CONFIGURABLE_FUNCTION_NAME) {
	      createNonEnumerableProperty$2(IterablePrototype, 'name', VALUES);
	    } else {
	      INCORRECT_VALUES_NAME = true;
	      defaultIterator = function values() { return nativeIterator.call(this); };
	    }
	  }

	  // export additional methods
	  if (DEFAULT) {
	    methods = {
	      values: getIterationMethod(VALUES),
	      keys: IS_SET ? defaultIterator : getIterationMethod(KEYS),
	      entries: getIterationMethod(ENTRIES)
	    };
	    if (FORCED) for (KEY in methods) {
	      if (BUGGY_SAFARI_ITERATORS || INCORRECT_VALUES_NAME || !(KEY in IterablePrototype)) {
	        redefine$4(IterablePrototype, KEY, methods[KEY]);
	      }
	    } else $$f({ target: NAME, proto: true, forced: BUGGY_SAFARI_ITERATORS || INCORRECT_VALUES_NAME }, methods);
	  }

	  // define iterator
	  if (IterablePrototype[ITERATOR$4] !== defaultIterator) {
	    redefine$4(IterablePrototype, ITERATOR$4, defaultIterator, { name: DEFAULT });
	  }
	  Iterators$3[NAME] = defaultIterator;

	  return methods;
	};

	var charAt = stringMultibyte.charAt;
	var toString$3 = toString$5;
	var InternalStateModule$2 = internalState;
	var defineIterator$1 = defineIterator$2;

	var STRING_ITERATOR = 'String Iterator';
	var setInternalState$2 = InternalStateModule$2.set;
	var getInternalState$2 = InternalStateModule$2.getterFor(STRING_ITERATOR);

	// `String.prototype[@@iterator]` method
	// https://tc39.es/ecma262/#sec-string.prototype-@@iterator
	defineIterator$1(String, 'String', function (iterated) {
	  setInternalState$2(this, {
	    type: STRING_ITERATOR,
	    string: toString$3(iterated),
	    index: 0
	  });
	// `%StringIteratorPrototype%.next` method
	// https://tc39.es/ecma262/#sec-%stringiteratorprototype%.next
	}, function next() {
	  var state = getInternalState$2(this);
	  var string = state.string;
	  var index = state.index;
	  var point;
	  if (index >= string.length) return { value: undefined, done: true };
	  point = charAt(string, index);
	  state.index += point.length;
	  return { value: point, done: false };
	});

	var aCallable$5 = aCallable$7;

	// optional / simple context binding
	var functionBindContext = function (fn, that, length) {
	  aCallable$5(fn);
	  if (that === undefined) return fn;
	  switch (length) {
	    case 0: return function () {
	      return fn.call(that);
	    };
	    case 1: return function (a) {
	      return fn.call(that, a);
	    };
	    case 2: return function (a, b) {
	      return fn.call(that, a, b);
	    };
	    case 3: return function (a, b, c) {
	      return fn.call(that, a, b, c);
	    };
	  }
	  return function (/* ...args */) {
	    return fn.apply(that, arguments);
	  };
	};

	var anObject$5 = anObject$b;
	var getMethod$1 = getMethod$3;

	var iteratorClose$2 = function (iterator, kind, value) {
	  var innerResult, innerError;
	  anObject$5(iterator);
	  try {
	    innerResult = getMethod$1(iterator, 'return');
	    if (!innerResult) {
	      if (kind === 'throw') throw value;
	      return value;
	    }
	    innerResult = innerResult.call(iterator);
	  } catch (error) {
	    innerError = true;
	    innerResult = error;
	  }
	  if (kind === 'throw') throw value;
	  if (innerError) throw innerResult;
	  anObject$5(innerResult);
	  return value;
	};

	var anObject$4 = anObject$b;
	var iteratorClose$1 = iteratorClose$2;

	// call something on iterator step with safe closing on error
	var callWithSafeIterationClosing$1 = function (iterator, fn, value, ENTRIES) {
	  try {
	    return ENTRIES ? fn(anObject$4(value)[0], value[1]) : fn(value);
	  } catch (error) {
	    iteratorClose$1(iterator, 'throw', error);
	  }
	};

	var wellKnownSymbol$a = wellKnownSymbol$h;
	var Iterators$2 = iterators;

	var ITERATOR$3 = wellKnownSymbol$a('iterator');
	var ArrayPrototype$1 = Array.prototype;

	// check on default Array iterator
	var isArrayIteratorMethod$2 = function (it) {
	  return it !== undefined && (Iterators$2.Array === it || ArrayPrototype$1[ITERATOR$3] === it);
	};

	var fails$3 = fails$b;
	var isCallable$3 = isCallable$i;
	var classof$5 = classof$8;
	var getBuiltIn$4 = getBuiltIn$9;
	var inspectSource$1 = inspectSource$4;

	var empty = [];
	var construct = getBuiltIn$4('Reflect', 'construct');
	var constructorRegExp = /^\s*(?:class|function)\b/;
	var exec = constructorRegExp.exec;
	var INCORRECT_TO_STRING = !constructorRegExp.exec(function () { /* empty */ });

	var isConstructorModern = function (argument) {
	  if (!isCallable$3(argument)) return false;
	  try {
	    construct(Object, empty, argument);
	    return true;
	  } catch (error) {
	    return false;
	  }
	};

	var isConstructorLegacy = function (argument) {
	  if (!isCallable$3(argument)) return false;
	  switch (classof$5(argument)) {
	    case 'AsyncFunction':
	    case 'GeneratorFunction':
	    case 'AsyncGeneratorFunction': return false;
	    // we can't check .prototype since constructors produced by .bind haven't it
	  } return INCORRECT_TO_STRING || !!exec.call(constructorRegExp, inspectSource$1(argument));
	};

	// `IsConstructor` abstract operation
	// https://tc39.es/ecma262/#sec-isconstructor
	var isConstructor$3 = !construct || fails$3(function () {
	  var called;
	  return isConstructorModern(isConstructorModern.call)
	    || !isConstructorModern(Object)
	    || !isConstructorModern(function () { called = true; })
	    || called;
	}) ? isConstructorLegacy : isConstructorModern;

	var toPropertyKey = toPropertyKey$3;
	var definePropertyModule$2 = objectDefineProperty;
	var createPropertyDescriptor$1 = createPropertyDescriptor$5;

	var createProperty$1 = function (object, key, value) {
	  var propertyKey = toPropertyKey(key);
	  if (propertyKey in object) definePropertyModule$2.f(object, propertyKey, createPropertyDescriptor$1(0, value));
	  else object[propertyKey] = value;
	};

	var classof$4 = classof$8;
	var getMethod = getMethod$3;
	var Iterators$1 = iterators;
	var wellKnownSymbol$9 = wellKnownSymbol$h;

	var ITERATOR$2 = wellKnownSymbol$9('iterator');

	var getIteratorMethod$3 = function (it) {
	  if (it != undefined) return getMethod(it, ITERATOR$2)
	    || getMethod(it, '@@iterator')
	    || Iterators$1[classof$4(it)];
	};

	var aCallable$4 = aCallable$7;
	var anObject$3 = anObject$b;
	var getIteratorMethod$2 = getIteratorMethod$3;

	var getIterator$2 = function (argument, usingIterator) {
	  var iteratorMethod = arguments.length < 2 ? getIteratorMethod$2(argument) : usingIterator;
	  if (aCallable$4(iteratorMethod)) return anObject$3(iteratorMethod.call(argument));
	  throw TypeError(String(argument) + ' is not iterable');
	};

	var bind$7 = functionBindContext;
	var toObject$3 = toObject$6;
	var callWithSafeIterationClosing = callWithSafeIterationClosing$1;
	var isArrayIteratorMethod$1 = isArrayIteratorMethod$2;
	var isConstructor$2 = isConstructor$3;
	var toLength$5 = toLength$7;
	var createProperty = createProperty$1;
	var getIterator$1 = getIterator$2;
	var getIteratorMethod$1 = getIteratorMethod$3;

	// `Array.from` method implementation
	// https://tc39.es/ecma262/#sec-array.from
	var arrayFrom = function from(arrayLike /* , mapfn = undefined, thisArg = undefined */) {
	  var O = toObject$3(arrayLike);
	  var IS_CONSTRUCTOR = isConstructor$2(this);
	  var argumentsLength = arguments.length;
	  var mapfn = argumentsLength > 1 ? arguments[1] : undefined;
	  var mapping = mapfn !== undefined;
	  if (mapping) mapfn = bind$7(mapfn, argumentsLength > 2 ? arguments[2] : undefined, 2);
	  var iteratorMethod = getIteratorMethod$1(O);
	  var index = 0;
	  var length, result, step, iterator, next, value;
	  // if the target is not iterable or it's an array with the default iterator - use a simple case
	  if (iteratorMethod && !(this == Array && isArrayIteratorMethod$1(iteratorMethod))) {
	    iterator = getIterator$1(O, iteratorMethod);
	    next = iterator.next;
	    result = IS_CONSTRUCTOR ? new this() : [];
	    for (;!(step = next.call(iterator)).done; index++) {
	      value = mapping ? callWithSafeIterationClosing(iterator, mapfn, [step.value, index], true) : step.value;
	      createProperty(result, index, value);
	    }
	  } else {
	    length = toLength$5(O.length);
	    result = IS_CONSTRUCTOR ? new this(length) : Array(length);
	    for (;length > index; index++) {
	      value = mapping ? mapfn(O[index], index) : O[index];
	      createProperty(result, index, value);
	    }
	  }
	  result.length = index;
	  return result;
	};

	var wellKnownSymbol$8 = wellKnownSymbol$h;

	var ITERATOR$1 = wellKnownSymbol$8('iterator');
	var SAFE_CLOSING = false;

	try {
	  var called = 0;
	  var iteratorWithReturn = {
	    next: function () {
	      return { done: !!called++ };
	    },
	    'return': function () {
	      SAFE_CLOSING = true;
	    }
	  };
	  iteratorWithReturn[ITERATOR$1] = function () {
	    return this;
	  };
	  // eslint-disable-next-line es/no-array-from, no-throw-literal -- required for testing
	  Array.from(iteratorWithReturn, function () { throw 2; });
	} catch (error) { /* empty */ }

	var checkCorrectnessOfIteration$2 = function (exec, SKIP_CLOSING) {
	  if (!SKIP_CLOSING && !SAFE_CLOSING) return false;
	  var ITERATION_SUPPORT = false;
	  try {
	    var object = {};
	    object[ITERATOR$1] = function () {
	      return {
	        next: function () {
	          return { done: ITERATION_SUPPORT = true };
	        }
	      };
	    };
	    exec(object);
	  } catch (error) { /* empty */ }
	  return ITERATION_SUPPORT;
	};

	var $$e = _export;
	var from = arrayFrom;
	var checkCorrectnessOfIteration$1 = checkCorrectnessOfIteration$2;

	var INCORRECT_ITERATION$1 = !checkCorrectnessOfIteration$1(function (iterable) {
	  // eslint-disable-next-line es/no-array-from -- required for testing
	  Array.from(iterable);
	});

	// `Array.from` method
	// https://tc39.es/ecma262/#sec-array.from
	$$e({ target: 'Array', stat: true, forced: INCORRECT_ITERATION$1 }, {
	  from: from
	});

	var global$c = global$n;

	var path$5 = global$c;

	var path$4 = path$5;

	path$4.Array.from;

	var wellKnownSymbol$7 = wellKnownSymbol$h;
	var create$1 = objectCreate;
	var definePropertyModule$1 = objectDefineProperty;

	var UNSCOPABLES = wellKnownSymbol$7('unscopables');
	var ArrayPrototype = Array.prototype;

	// Array.prototype[@@unscopables]
	// https://tc39.es/ecma262/#sec-array.prototype-@@unscopables
	if (ArrayPrototype[UNSCOPABLES] == undefined) {
	  definePropertyModule$1.f(ArrayPrototype, UNSCOPABLES, {
	    configurable: true,
	    value: create$1(null)
	  });
	}

	// add a key to Array.prototype[@@unscopables]
	var addToUnscopables$4 = function (key) {
	  ArrayPrototype[UNSCOPABLES][key] = true;
	};

	var $$d = _export;
	var $includes = arrayIncludes.includes;
	var addToUnscopables$3 = addToUnscopables$4;

	// `Array.prototype.includes` method
	// https://tc39.es/ecma262/#sec-array.prototype.includes
	$$d({ target: 'Array', proto: true }, {
	  includes: function includes(el /* , fromIndex = 0 */) {
	    return $includes(this, el, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	// https://tc39.es/ecma262/#sec-array.prototype-@@unscopables
	addToUnscopables$3('includes');

	var global$b = global$n;
	var bind$6 = functionBindContext;

	var call = Function.call;

	var entryUnbind$4 = function (CONSTRUCTOR, METHOD, length) {
	  return bind$6(call, global$b[CONSTRUCTOR].prototype[METHOD], length);
	};

	var entryUnbind$3 = entryUnbind$4;

	entryUnbind$3('Array', 'includes');

	var classof$3 = classofRaw$1;

	// `IsArray` abstract operation
	// https://tc39.es/ecma262/#sec-isarray
	// eslint-disable-next-line es/no-array-isarray -- safe
	var isArray$2 = Array.isArray || function isArray(argument) {
	  return classof$3(argument) == 'Array';
	};

	var isArray$1 = isArray$2;
	var toLength$4 = toLength$7;
	var bind$5 = functionBindContext;

	// `FlattenIntoArray` abstract operation
	// https://tc39.github.io/proposal-flatMap/#sec-FlattenIntoArray
	var flattenIntoArray$1 = function (target, original, source, sourceLen, start, depth, mapper, thisArg) {
	  var targetIndex = start;
	  var sourceIndex = 0;
	  var mapFn = mapper ? bind$5(mapper, thisArg, 3) : false;
	  var element;

	  while (sourceIndex < sourceLen) {
	    if (sourceIndex in source) {
	      element = mapFn ? mapFn(source[sourceIndex], sourceIndex, original) : source[sourceIndex];

	      if (depth > 0 && isArray$1(element)) {
	        targetIndex = flattenIntoArray$1(target, original, element, toLength$4(element.length), targetIndex, depth - 1) - 1;
	      } else {
	        if (targetIndex >= 0x1FFFFFFFFFFFFF) throw TypeError('Exceed the acceptable array length');
	        target[targetIndex] = element;
	      }

	      targetIndex++;
	    }
	    sourceIndex++;
	  }
	  return targetIndex;
	};

	var flattenIntoArray_1 = flattenIntoArray$1;

	var isArray = isArray$2;
	var isConstructor$1 = isConstructor$3;
	var isObject$4 = isObject$a;
	var wellKnownSymbol$6 = wellKnownSymbol$h;

	var SPECIES$3 = wellKnownSymbol$6('species');

	// a part of `ArraySpeciesCreate` abstract operation
	// https://tc39.es/ecma262/#sec-arrayspeciescreate
	var arraySpeciesConstructor$1 = function (originalArray) {
	  var C;
	  if (isArray(originalArray)) {
	    C = originalArray.constructor;
	    // cross-realm fallback
	    if (isConstructor$1(C) && (C === Array || isArray(C.prototype))) C = undefined;
	    else if (isObject$4(C)) {
	      C = C[SPECIES$3];
	      if (C === null) C = undefined;
	    }
	  } return C === undefined ? Array : C;
	};

	var arraySpeciesConstructor = arraySpeciesConstructor$1;

	// `ArraySpeciesCreate` abstract operation
	// https://tc39.es/ecma262/#sec-arrayspeciescreate
	var arraySpeciesCreate$2 = function (originalArray, length) {
	  return new (arraySpeciesConstructor(originalArray))(length === 0 ? 0 : length);
	};

	var $$c = _export;
	var flattenIntoArray = flattenIntoArray_1;
	var toObject$2 = toObject$6;
	var toLength$3 = toLength$7;
	var toInteger = toInteger$4;
	var arraySpeciesCreate$1 = arraySpeciesCreate$2;

	// `Array.prototype.flat` method
	// https://tc39.es/ecma262/#sec-array.prototype.flat
	$$c({ target: 'Array', proto: true }, {
	  flat: function flat(/* depthArg = 1 */) {
	    var depthArg = arguments.length ? arguments[0] : undefined;
	    var O = toObject$2(this);
	    var sourceLen = toLength$3(O.length);
	    var A = arraySpeciesCreate$1(O, 0);
	    A.length = flattenIntoArray(A, O, O, sourceLen, 0, depthArg === undefined ? 1 : toInteger(depthArg));
	    return A;
	  }
	});

	// this method was added to unscopables after implementation
	// in popular engines, so it's moved to a separate module
	var addToUnscopables$2 = addToUnscopables$4;

	// https://tc39.es/ecma262/#sec-array.prototype-@@unscopables
	addToUnscopables$2('flat');

	var entryUnbind$2 = entryUnbind$4;

	entryUnbind$2('Array', 'flat');

	var bind$4 = functionBindContext;
	var IndexedObject$1 = indexedObject;
	var toObject$1 = toObject$6;
	var toLength$2 = toLength$7;
	var arraySpeciesCreate = arraySpeciesCreate$2;

	var push$1 = [].push;

	// `Array.prototype.{ forEach, map, filter, some, every, find, findIndex, filterReject }` methods implementation
	var createMethod$1 = function (TYPE) {
	  var IS_MAP = TYPE == 1;
	  var IS_FILTER = TYPE == 2;
	  var IS_SOME = TYPE == 3;
	  var IS_EVERY = TYPE == 4;
	  var IS_FIND_INDEX = TYPE == 6;
	  var IS_FILTER_REJECT = TYPE == 7;
	  var NO_HOLES = TYPE == 5 || IS_FIND_INDEX;
	  return function ($this, callbackfn, that, specificCreate) {
	    var O = toObject$1($this);
	    var self = IndexedObject$1(O);
	    var boundFunction = bind$4(callbackfn, that, 3);
	    var length = toLength$2(self.length);
	    var index = 0;
	    var create = specificCreate || arraySpeciesCreate;
	    var target = IS_MAP ? create($this, length) : IS_FILTER || IS_FILTER_REJECT ? create($this, 0) : undefined;
	    var value, result;
	    for (;length > index; index++) if (NO_HOLES || index in self) {
	      value = self[index];
	      result = boundFunction(value, index, O);
	      if (TYPE) {
	        if (IS_MAP) target[index] = result; // map
	        else if (result) switch (TYPE) {
	          case 3: return true;              // some
	          case 5: return value;             // find
	          case 6: return index;             // findIndex
	          case 2: push$1.call(target, value); // filter
	        } else switch (TYPE) {
	          case 4: return false;             // every
	          case 7: push$1.call(target, value); // filterReject
	        }
	      }
	    }
	    return IS_FIND_INDEX ? -1 : IS_SOME || IS_EVERY ? IS_EVERY : target;
	  };
	};

	var arrayIteration = {
	  // `Array.prototype.forEach` method
	  // https://tc39.es/ecma262/#sec-array.prototype.foreach
	  forEach: createMethod$1(0),
	  // `Array.prototype.map` method
	  // https://tc39.es/ecma262/#sec-array.prototype.map
	  map: createMethod$1(1),
	  // `Array.prototype.filter` method
	  // https://tc39.es/ecma262/#sec-array.prototype.filter
	  filter: createMethod$1(2),
	  // `Array.prototype.some` method
	  // https://tc39.es/ecma262/#sec-array.prototype.some
	  some: createMethod$1(3),
	  // `Array.prototype.every` method
	  // https://tc39.es/ecma262/#sec-array.prototype.every
	  every: createMethod$1(4),
	  // `Array.prototype.find` method
	  // https://tc39.es/ecma262/#sec-array.prototype.find
	  find: createMethod$1(5),
	  // `Array.prototype.findIndex` method
	  // https://tc39.es/ecma262/#sec-array.prototype.findIndex
	  findIndex: createMethod$1(6),
	  // `Array.prototype.filterReject` method
	  // https://github.com/tc39/proposal-array-filtering
	  filterReject: createMethod$1(7)
	};

	var $$b = _export;
	var $find = arrayIteration.find;
	var addToUnscopables$1 = addToUnscopables$4;

	var FIND = 'find';
	var SKIPS_HOLES = true;

	// Shouldn't skip holes
	if (FIND in []) Array(1)[FIND](function () { SKIPS_HOLES = false; });

	// `Array.prototype.find` method
	// https://tc39.es/ecma262/#sec-array.prototype.find
	$$b({ target: 'Array', proto: true, forced: SKIPS_HOLES }, {
	  find: function find(callbackfn /* , that = undefined */) {
	    return $find(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	// https://tc39.es/ecma262/#sec-array.prototype-@@unscopables
	addToUnscopables$1(FIND);

	var entryUnbind$1 = entryUnbind$4;

	entryUnbind$1('Array', 'find');

	var DESCRIPTORS$2 = descriptors;
	var fails$2 = fails$b;
	var objectKeys$1 = objectKeys$3;
	var getOwnPropertySymbolsModule = objectGetOwnPropertySymbols;
	var propertyIsEnumerableModule = objectPropertyIsEnumerable;
	var toObject = toObject$6;
	var IndexedObject = indexedObject;

	// eslint-disable-next-line es/no-object-assign -- safe
	var $assign = Object.assign;
	// eslint-disable-next-line es/no-object-defineproperty -- required for testing
	var defineProperty = Object.defineProperty;

	// `Object.assign` method
	// https://tc39.es/ecma262/#sec-object.assign
	var objectAssign = !$assign || fails$2(function () {
	  // should have correct order of operations (Edge bug)
	  if (DESCRIPTORS$2 && $assign({ b: 1 }, $assign(defineProperty({}, 'a', {
	    enumerable: true,
	    get: function () {
	      defineProperty(this, 'b', {
	        value: 3,
	        enumerable: false
	      });
	    }
	  }), { b: 2 })).b !== 1) return true;
	  // should work with symbols and should have deterministic property order (V8 bug)
	  var A = {};
	  var B = {};
	  // eslint-disable-next-line es/no-symbol -- safe
	  var symbol = Symbol();
	  var alphabet = 'abcdefghijklmnopqrst';
	  A[symbol] = 7;
	  alphabet.split('').forEach(function (chr) { B[chr] = chr; });
	  return $assign({}, A)[symbol] != 7 || objectKeys$1($assign({}, B)).join('') != alphabet;
	}) ? function assign(target, source) { // eslint-disable-line no-unused-vars -- required for `.length`
	  var T = toObject(target);
	  var argumentsLength = arguments.length;
	  var index = 1;
	  var getOwnPropertySymbols = getOwnPropertySymbolsModule.f;
	  var propertyIsEnumerable = propertyIsEnumerableModule.f;
	  while (argumentsLength > index) {
	    var S = IndexedObject(arguments[index++]);
	    var keys = getOwnPropertySymbols ? objectKeys$1(S).concat(getOwnPropertySymbols(S)) : objectKeys$1(S);
	    var length = keys.length;
	    var j = 0;
	    var key;
	    while (length > j) {
	      key = keys[j++];
	      if (!DESCRIPTORS$2 || propertyIsEnumerable.call(S, key)) T[key] = S[key];
	    }
	  } return T;
	} : $assign;

	var $$a = _export;
	var assign = objectAssign;

	// `Object.assign` method
	// https://tc39.es/ecma262/#sec-object.assign
	// eslint-disable-next-line es/no-object-assign -- required for testing
	$$a({ target: 'Object', stat: true, forced: Object.assign !== assign }, {
	  assign: assign
	});

	var path$3 = path$5;

	path$3.Object.assign;

	var DESCRIPTORS$1 = descriptors;
	var objectKeys = objectKeys$3;
	var toIndexedObject$1 = toIndexedObject$5;
	var propertyIsEnumerable = objectPropertyIsEnumerable.f;

	// `Object.{ entries, values }` methods implementation
	var createMethod = function (TO_ENTRIES) {
	  return function (it) {
	    var O = toIndexedObject$1(it);
	    var keys = objectKeys(O);
	    var length = keys.length;
	    var i = 0;
	    var result = [];
	    var key;
	    while (length > i) {
	      key = keys[i++];
	      if (!DESCRIPTORS$1 || propertyIsEnumerable.call(O, key)) {
	        result.push(TO_ENTRIES ? [key, O[key]] : O[key]);
	      }
	    }
	    return result;
	  };
	};

	var objectToArray = {
	  // `Object.entries` method
	  // https://tc39.es/ecma262/#sec-object.entries
	  entries: createMethod(true),
	  // `Object.values` method
	  // https://tc39.es/ecma262/#sec-object.values
	  values: createMethod(false)
	};

	var $$9 = _export;
	var $entries = objectToArray.entries;

	// `Object.entries` method
	// https://tc39.es/ecma262/#sec-object.entries
	$$9({ target: 'Object', stat: true }, {
	  entries: function entries(O) {
	    return $entries(O);
	  }
	});

	var path$2 = path$5;

	path$2.Object.entries;

	var $$8 = _export;
	var $values = objectToArray.values;

	// `Object.values` method
	// https://tc39.es/ecma262/#sec-object.values
	$$8({ target: 'Object', stat: true }, {
	  values: function values(O) {
	    return $values(O);
	  }
	});

	var path$1 = path$5;

	path$1.Object.values;

	var anObject$2 = anObject$b;
	var isArrayIteratorMethod = isArrayIteratorMethod$2;
	var toLength$1 = toLength$7;
	var bind$3 = functionBindContext;
	var getIterator = getIterator$2;
	var getIteratorMethod = getIteratorMethod$3;
	var iteratorClose = iteratorClose$2;

	var Result = function (stopped, result) {
	  this.stopped = stopped;
	  this.result = result;
	};

	var iterate$4 = function (iterable, unboundFunction, options) {
	  var that = options && options.that;
	  var AS_ENTRIES = !!(options && options.AS_ENTRIES);
	  var IS_ITERATOR = !!(options && options.IS_ITERATOR);
	  var INTERRUPTED = !!(options && options.INTERRUPTED);
	  var fn = bind$3(unboundFunction, that, 1 + AS_ENTRIES + INTERRUPTED);
	  var iterator, iterFn, index, length, result, next, step;

	  var stop = function (condition) {
	    if (iterator) iteratorClose(iterator, 'normal', condition);
	    return new Result(true, condition);
	  };

	  var callFn = function (value) {
	    if (AS_ENTRIES) {
	      anObject$2(value);
	      return INTERRUPTED ? fn(value[0], value[1], stop) : fn(value[0], value[1]);
	    } return INTERRUPTED ? fn(value, stop) : fn(value);
	  };

	  if (IS_ITERATOR) {
	    iterator = iterable;
	  } else {
	    iterFn = getIteratorMethod(iterable);
	    if (!iterFn) throw TypeError(String(iterable) + ' is not iterable');
	    // optimisation for array iterators
	    if (isArrayIteratorMethod(iterFn)) {
	      for (index = 0, length = toLength$1(iterable.length); length > index; index++) {
	        result = callFn(iterable[index]);
	        if (result && result instanceof Result) return result;
	      } return new Result(false);
	    }
	    iterator = getIterator(iterable, iterFn);
	  }

	  next = iterator.next;
	  while (!(step = next.call(iterator)).done) {
	    try {
	      result = callFn(step.value);
	    } catch (error) {
	      iteratorClose(iterator, 'throw', error);
	    }
	    if (typeof result == 'object' && result && result instanceof Result) return result;
	  } return new Result(false);
	};

	var $$7 = _export;
	var getPrototypeOf = objectGetPrototypeOf;
	var setPrototypeOf$1 = objectSetPrototypeOf;
	var create = objectCreate;
	var createNonEnumerableProperty$1 = createNonEnumerableProperty$6;
	var createPropertyDescriptor = createPropertyDescriptor$5;
	var iterate$3 = iterate$4;
	var toString$2 = toString$5;

	var $AggregateError = function AggregateError(errors, message) {
	  var that = this;
	  if (!(that instanceof $AggregateError)) return new $AggregateError(errors, message);
	  if (setPrototypeOf$1) {
	    // eslint-disable-next-line unicorn/error-message -- expected
	    that = setPrototypeOf$1(new Error(undefined), getPrototypeOf(that));
	  }
	  if (message !== undefined) createNonEnumerableProperty$1(that, 'message', toString$2(message));
	  var errorsArray = [];
	  iterate$3(errors, errorsArray.push, { that: errorsArray });
	  createNonEnumerableProperty$1(that, 'errors', errorsArray);
	  return that;
	};

	$AggregateError.prototype = create(Error.prototype, {
	  constructor: createPropertyDescriptor(5, $AggregateError),
	  message: createPropertyDescriptor(5, ''),
	  name: createPropertyDescriptor(5, 'AggregateError')
	});

	// `AggregateError` constructor
	// https://tc39.es/ecma262/#sec-aggregate-error-constructor
	$$7({ global: true }, {
	  AggregateError: $AggregateError
	});

	var toIndexedObject = toIndexedObject$5;
	var addToUnscopables = addToUnscopables$4;
	var Iterators = iterators;
	var InternalStateModule$1 = internalState;
	var defineIterator = defineIterator$2;

	var ARRAY_ITERATOR = 'Array Iterator';
	var setInternalState$1 = InternalStateModule$1.set;
	var getInternalState$1 = InternalStateModule$1.getterFor(ARRAY_ITERATOR);

	// `Array.prototype.entries` method
	// https://tc39.es/ecma262/#sec-array.prototype.entries
	// `Array.prototype.keys` method
	// https://tc39.es/ecma262/#sec-array.prototype.keys
	// `Array.prototype.values` method
	// https://tc39.es/ecma262/#sec-array.prototype.values
	// `Array.prototype[@@iterator]` method
	// https://tc39.es/ecma262/#sec-array.prototype-@@iterator
	// `CreateArrayIterator` internal method
	// https://tc39.es/ecma262/#sec-createarrayiterator
	var es_array_iterator = defineIterator(Array, 'Array', function (iterated, kind) {
	  setInternalState$1(this, {
	    type: ARRAY_ITERATOR,
	    target: toIndexedObject(iterated), // target
	    index: 0,                          // next index
	    kind: kind                         // kind
	  });
	// `%ArrayIteratorPrototype%.next` method
	// https://tc39.es/ecma262/#sec-%arrayiteratorprototype%.next
	}, function () {
	  var state = getInternalState$1(this);
	  var target = state.target;
	  var kind = state.kind;
	  var index = state.index++;
	  if (!target || index >= target.length) {
	    state.target = undefined;
	    return { value: undefined, done: true };
	  }
	  if (kind == 'keys') return { value: index, done: false };
	  if (kind == 'values') return { value: target[index], done: false };
	  return { value: [index, target[index]], done: false };
	}, 'values');

	// argumentsList[@@iterator] is %ArrayProto_values%
	// https://tc39.es/ecma262/#sec-createunmappedargumentsobject
	// https://tc39.es/ecma262/#sec-createmappedargumentsobject
	Iterators.Arguments = Iterators.Array;

	// https://tc39.es/ecma262/#sec-array.prototype-@@unscopables
	addToUnscopables('keys');
	addToUnscopables('values');
	addToUnscopables('entries');

	var TO_STRING_TAG_SUPPORT$1 = toStringTagSupport;
	var classof$2 = classof$8;

	// `Object.prototype.toString` method implementation
	// https://tc39.es/ecma262/#sec-object.prototype.tostring
	var objectToString = TO_STRING_TAG_SUPPORT$1 ? {}.toString : function toString() {
	  return '[object ' + classof$2(this) + ']';
	};

	var TO_STRING_TAG_SUPPORT = toStringTagSupport;
	var redefine$3 = redefine$7.exports;
	var toString$1 = objectToString;

	// `Object.prototype.toString` method
	// https://tc39.es/ecma262/#sec-object.prototype.tostring
	if (!TO_STRING_TAG_SUPPORT) {
	  redefine$3(Object.prototype, 'toString', toString$1, { unsafe: true });
	}

	var global$a = global$n;

	var nativePromiseConstructor = global$a.Promise;

	var redefine$2 = redefine$7.exports;

	var redefineAll$1 = function (target, src, options) {
	  for (var key in src) redefine$2(target, key, src[key], options);
	  return target;
	};

	var getBuiltIn$3 = getBuiltIn$9;
	var definePropertyModule = objectDefineProperty;
	var wellKnownSymbol$5 = wellKnownSymbol$h;
	var DESCRIPTORS = descriptors;

	var SPECIES$2 = wellKnownSymbol$5('species');

	var setSpecies$1 = function (CONSTRUCTOR_NAME) {
	  var Constructor = getBuiltIn$3(CONSTRUCTOR_NAME);
	  var defineProperty = definePropertyModule.f;

	  if (DESCRIPTORS && Constructor && !Constructor[SPECIES$2]) {
	    defineProperty(Constructor, SPECIES$2, {
	      configurable: true,
	      get: function () { return this; }
	    });
	  }
	};

	var anInstance$1 = function (it, Constructor, name) {
	  if (it instanceof Constructor) return it;
	  throw TypeError('Incorrect ' + (name ? name + ' ' : '') + 'invocation');
	};

	var isConstructor = isConstructor$3;
	var tryToString = tryToString$2;

	// `Assert: IsConstructor(argument) is true`
	var aConstructor$1 = function (argument) {
	  if (isConstructor(argument)) return argument;
	  throw TypeError(tryToString(argument) + ' is not a constructor');
	};

	var anObject$1 = anObject$b;
	var aConstructor = aConstructor$1;
	var wellKnownSymbol$4 = wellKnownSymbol$h;

	var SPECIES$1 = wellKnownSymbol$4('species');

	// `SpeciesConstructor` abstract operation
	// https://tc39.es/ecma262/#sec-speciesconstructor
	var speciesConstructor$2 = function (O, defaultConstructor) {
	  var C = anObject$1(O).constructor;
	  var S;
	  return C === undefined || (S = anObject$1(C)[SPECIES$1]) == undefined ? defaultConstructor : aConstructor(S);
	};

	var userAgent$2 = engineUserAgent;

	var engineIsIos = /(?:ipad|iphone|ipod).*applewebkit/i.test(userAgent$2);

	var classof$1 = classofRaw$1;
	var global$9 = global$n;

	var engineIsNode = classof$1(global$9.process) == 'process';

	var global$8 = global$n;
	var isCallable$2 = isCallable$i;
	var fails$1 = fails$b;
	var bind$2 = functionBindContext;
	var html = html$2;
	var createElement = documentCreateElement$2;
	var IS_IOS$1 = engineIsIos;
	var IS_NODE$2 = engineIsNode;

	var set$1 = global$8.setImmediate;
	var clear = global$8.clearImmediate;
	var process$2 = global$8.process;
	var MessageChannel = global$8.MessageChannel;
	var Dispatch = global$8.Dispatch;
	var counter = 0;
	var queue$1 = {};
	var ONREADYSTATECHANGE = 'onreadystatechange';
	var location$1, defer, channel, port;

	try {
	  // Deno throws a ReferenceError on `location` access without `--location` flag
	  location$1 = global$8.location;
	} catch (error) { /* empty */ }

	var run = function (id) {
	  // eslint-disable-next-line no-prototype-builtins -- safe
	  if (queue$1.hasOwnProperty(id)) {
	    var fn = queue$1[id];
	    delete queue$1[id];
	    fn();
	  }
	};

	var runner = function (id) {
	  return function () {
	    run(id);
	  };
	};

	var listener = function (event) {
	  run(event.data);
	};

	var post = function (id) {
	  // old engines have not location.origin
	  global$8.postMessage(String(id), location$1.protocol + '//' + location$1.host);
	};

	// Node.js 0.9+ & IE10+ has setImmediate, otherwise:
	if (!set$1 || !clear) {
	  set$1 = function setImmediate(fn) {
	    var args = [];
	    var argumentsLength = arguments.length;
	    var i = 1;
	    while (argumentsLength > i) args.push(arguments[i++]);
	    queue$1[++counter] = function () {
	      // eslint-disable-next-line no-new-func -- spec requirement
	      (isCallable$2(fn) ? fn : Function(fn)).apply(undefined, args);
	    };
	    defer(counter);
	    return counter;
	  };
	  clear = function clearImmediate(id) {
	    delete queue$1[id];
	  };
	  // Node.js 0.8-
	  if (IS_NODE$2) {
	    defer = function (id) {
	      process$2.nextTick(runner(id));
	    };
	  // Sphere (JS game engine) Dispatch API
	  } else if (Dispatch && Dispatch.now) {
	    defer = function (id) {
	      Dispatch.now(runner(id));
	    };
	  // Browsers with MessageChannel, includes WebWorkers
	  // except iOS - https://github.com/zloirock/core-js/issues/624
	  } else if (MessageChannel && !IS_IOS$1) {
	    channel = new MessageChannel();
	    port = channel.port2;
	    channel.port1.onmessage = listener;
	    defer = bind$2(port.postMessage, port, 1);
	  // Browsers with postMessage, skip WebWorkers
	  // IE8 has postMessage, but it's sync & typeof its postMessage is 'object'
	  } else if (
	    global$8.addEventListener &&
	    isCallable$2(global$8.postMessage) &&
	    !global$8.importScripts &&
	    location$1 && location$1.protocol !== 'file:' &&
	    !fails$1(post)
	  ) {
	    defer = post;
	    global$8.addEventListener('message', listener, false);
	  // IE8-
	  } else if (ONREADYSTATECHANGE in createElement('script')) {
	    defer = function (id) {
	      html.appendChild(createElement('script'))[ONREADYSTATECHANGE] = function () {
	        html.removeChild(this);
	        run(id);
	      };
	    };
	  // Rest old browsers
	  } else {
	    defer = function (id) {
	      setTimeout(runner(id), 0);
	    };
	  }
	}

	var task$1 = {
	  set: set$1,
	  clear: clear
	};

	var userAgent$1 = engineUserAgent;
	var global$7 = global$n;

	var engineIsIosPebble = /ipad|iphone|ipod/i.test(userAgent$1) && global$7.Pebble !== undefined;

	var userAgent = engineUserAgent;

	var engineIsWebosWebkit = /web0s(?!.*chrome)/i.test(userAgent);

	var global$6 = global$n;
	var getOwnPropertyDescriptor$1 = objectGetOwnPropertyDescriptor.f;
	var macrotask = task$1.set;
	var IS_IOS = engineIsIos;
	var IS_IOS_PEBBLE = engineIsIosPebble;
	var IS_WEBOS_WEBKIT = engineIsWebosWebkit;
	var IS_NODE$1 = engineIsNode;

	var MutationObserver$1 = global$6.MutationObserver || global$6.WebKitMutationObserver;
	var document$2 = global$6.document;
	var process$1 = global$6.process;
	var Promise$1 = global$6.Promise;
	// Node.js 11 shows ExperimentalWarning on getting `queueMicrotask`
	var queueMicrotaskDescriptor = getOwnPropertyDescriptor$1(global$6, 'queueMicrotask');
	var queueMicrotask$1 = queueMicrotaskDescriptor && queueMicrotaskDescriptor.value;

	var flush, head, last, notify$1, toggle, node, promise, then;

	// modern engines have queueMicrotask method
	if (!queueMicrotask$1) {
	  flush = function () {
	    var parent, fn;
	    if (IS_NODE$1 && (parent = process$1.domain)) parent.exit();
	    while (head) {
	      fn = head.fn;
	      head = head.next;
	      try {
	        fn();
	      } catch (error) {
	        if (head) notify$1();
	        else last = undefined;
	        throw error;
	      }
	    } last = undefined;
	    if (parent) parent.enter();
	  };

	  // browsers with MutationObserver, except iOS - https://github.com/zloirock/core-js/issues/339
	  // also except WebOS Webkit https://github.com/zloirock/core-js/issues/898
	  if (!IS_IOS && !IS_NODE$1 && !IS_WEBOS_WEBKIT && MutationObserver$1 && document$2) {
	    toggle = true;
	    node = document$2.createTextNode('');
	    new MutationObserver$1(flush).observe(node, { characterData: true });
	    notify$1 = function () {
	      node.data = toggle = !toggle;
	    };
	  // environments with maybe non-completely correct, but existent Promise
	  } else if (!IS_IOS_PEBBLE && Promise$1 && Promise$1.resolve) {
	    // Promise.resolve without an argument throws an error in LG WebOS 2
	    promise = Promise$1.resolve(undefined);
	    // workaround of WebKit ~ iOS Safari 10.1 bug
	    promise.constructor = Promise$1;
	    then = promise.then;
	    notify$1 = function () {
	      then.call(promise, flush);
	    };
	  // Node.js without promises
	  } else if (IS_NODE$1) {
	    notify$1 = function () {
	      process$1.nextTick(flush);
	    };
	  // for other environments - macrotask based on:
	  // - setImmediate
	  // - MessageChannel
	  // - window.postMessag
	  // - onreadystatechange
	  // - setTimeout
	  } else {
	    notify$1 = function () {
	      // strange IE + webpack dev server bug - use .call(global)
	      macrotask.call(global$6, flush);
	    };
	  }
	}

	var microtask$1 = queueMicrotask$1 || function (fn) {
	  var task = { fn: fn, next: undefined };
	  if (last) last.next = task;
	  if (!head) {
	    head = task;
	    notify$1();
	  } last = task;
	};

	var newPromiseCapability$2 = {};

	var aCallable$3 = aCallable$7;

	var PromiseCapability = function (C) {
	  var resolve, reject;
	  this.promise = new C(function ($$resolve, $$reject) {
	    if (resolve !== undefined || reject !== undefined) throw TypeError('Bad Promise constructor');
	    resolve = $$resolve;
	    reject = $$reject;
	  });
	  this.resolve = aCallable$3(resolve);
	  this.reject = aCallable$3(reject);
	};

	// `NewPromiseCapability` abstract operation
	// https://tc39.es/ecma262/#sec-newpromisecapability
	newPromiseCapability$2.f = function (C) {
	  return new PromiseCapability(C);
	};

	var anObject = anObject$b;
	var isObject$3 = isObject$a;
	var newPromiseCapability$1 = newPromiseCapability$2;

	var promiseResolve$2 = function (C, x) {
	  anObject(C);
	  if (isObject$3(x) && x.constructor === C) return x;
	  var promiseCapability = newPromiseCapability$1.f(C);
	  var resolve = promiseCapability.resolve;
	  resolve(x);
	  return promiseCapability.promise;
	};

	var global$5 = global$n;

	var hostReportErrors$1 = function (a, b) {
	  var console = global$5.console;
	  if (console && console.error) {
	    arguments.length === 1 ? console.error(a) : console.error(a, b);
	  }
	};

	var perform$4 = function (exec) {
	  try {
	    return { error: false, value: exec() };
	  } catch (error) {
	    return { error: true, value: error };
	  }
	};

	var engineIsBrowser = typeof window == 'object';

	var $$6 = _export;
	var global$4 = global$n;
	var getBuiltIn$2 = getBuiltIn$9;
	var NativePromise$1 = nativePromiseConstructor;
	var redefine$1 = redefine$7.exports;
	var redefineAll = redefineAll$1;
	var setPrototypeOf = objectSetPrototypeOf;
	var setToStringTag = setToStringTag$3;
	var setSpecies = setSpecies$1;
	var aCallable$2 = aCallable$7;
	var isCallable$1 = isCallable$i;
	var isObject$2 = isObject$a;
	var anInstance = anInstance$1;
	var inspectSource = inspectSource$4;
	var iterate$2 = iterate$4;
	var checkCorrectnessOfIteration = checkCorrectnessOfIteration$2;
	var speciesConstructor$1 = speciesConstructor$2;
	var task = task$1.set;
	var microtask = microtask$1;
	var promiseResolve$1 = promiseResolve$2;
	var hostReportErrors = hostReportErrors$1;
	var newPromiseCapabilityModule$3 = newPromiseCapability$2;
	var perform$3 = perform$4;
	var InternalStateModule = internalState;
	var isForced = isForced_1;
	var wellKnownSymbol$3 = wellKnownSymbol$h;
	var IS_BROWSER = engineIsBrowser;
	var IS_NODE = engineIsNode;
	var V8_VERSION = engineV8Version;

	var SPECIES = wellKnownSymbol$3('species');
	var PROMISE = 'Promise';
	var getInternalState = InternalStateModule.get;
	var setInternalState = InternalStateModule.set;
	var getInternalPromiseState = InternalStateModule.getterFor(PROMISE);
	var NativePromisePrototype = NativePromise$1 && NativePromise$1.prototype;
	var PromiseConstructor = NativePromise$1;
	var PromiseConstructorPrototype = NativePromisePrototype;
	var TypeError$1 = global$4.TypeError;
	var document$1 = global$4.document;
	var process = global$4.process;
	var newPromiseCapability = newPromiseCapabilityModule$3.f;
	var newGenericPromiseCapability = newPromiseCapability;
	var DISPATCH_EVENT = !!(document$1 && document$1.createEvent && global$4.dispatchEvent);
	var NATIVE_REJECTION_EVENT = isCallable$1(global$4.PromiseRejectionEvent);
	var UNHANDLED_REJECTION = 'unhandledrejection';
	var REJECTION_HANDLED = 'rejectionhandled';
	var PENDING = 0;
	var FULFILLED = 1;
	var REJECTED = 2;
	var HANDLED = 1;
	var UNHANDLED = 2;
	var SUBCLASSING = false;
	var Internal, OwnPromiseCapability, PromiseWrapper, nativeThen;

	var FORCED = isForced(PROMISE, function () {
	  var PROMISE_CONSTRUCTOR_SOURCE = inspectSource(PromiseConstructor);
	  var GLOBAL_CORE_JS_PROMISE = PROMISE_CONSTRUCTOR_SOURCE !== String(PromiseConstructor);
	  // V8 6.6 (Node 10 and Chrome 66) have a bug with resolving custom thenables
	  // https://bugs.chromium.org/p/chromium/issues/detail?id=830565
	  // We can't detect it synchronously, so just check versions
	  if (!GLOBAL_CORE_JS_PROMISE && V8_VERSION === 66) return true;
	  // We can't use @@species feature detection in V8 since it causes
	  // deoptimization and performance degradation
	  // https://github.com/zloirock/core-js/issues/679
	  if (V8_VERSION >= 51 && /native code/.test(PROMISE_CONSTRUCTOR_SOURCE)) return false;
	  // Detect correctness of subclassing with @@species support
	  var promise = new PromiseConstructor(function (resolve) { resolve(1); });
	  var FakePromise = function (exec) {
	    exec(function () { /* empty */ }, function () { /* empty */ });
	  };
	  var constructor = promise.constructor = {};
	  constructor[SPECIES] = FakePromise;
	  SUBCLASSING = promise.then(function () { /* empty */ }) instanceof FakePromise;
	  if (!SUBCLASSING) return true;
	  // Unhandled rejections tracking support, NodeJS Promise without it fails @@species test
	  return !GLOBAL_CORE_JS_PROMISE && IS_BROWSER && !NATIVE_REJECTION_EVENT;
	});

	var INCORRECT_ITERATION = FORCED || !checkCorrectnessOfIteration(function (iterable) {
	  PromiseConstructor.all(iterable)['catch'](function () { /* empty */ });
	});

	// helpers
	var isThenable = function (it) {
	  var then;
	  return isObject$2(it) && isCallable$1(then = it.then) ? then : false;
	};

	var notify = function (state, isReject) {
	  if (state.notified) return;
	  state.notified = true;
	  var chain = state.reactions;
	  microtask(function () {
	    var value = state.value;
	    var ok = state.state == FULFILLED;
	    var index = 0;
	    // variable length - can't use forEach
	    while (chain.length > index) {
	      var reaction = chain[index++];
	      var handler = ok ? reaction.ok : reaction.fail;
	      var resolve = reaction.resolve;
	      var reject = reaction.reject;
	      var domain = reaction.domain;
	      var result, then, exited;
	      try {
	        if (handler) {
	          if (!ok) {
	            if (state.rejection === UNHANDLED) onHandleUnhandled(state);
	            state.rejection = HANDLED;
	          }
	          if (handler === true) result = value;
	          else {
	            if (domain) domain.enter();
	            result = handler(value); // can throw
	            if (domain) {
	              domain.exit();
	              exited = true;
	            }
	          }
	          if (result === reaction.promise) {
	            reject(TypeError$1('Promise-chain cycle'));
	          } else if (then = isThenable(result)) {
	            then.call(result, resolve, reject);
	          } else resolve(result);
	        } else reject(value);
	      } catch (error) {
	        if (domain && !exited) domain.exit();
	        reject(error);
	      }
	    }
	    state.reactions = [];
	    state.notified = false;
	    if (isReject && !state.rejection) onUnhandled(state);
	  });
	};

	var dispatchEvent = function (name, promise, reason) {
	  var event, handler;
	  if (DISPATCH_EVENT) {
	    event = document$1.createEvent('Event');
	    event.promise = promise;
	    event.reason = reason;
	    event.initEvent(name, false, true);
	    global$4.dispatchEvent(event);
	  } else event = { promise: promise, reason: reason };
	  if (!NATIVE_REJECTION_EVENT && (handler = global$4['on' + name])) handler(event);
	  else if (name === UNHANDLED_REJECTION) hostReportErrors('Unhandled promise rejection', reason);
	};

	var onUnhandled = function (state) {
	  task.call(global$4, function () {
	    var promise = state.facade;
	    var value = state.value;
	    var IS_UNHANDLED = isUnhandled(state);
	    var result;
	    if (IS_UNHANDLED) {
	      result = perform$3(function () {
	        if (IS_NODE) {
	          process.emit('unhandledRejection', value, promise);
	        } else dispatchEvent(UNHANDLED_REJECTION, promise, value);
	      });
	      // Browsers should not trigger `rejectionHandled` event if it was handled here, NodeJS - should
	      state.rejection = IS_NODE || isUnhandled(state) ? UNHANDLED : HANDLED;
	      if (result.error) throw result.value;
	    }
	  });
	};

	var isUnhandled = function (state) {
	  return state.rejection !== HANDLED && !state.parent;
	};

	var onHandleUnhandled = function (state) {
	  task.call(global$4, function () {
	    var promise = state.facade;
	    if (IS_NODE) {
	      process.emit('rejectionHandled', promise);
	    } else dispatchEvent(REJECTION_HANDLED, promise, state.value);
	  });
	};

	var bind$1 = function (fn, state, unwrap) {
	  return function (value) {
	    fn(state, value, unwrap);
	  };
	};

	var internalReject = function (state, value, unwrap) {
	  if (state.done) return;
	  state.done = true;
	  if (unwrap) state = unwrap;
	  state.value = value;
	  state.state = REJECTED;
	  notify(state, true);
	};

	var internalResolve = function (state, value, unwrap) {
	  if (state.done) return;
	  state.done = true;
	  if (unwrap) state = unwrap;
	  try {
	    if (state.facade === value) throw TypeError$1("Promise can't be resolved itself");
	    var then = isThenable(value);
	    if (then) {
	      microtask(function () {
	        var wrapper = { done: false };
	        try {
	          then.call(value,
	            bind$1(internalResolve, wrapper, state),
	            bind$1(internalReject, wrapper, state)
	          );
	        } catch (error) {
	          internalReject(wrapper, error, state);
	        }
	      });
	    } else {
	      state.value = value;
	      state.state = FULFILLED;
	      notify(state, false);
	    }
	  } catch (error) {
	    internalReject({ done: false }, error, state);
	  }
	};

	// constructor polyfill
	if (FORCED) {
	  // 25.4.3.1 Promise(executor)
	  PromiseConstructor = function Promise(executor) {
	    anInstance(this, PromiseConstructor, PROMISE);
	    aCallable$2(executor);
	    Internal.call(this);
	    var state = getInternalState(this);
	    try {
	      executor(bind$1(internalResolve, state), bind$1(internalReject, state));
	    } catch (error) {
	      internalReject(state, error);
	    }
	  };
	  PromiseConstructorPrototype = PromiseConstructor.prototype;
	  // eslint-disable-next-line no-unused-vars -- required for `.length`
	  Internal = function Promise(executor) {
	    setInternalState(this, {
	      type: PROMISE,
	      done: false,
	      notified: false,
	      parent: false,
	      reactions: [],
	      rejection: false,
	      state: PENDING,
	      value: undefined
	    });
	  };
	  Internal.prototype = redefineAll(PromiseConstructorPrototype, {
	    // `Promise.prototype.then` method
	    // https://tc39.es/ecma262/#sec-promise.prototype.then
	    then: function then(onFulfilled, onRejected) {
	      var state = getInternalPromiseState(this);
	      var reaction = newPromiseCapability(speciesConstructor$1(this, PromiseConstructor));
	      reaction.ok = isCallable$1(onFulfilled) ? onFulfilled : true;
	      reaction.fail = isCallable$1(onRejected) && onRejected;
	      reaction.domain = IS_NODE ? process.domain : undefined;
	      state.parent = true;
	      state.reactions.push(reaction);
	      if (state.state != PENDING) notify(state, false);
	      return reaction.promise;
	    },
	    // `Promise.prototype.catch` method
	    // https://tc39.es/ecma262/#sec-promise.prototype.catch
	    'catch': function (onRejected) {
	      return this.then(undefined, onRejected);
	    }
	  });
	  OwnPromiseCapability = function () {
	    var promise = new Internal();
	    var state = getInternalState(promise);
	    this.promise = promise;
	    this.resolve = bind$1(internalResolve, state);
	    this.reject = bind$1(internalReject, state);
	  };
	  newPromiseCapabilityModule$3.f = newPromiseCapability = function (C) {
	    return C === PromiseConstructor || C === PromiseWrapper
	      ? new OwnPromiseCapability(C)
	      : newGenericPromiseCapability(C);
	  };

	  if (isCallable$1(NativePromise$1) && NativePromisePrototype !== Object.prototype) {
	    nativeThen = NativePromisePrototype.then;

	    if (!SUBCLASSING) {
	      // make `Promise#then` return a polyfilled `Promise` for native promise-based APIs
	      redefine$1(NativePromisePrototype, 'then', function then(onFulfilled, onRejected) {
	        var that = this;
	        return new PromiseConstructor(function (resolve, reject) {
	          nativeThen.call(that, resolve, reject);
	        }).then(onFulfilled, onRejected);
	      // https://github.com/zloirock/core-js/issues/640
	      }, { unsafe: true });

	      // makes sure that native promise-based APIs `Promise#catch` properly works with patched `Promise#then`
	      redefine$1(NativePromisePrototype, 'catch', PromiseConstructorPrototype['catch'], { unsafe: true });
	    }

	    // make `.constructor === Promise` work for native promise-based APIs
	    try {
	      delete NativePromisePrototype.constructor;
	    } catch (error) { /* empty */ }

	    // make `instanceof Promise` work for native promise-based APIs
	    if (setPrototypeOf) {
	      setPrototypeOf(NativePromisePrototype, PromiseConstructorPrototype);
	    }
	  }
	}

	$$6({ global: true, wrap: true, forced: FORCED }, {
	  Promise: PromiseConstructor
	});

	setToStringTag(PromiseConstructor, PROMISE, false);
	setSpecies(PROMISE);

	PromiseWrapper = getBuiltIn$2(PROMISE);

	// statics
	$$6({ target: PROMISE, stat: true, forced: FORCED }, {
	  // `Promise.reject` method
	  // https://tc39.es/ecma262/#sec-promise.reject
	  reject: function reject(r) {
	    var capability = newPromiseCapability(this);
	    capability.reject.call(undefined, r);
	    return capability.promise;
	  }
	});

	$$6({ target: PROMISE, stat: true, forced: FORCED }, {
	  // `Promise.resolve` method
	  // https://tc39.es/ecma262/#sec-promise.resolve
	  resolve: function resolve(x) {
	    return promiseResolve$1(this, x);
	  }
	});

	$$6({ target: PROMISE, stat: true, forced: INCORRECT_ITERATION }, {
	  // `Promise.all` method
	  // https://tc39.es/ecma262/#sec-promise.all
	  all: function all(iterable) {
	    var C = this;
	    var capability = newPromiseCapability(C);
	    var resolve = capability.resolve;
	    var reject = capability.reject;
	    var result = perform$3(function () {
	      var $promiseResolve = aCallable$2(C.resolve);
	      var values = [];
	      var counter = 0;
	      var remaining = 1;
	      iterate$2(iterable, function (promise) {
	        var index = counter++;
	        var alreadyCalled = false;
	        values.push(undefined);
	        remaining++;
	        $promiseResolve.call(C, promise).then(function (value) {
	          if (alreadyCalled) return;
	          alreadyCalled = true;
	          values[index] = value;
	          --remaining || resolve(values);
	        }, reject);
	      });
	      --remaining || resolve(values);
	    });
	    if (result.error) reject(result.value);
	    return capability.promise;
	  },
	  // `Promise.race` method
	  // https://tc39.es/ecma262/#sec-promise.race
	  race: function race(iterable) {
	    var C = this;
	    var capability = newPromiseCapability(C);
	    var reject = capability.reject;
	    var result = perform$3(function () {
	      var $promiseResolve = aCallable$2(C.resolve);
	      iterate$2(iterable, function (promise) {
	        $promiseResolve.call(C, promise).then(capability.resolve, reject);
	      });
	    });
	    if (result.error) reject(result.value);
	    return capability.promise;
	  }
	});

	var $$5 = _export;
	var aCallable$1 = aCallable$7;
	var newPromiseCapabilityModule$2 = newPromiseCapability$2;
	var perform$2 = perform$4;
	var iterate$1 = iterate$4;

	// `Promise.allSettled` method
	// https://tc39.es/ecma262/#sec-promise.allsettled
	$$5({ target: 'Promise', stat: true }, {
	  allSettled: function allSettled(iterable) {
	    var C = this;
	    var capability = newPromiseCapabilityModule$2.f(C);
	    var resolve = capability.resolve;
	    var reject = capability.reject;
	    var result = perform$2(function () {
	      var promiseResolve = aCallable$1(C.resolve);
	      var values = [];
	      var counter = 0;
	      var remaining = 1;
	      iterate$1(iterable, function (promise) {
	        var index = counter++;
	        var alreadyCalled = false;
	        values.push(undefined);
	        remaining++;
	        promiseResolve.call(C, promise).then(function (value) {
	          if (alreadyCalled) return;
	          alreadyCalled = true;
	          values[index] = { status: 'fulfilled', value: value };
	          --remaining || resolve(values);
	        }, function (error) {
	          if (alreadyCalled) return;
	          alreadyCalled = true;
	          values[index] = { status: 'rejected', reason: error };
	          --remaining || resolve(values);
	        });
	      });
	      --remaining || resolve(values);
	    });
	    if (result.error) reject(result.value);
	    return capability.promise;
	  }
	});

	var $$4 = _export;
	var aCallable = aCallable$7;
	var getBuiltIn$1 = getBuiltIn$9;
	var newPromiseCapabilityModule$1 = newPromiseCapability$2;
	var perform$1 = perform$4;
	var iterate = iterate$4;

	var PROMISE_ANY_ERROR = 'No one promise resolved';

	// `Promise.any` method
	// https://tc39.es/ecma262/#sec-promise.any
	$$4({ target: 'Promise', stat: true }, {
	  any: function any(iterable) {
	    var C = this;
	    var capability = newPromiseCapabilityModule$1.f(C);
	    var resolve = capability.resolve;
	    var reject = capability.reject;
	    var result = perform$1(function () {
	      var promiseResolve = aCallable(C.resolve);
	      var errors = [];
	      var counter = 0;
	      var remaining = 1;
	      var alreadyResolved = false;
	      iterate(iterable, function (promise) {
	        var index = counter++;
	        var alreadyRejected = false;
	        errors.push(undefined);
	        remaining++;
	        promiseResolve.call(C, promise).then(function (value) {
	          if (alreadyRejected || alreadyResolved) return;
	          alreadyResolved = true;
	          resolve(value);
	        }, function (error) {
	          if (alreadyRejected || alreadyResolved) return;
	          alreadyRejected = true;
	          errors[index] = error;
	          --remaining || reject(new (getBuiltIn$1('AggregateError'))(errors, PROMISE_ANY_ERROR));
	        });
	      });
	      --remaining || reject(new (getBuiltIn$1('AggregateError'))(errors, PROMISE_ANY_ERROR));
	    });
	    if (result.error) reject(result.value);
	    return capability.promise;
	  }
	});

	var $$3 = _export;
	var NativePromise = nativePromiseConstructor;
	var fails = fails$b;
	var getBuiltIn = getBuiltIn$9;
	var isCallable = isCallable$i;
	var speciesConstructor = speciesConstructor$2;
	var promiseResolve = promiseResolve$2;
	var redefine = redefine$7.exports;

	// Safari bug https://bugs.webkit.org/show_bug.cgi?id=200829
	var NON_GENERIC = !!NativePromise && fails(function () {
	  NativePromise.prototype['finally'].call({ then: function () { /* empty */ } }, function () { /* empty */ });
	});

	// `Promise.prototype.finally` method
	// https://tc39.es/ecma262/#sec-promise.prototype.finally
	$$3({ target: 'Promise', proto: true, real: true, forced: NON_GENERIC }, {
	  'finally': function (onFinally) {
	    var C = speciesConstructor(this, getBuiltIn('Promise'));
	    var isFunction = isCallable(onFinally);
	    return this.then(
	      isFunction ? function (x) {
	        return promiseResolve(C, onFinally()).then(function () { return x; });
	      } : onFinally,
	      isFunction ? function (e) {
	        return promiseResolve(C, onFinally()).then(function () { throw e; });
	      } : onFinally
	    );
	  }
	});

	// makes sure that native promise-based APIs `Promise#finally` properly works with patched `Promise#then`
	if (isCallable(NativePromise)) {
	  var method = getBuiltIn('Promise').prototype['finally'];
	  if (NativePromise.prototype['finally'] !== method) {
	    redefine(NativePromise.prototype, 'finally', method, { unsafe: true });
	  }
	}

	var path = path$5;

	path.Promise;

	// iterable DOM collections
	// flag - `iterable` interface - 'entries', 'keys', 'values', 'forEach' methods
	var domIterables = {
	  CSSRuleList: 0,
	  CSSStyleDeclaration: 0,
	  CSSValueList: 0,
	  ClientRectList: 0,
	  DOMRectList: 0,
	  DOMStringList: 0,
	  DOMTokenList: 1,
	  DataTransferItemList: 0,
	  FileList: 0,
	  HTMLAllCollection: 0,
	  HTMLCollection: 0,
	  HTMLFormElement: 0,
	  HTMLSelectElement: 0,
	  MediaList: 0,
	  MimeTypeArray: 0,
	  NamedNodeMap: 0,
	  NodeList: 1,
	  PaintRequestList: 0,
	  Plugin: 0,
	  PluginArray: 0,
	  SVGLengthList: 0,
	  SVGNumberList: 0,
	  SVGPathSegList: 0,
	  SVGPointList: 0,
	  SVGStringList: 0,
	  SVGTransformList: 0,
	  SourceBufferList: 0,
	  StyleSheetList: 0,
	  TextTrackCueList: 0,
	  TextTrackList: 0,
	  TouchList: 0
	};

	// in old WebKit versions, `element.classList` is not an instance of global `DOMTokenList`
	var documentCreateElement = documentCreateElement$2;

	var classList = documentCreateElement('span').classList;
	var DOMTokenListPrototype$1 = classList && classList.constructor && classList.constructor.prototype;

	var domTokenListPrototype = DOMTokenListPrototype$1 === Object.prototype ? undefined : DOMTokenListPrototype$1;

	var global$3 = global$n;
	var DOMIterables = domIterables;
	var DOMTokenListPrototype = domTokenListPrototype;
	var ArrayIteratorMethods = es_array_iterator;
	var createNonEnumerableProperty = createNonEnumerableProperty$6;
	var wellKnownSymbol$2 = wellKnownSymbol$h;

	var ITERATOR = wellKnownSymbol$2('iterator');
	var TO_STRING_TAG = wellKnownSymbol$2('toStringTag');
	var ArrayValues = ArrayIteratorMethods.values;

	var handlePrototype = function (CollectionPrototype, COLLECTION_NAME) {
	  if (CollectionPrototype) {
	    // some Chrome versions have non-configurable methods on DOMTokenList
	    if (CollectionPrototype[ITERATOR] !== ArrayValues) try {
	      createNonEnumerableProperty(CollectionPrototype, ITERATOR, ArrayValues);
	    } catch (error) {
	      CollectionPrototype[ITERATOR] = ArrayValues;
	    }
	    if (!CollectionPrototype[TO_STRING_TAG]) {
	      createNonEnumerableProperty(CollectionPrototype, TO_STRING_TAG, COLLECTION_NAME);
	    }
	    if (DOMIterables[COLLECTION_NAME]) for (var METHOD_NAME in ArrayIteratorMethods) {
	      // some Chrome versions have non-configurable methods on DOMTokenList
	      if (CollectionPrototype[METHOD_NAME] !== ArrayIteratorMethods[METHOD_NAME]) try {
	        createNonEnumerableProperty(CollectionPrototype, METHOD_NAME, ArrayIteratorMethods[METHOD_NAME]);
	      } catch (error) {
	        CollectionPrototype[METHOD_NAME] = ArrayIteratorMethods[METHOD_NAME];
	      }
	    }
	  }
	};

	for (var COLLECTION_NAME in DOMIterables) {
	  handlePrototype(global$3[COLLECTION_NAME] && global$3[COLLECTION_NAME].prototype, COLLECTION_NAME);
	}

	handlePrototype(DOMTokenListPrototype, 'DOMTokenList');

	var $$2 = _export;
	var newPromiseCapabilityModule = newPromiseCapability$2;
	var perform = perform$4;

	// `Promise.try` method
	// https://github.com/tc39/proposal-promise-try
	$$2({ target: 'Promise', stat: true }, {
	  'try': function (callbackfn) {
	    var promiseCapability = newPromiseCapabilityModule.f(this);
	    var result = perform(callbackfn);
	    (result.error ? promiseCapability.reject : promiseCapability.resolve)(result.value);
	    return promiseCapability.promise;
	  }
	});

	var isObject$1 = isObject$a;
	var classof = classofRaw$1;
	var wellKnownSymbol$1 = wellKnownSymbol$h;

	var MATCH$1 = wellKnownSymbol$1('match');

	// `IsRegExp` abstract operation
	// https://tc39.es/ecma262/#sec-isregexp
	var isRegexp = function (it) {
	  var isRegExp;
	  return isObject$1(it) && ((isRegExp = it[MATCH$1]) !== undefined ? !!isRegExp : classof(it) == 'RegExp');
	};

	var isRegExp = isRegexp;

	var notARegexp = function (it) {
	  if (isRegExp(it)) {
	    throw TypeError("The method doesn't accept regular expressions");
	  } return it;
	};

	var wellKnownSymbol = wellKnownSymbol$h;

	var MATCH = wellKnownSymbol('match');

	var correctIsRegexpLogic = function (METHOD_NAME) {
	  var regexp = /./;
	  try {
	    '/./'[METHOD_NAME](regexp);
	  } catch (error1) {
	    try {
	      regexp[MATCH] = false;
	      return '/./'[METHOD_NAME](regexp);
	    } catch (error2) { /* empty */ }
	  } return false;
	};

	var $$1 = _export;
	var getOwnPropertyDescriptor = objectGetOwnPropertyDescriptor.f;
	var toLength = toLength$7;
	var toString = toString$5;
	var notARegExp = notARegexp;
	var requireObjectCoercible = requireObjectCoercible$4;
	var correctIsRegExpLogic = correctIsRegexpLogic;

	// eslint-disable-next-line es/no-string-prototype-startswith -- safe
	var $startsWith = ''.startsWith;
	var min = Math.min;

	var CORRECT_IS_REGEXP_LOGIC = correctIsRegExpLogic('startsWith');
	// https://github.com/zloirock/core-js/pull/702
	var MDN_POLYFILL_BUG = !CORRECT_IS_REGEXP_LOGIC && !!function () {
	  var descriptor = getOwnPropertyDescriptor(String.prototype, 'startsWith');
	  return descriptor && !descriptor.writable;
	}();

	// `String.prototype.startsWith` method
	// https://tc39.es/ecma262/#sec-string.prototype.startswith
	$$1({ target: 'String', proto: true, forced: !MDN_POLYFILL_BUG && !CORRECT_IS_REGEXP_LOGIC }, {
	  startsWith: function startsWith(searchString /* , position = 0 */) {
	    var that = toString(requireObjectCoercible(this));
	    notARegExp(searchString);
	    var index = toLength(min(arguments.length > 1 ? arguments[1] : undefined, that.length));
	    var search = toString(searchString);
	    return $startsWith
	      ? $startsWith.call(that, search, index)
	      : that.slice(index, index + search.length) === search;
	  }
	});

	var entryUnbind = entryUnbind$4;

	entryUnbind('String', 'startsWith');

	var global$2 =
	  (typeof globalThis !== 'undefined' && globalThis) ||
	  (typeof self !== 'undefined' && self) ||
	  (typeof global$2 !== 'undefined' && global$2);

	var support = {
	  searchParams: 'URLSearchParams' in global$2,
	  iterable: 'Symbol' in global$2 && 'iterator' in Symbol,
	  blob:
	    'FileReader' in global$2 &&
	    'Blob' in global$2 &&
	    (function() {
	      try {
	        new Blob();
	        return true
	      } catch (e) {
	        return false
	      }
	    })(),
	  formData: 'FormData' in global$2,
	  arrayBuffer: 'ArrayBuffer' in global$2
	};

	function isDataView(obj) {
	  return obj && DataView.prototype.isPrototypeOf(obj)
	}

	if (support.arrayBuffer) {
	  var viewClasses = [
	    '[object Int8Array]',
	    '[object Uint8Array]',
	    '[object Uint8ClampedArray]',
	    '[object Int16Array]',
	    '[object Uint16Array]',
	    '[object Int32Array]',
	    '[object Uint32Array]',
	    '[object Float32Array]',
	    '[object Float64Array]'
	  ];

	  var isArrayBufferView =
	    ArrayBuffer.isView ||
	    function(obj) {
	      return obj && viewClasses.indexOf(Object.prototype.toString.call(obj)) > -1
	    };
	}

	function normalizeName(name) {
	  if (typeof name !== 'string') {
	    name = String(name);
	  }
	  if (/[^a-z0-9\-#$%&'*+.^_`|~!]/i.test(name) || name === '') {
	    throw new TypeError('Invalid character in header field name: "' + name + '"')
	  }
	  return name.toLowerCase()
	}

	function normalizeValue(value) {
	  if (typeof value !== 'string') {
	    value = String(value);
	  }
	  return value
	}

	// Build a destructive iterator for the value list
	function iteratorFor(items) {
	  var iterator = {
	    next: function() {
	      var value = items.shift();
	      return {done: value === undefined, value: value}
	    }
	  };

	  if (support.iterable) {
	    iterator[Symbol.iterator] = function() {
	      return iterator
	    };
	  }

	  return iterator
	}

	function Headers(headers) {
	  this.map = {};

	  if (headers instanceof Headers) {
	    headers.forEach(function(value, name) {
	      this.append(name, value);
	    }, this);
	  } else if (Array.isArray(headers)) {
	    headers.forEach(function(header) {
	      this.append(header[0], header[1]);
	    }, this);
	  } else if (headers) {
	    Object.getOwnPropertyNames(headers).forEach(function(name) {
	      this.append(name, headers[name]);
	    }, this);
	  }
	}

	Headers.prototype.append = function(name, value) {
	  name = normalizeName(name);
	  value = normalizeValue(value);
	  var oldValue = this.map[name];
	  this.map[name] = oldValue ? oldValue + ', ' + value : value;
	};

	Headers.prototype['delete'] = function(name) {
	  delete this.map[normalizeName(name)];
	};

	Headers.prototype.get = function(name) {
	  name = normalizeName(name);
	  return this.has(name) ? this.map[name] : null
	};

	Headers.prototype.has = function(name) {
	  return this.map.hasOwnProperty(normalizeName(name))
	};

	Headers.prototype.set = function(name, value) {
	  this.map[normalizeName(name)] = normalizeValue(value);
	};

	Headers.prototype.forEach = function(callback, thisArg) {
	  for (var name in this.map) {
	    if (this.map.hasOwnProperty(name)) {
	      callback.call(thisArg, this.map[name], name, this);
	    }
	  }
	};

	Headers.prototype.keys = function() {
	  var items = [];
	  this.forEach(function(value, name) {
	    items.push(name);
	  });
	  return iteratorFor(items)
	};

	Headers.prototype.values = function() {
	  var items = [];
	  this.forEach(function(value) {
	    items.push(value);
	  });
	  return iteratorFor(items)
	};

	Headers.prototype.entries = function() {
	  var items = [];
	  this.forEach(function(value, name) {
	    items.push([name, value]);
	  });
	  return iteratorFor(items)
	};

	if (support.iterable) {
	  Headers.prototype[Symbol.iterator] = Headers.prototype.entries;
	}

	function consumed(body) {
	  if (body.bodyUsed) {
	    return Promise.reject(new TypeError('Already read'))
	  }
	  body.bodyUsed = true;
	}

	function fileReaderReady(reader) {
	  return new Promise(function(resolve, reject) {
	    reader.onload = function() {
	      resolve(reader.result);
	    };
	    reader.onerror = function() {
	      reject(reader.error);
	    };
	  })
	}

	function readBlobAsArrayBuffer(blob) {
	  var reader = new FileReader();
	  var promise = fileReaderReady(reader);
	  reader.readAsArrayBuffer(blob);
	  return promise
	}

	function readBlobAsText(blob) {
	  var reader = new FileReader();
	  var promise = fileReaderReady(reader);
	  reader.readAsText(blob);
	  return promise
	}

	function readArrayBufferAsText(buf) {
	  var view = new Uint8Array(buf);
	  var chars = new Array(view.length);

	  for (var i = 0; i < view.length; i++) {
	    chars[i] = String.fromCharCode(view[i]);
	  }
	  return chars.join('')
	}

	function bufferClone(buf) {
	  if (buf.slice) {
	    return buf.slice(0)
	  } else {
	    var view = new Uint8Array(buf.byteLength);
	    view.set(new Uint8Array(buf));
	    return view.buffer
	  }
	}

	function Body() {
	  this.bodyUsed = false;

	  this._initBody = function(body) {
	    /*
	      fetch-mock wraps the Response object in an ES6 Proxy to
	      provide useful test harness features such as flush. However, on
	      ES5 browsers without fetch or Proxy support pollyfills must be used;
	      the proxy-pollyfill is unable to proxy an attribute unless it exists
	      on the object before the Proxy is created. This change ensures
	      Response.bodyUsed exists on the instance, while maintaining the
	      semantic of setting Request.bodyUsed in the constructor before
	      _initBody is called.
	    */
	    this.bodyUsed = this.bodyUsed;
	    this._bodyInit = body;
	    if (!body) {
	      this._bodyText = '';
	    } else if (typeof body === 'string') {
	      this._bodyText = body;
	    } else if (support.blob && Blob.prototype.isPrototypeOf(body)) {
	      this._bodyBlob = body;
	    } else if (support.formData && FormData.prototype.isPrototypeOf(body)) {
	      this._bodyFormData = body;
	    } else if (support.searchParams && URLSearchParams.prototype.isPrototypeOf(body)) {
	      this._bodyText = body.toString();
	    } else if (support.arrayBuffer && support.blob && isDataView(body)) {
	      this._bodyArrayBuffer = bufferClone(body.buffer);
	      // IE 10-11 can't handle a DataView body.
	      this._bodyInit = new Blob([this._bodyArrayBuffer]);
	    } else if (support.arrayBuffer && (ArrayBuffer.prototype.isPrototypeOf(body) || isArrayBufferView(body))) {
	      this._bodyArrayBuffer = bufferClone(body);
	    } else {
	      this._bodyText = body = Object.prototype.toString.call(body);
	    }

	    if (!this.headers.get('content-type')) {
	      if (typeof body === 'string') {
	        this.headers.set('content-type', 'text/plain;charset=UTF-8');
	      } else if (this._bodyBlob && this._bodyBlob.type) {
	        this.headers.set('content-type', this._bodyBlob.type);
	      } else if (support.searchParams && URLSearchParams.prototype.isPrototypeOf(body)) {
	        this.headers.set('content-type', 'application/x-www-form-urlencoded;charset=UTF-8');
	      }
	    }
	  };

	  if (support.blob) {
	    this.blob = function() {
	      var rejected = consumed(this);
	      if (rejected) {
	        return rejected
	      }

	      if (this._bodyBlob) {
	        return Promise.resolve(this._bodyBlob)
	      } else if (this._bodyArrayBuffer) {
	        return Promise.resolve(new Blob([this._bodyArrayBuffer]))
	      } else if (this._bodyFormData) {
	        throw new Error('could not read FormData body as blob')
	      } else {
	        return Promise.resolve(new Blob([this._bodyText]))
	      }
	    };

	    this.arrayBuffer = function() {
	      if (this._bodyArrayBuffer) {
	        var isConsumed = consumed(this);
	        if (isConsumed) {
	          return isConsumed
	        }
	        if (ArrayBuffer.isView(this._bodyArrayBuffer)) {
	          return Promise.resolve(
	            this._bodyArrayBuffer.buffer.slice(
	              this._bodyArrayBuffer.byteOffset,
	              this._bodyArrayBuffer.byteOffset + this._bodyArrayBuffer.byteLength
	            )
	          )
	        } else {
	          return Promise.resolve(this._bodyArrayBuffer)
	        }
	      } else {
	        return this.blob().then(readBlobAsArrayBuffer)
	      }
	    };
	  }

	  this.text = function() {
	    var rejected = consumed(this);
	    if (rejected) {
	      return rejected
	    }

	    if (this._bodyBlob) {
	      return readBlobAsText(this._bodyBlob)
	    } else if (this._bodyArrayBuffer) {
	      return Promise.resolve(readArrayBufferAsText(this._bodyArrayBuffer))
	    } else if (this._bodyFormData) {
	      throw new Error('could not read FormData body as text')
	    } else {
	      return Promise.resolve(this._bodyText)
	    }
	  };

	  if (support.formData) {
	    this.formData = function() {
	      return this.text().then(decode$1)
	    };
	  }

	  this.json = function() {
	    return this.text().then(JSON.parse)
	  };

	  return this
	}

	// HTTP methods whose capitalization should be normalized
	var methods = ['DELETE', 'GET', 'HEAD', 'OPTIONS', 'POST', 'PUT'];

	function normalizeMethod(method) {
	  var upcased = method.toUpperCase();
	  return methods.indexOf(upcased) > -1 ? upcased : method
	}

	function Request(input, options) {
	  if (!(this instanceof Request)) {
	    throw new TypeError('Please use the "new" operator, this DOM object constructor cannot be called as a function.')
	  }

	  options = options || {};
	  var body = options.body;

	  if (input instanceof Request) {
	    if (input.bodyUsed) {
	      throw new TypeError('Already read')
	    }
	    this.url = input.url;
	    this.credentials = input.credentials;
	    if (!options.headers) {
	      this.headers = new Headers(input.headers);
	    }
	    this.method = input.method;
	    this.mode = input.mode;
	    this.signal = input.signal;
	    if (!body && input._bodyInit != null) {
	      body = input._bodyInit;
	      input.bodyUsed = true;
	    }
	  } else {
	    this.url = String(input);
	  }

	  this.credentials = options.credentials || this.credentials || 'same-origin';
	  if (options.headers || !this.headers) {
	    this.headers = new Headers(options.headers);
	  }
	  this.method = normalizeMethod(options.method || this.method || 'GET');
	  this.mode = options.mode || this.mode || null;
	  this.signal = options.signal || this.signal;
	  this.referrer = null;

	  if ((this.method === 'GET' || this.method === 'HEAD') && body) {
	    throw new TypeError('Body not allowed for GET or HEAD requests')
	  }
	  this._initBody(body);

	  if (this.method === 'GET' || this.method === 'HEAD') {
	    if (options.cache === 'no-store' || options.cache === 'no-cache') {
	      // Search for a '_' parameter in the query string
	      var reParamSearch = /([?&])_=[^&]*/;
	      if (reParamSearch.test(this.url)) {
	        // If it already exists then set the value with the current time
	        this.url = this.url.replace(reParamSearch, '$1_=' + new Date().getTime());
	      } else {
	        // Otherwise add a new '_' parameter to the end with the current time
	        var reQueryString = /\?/;
	        this.url += (reQueryString.test(this.url) ? '&' : '?') + '_=' + new Date().getTime();
	      }
	    }
	  }
	}

	Request.prototype.clone = function() {
	  return new Request(this, {body: this._bodyInit})
	};

	function decode$1(body) {
	  var form = new FormData();
	  body
	    .trim()
	    .split('&')
	    .forEach(function(bytes) {
	      if (bytes) {
	        var split = bytes.split('=');
	        var name = split.shift().replace(/\+/g, ' ');
	        var value = split.join('=').replace(/\+/g, ' ');
	        form.append(decodeURIComponent(name), decodeURIComponent(value));
	      }
	    });
	  return form
	}

	function parseHeaders(rawHeaders) {
	  var headers = new Headers();
	  // Replace instances of \r\n and \n followed by at least one space or horizontal tab with a space
	  // https://tools.ietf.org/html/rfc7230#section-3.2
	  var preProcessedHeaders = rawHeaders.replace(/\r?\n[\t ]+/g, ' ');
	  // Avoiding split via regex to work around a common IE11 bug with the core-js 3.6.0 regex polyfill
	  // https://github.com/github/fetch/issues/748
	  // https://github.com/zloirock/core-js/issues/751
	  preProcessedHeaders
	    .split('\r')
	    .map(function(header) {
	      return header.indexOf('\n') === 0 ? header.substr(1, header.length) : header
	    })
	    .forEach(function(line) {
	      var parts = line.split(':');
	      var key = parts.shift().trim();
	      if (key) {
	        var value = parts.join(':').trim();
	        headers.append(key, value);
	      }
	    });
	  return headers
	}

	Body.call(Request.prototype);

	function Response(bodyInit, options) {
	  if (!(this instanceof Response)) {
	    throw new TypeError('Please use the "new" operator, this DOM object constructor cannot be called as a function.')
	  }
	  if (!options) {
	    options = {};
	  }

	  this.type = 'default';
	  this.status = options.status === undefined ? 200 : options.status;
	  this.ok = this.status >= 200 && this.status < 300;
	  this.statusText = options.statusText === undefined ? '' : '' + options.statusText;
	  this.headers = new Headers(options.headers);
	  this.url = options.url || '';
	  this._initBody(bodyInit);
	}

	Body.call(Response.prototype);

	Response.prototype.clone = function() {
	  return new Response(this._bodyInit, {
	    status: this.status,
	    statusText: this.statusText,
	    headers: new Headers(this.headers),
	    url: this.url
	  })
	};

	Response.error = function() {
	  var response = new Response(null, {status: 0, statusText: ''});
	  response.type = 'error';
	  return response
	};

	var redirectStatuses = [301, 302, 303, 307, 308];

	Response.redirect = function(url, status) {
	  if (redirectStatuses.indexOf(status) === -1) {
	    throw new RangeError('Invalid status code')
	  }

	  return new Response(null, {status: status, headers: {location: url}})
	};

	var DOMException = global$2.DOMException;
	try {
	  new DOMException();
	} catch (err) {
	  DOMException = function(message, name) {
	    this.message = message;
	    this.name = name;
	    var error = Error(message);
	    this.stack = error.stack;
	  };
	  DOMException.prototype = Object.create(Error.prototype);
	  DOMException.prototype.constructor = DOMException;
	}

	function fetch$1(input, init) {
	  return new Promise(function(resolve, reject) {
	    var request = new Request(input, init);

	    if (request.signal && request.signal.aborted) {
	      return reject(new DOMException('Aborted', 'AbortError'))
	    }

	    var xhr = new XMLHttpRequest();

	    function abortXhr() {
	      xhr.abort();
	    }

	    xhr.onload = function() {
	      var options = {
	        status: xhr.status,
	        statusText: xhr.statusText,
	        headers: parseHeaders(xhr.getAllResponseHeaders() || '')
	      };
	      options.url = 'responseURL' in xhr ? xhr.responseURL : options.headers.get('X-Request-URL');
	      var body = 'response' in xhr ? xhr.response : xhr.responseText;
	      setTimeout(function() {
	        resolve(new Response(body, options));
	      }, 0);
	    };

	    xhr.onerror = function() {
	      setTimeout(function() {
	        reject(new TypeError('Network request failed'));
	      }, 0);
	    };

	    xhr.ontimeout = function() {
	      setTimeout(function() {
	        reject(new TypeError('Network request failed'));
	      }, 0);
	    };

	    xhr.onabort = function() {
	      setTimeout(function() {
	        reject(new DOMException('Aborted', 'AbortError'));
	      }, 0);
	    };

	    function fixUrl(url) {
	      try {
	        return url === '' && global$2.location.href ? global$2.location.href : url
	      } catch (e) {
	        return url
	      }
	    }

	    xhr.open(request.method, fixUrl(request.url), true);

	    if (request.credentials === 'include') {
	      xhr.withCredentials = true;
	    } else if (request.credentials === 'omit') {
	      xhr.withCredentials = false;
	    }

	    if ('responseType' in xhr) {
	      if (support.blob) {
	        xhr.responseType = 'blob';
	      } else if (
	        support.arrayBuffer &&
	        request.headers.get('Content-Type') &&
	        request.headers.get('Content-Type').indexOf('application/octet-stream') !== -1
	      ) {
	        xhr.responseType = 'arraybuffer';
	      }
	    }

	    if (init && typeof init.headers === 'object' && !(init.headers instanceof Headers)) {
	      Object.getOwnPropertyNames(init.headers).forEach(function(name) {
	        xhr.setRequestHeader(name, normalizeValue(init.headers[name]));
	      });
	    } else {
	      request.headers.forEach(function(value, name) {
	        xhr.setRequestHeader(name, value);
	      });
	    }

	    if (request.signal) {
	      request.signal.addEventListener('abort', abortXhr);

	      xhr.onreadystatechange = function() {
	        // DONE (success or failure)
	        if (xhr.readyState === 4) {
	          request.signal.removeEventListener('abort', abortXhr);
	        }
	      };
	    }

	    xhr.send(typeof request._bodyInit === 'undefined' ? null : request._bodyInit);
	  })
	}

	fetch$1.polyfill = true;

	if (!global$2.fetch) {
	  global$2.fetch = fetch$1;
	  global$2.Headers = Headers;
	  global$2.Request = Request;
	  global$2.Response = Response;
	}

	// https://developer.mozilla.org/en-US/docs/Web/API/Element/getAttributeNames#Polyfill
	if (Element.prototype.getAttributeNames == undefined) {
	  Element.prototype.getAttributeNames = function () {
	    var attributes = this.attributes;
	    var length = attributes.length;
	    var result = new Array(length);

	    for (var i = 0; i < length; i++) {
	      result[i] = attributes[i].name;
	    }

	    return result;
	  };
	}

	// https://developer.mozilla.org/en-US/docs/Web/API/Element/matches#Polyfill
	if (!Element.prototype.matches) {
	  Element.prototype.matches = Element.prototype.matchesSelector || Element.prototype.mozMatchesSelector || Element.prototype.msMatchesSelector || Element.prototype.oMatchesSelector || Element.prototype.webkitMatchesSelector || function (s) {
	    var matches = (this.document || this.ownerDocument).querySelectorAll(s),
	        i = matches.length;

	    while (--i >= 0 && matches.item(i) !== this) {//do nothing
	    }

	    return i > -1;
	  };
	}

	// https://developer.mozilla.org/en-US/docs/Web/API/Element/closest#Polyfill
	if (!Element.prototype.matches) {
	  Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
	}

	if (!Element.prototype.closest) {
	  Element.prototype.closest = function (s) {
	    var el = this;

	    do {
	      if (el.matches(s)) {
	        return el;
	      }

	      el = el.parentElement || el.parentNode;
	    } while (el !== null && el.nodeType === 1);

	    return null;
	  };
	}

	if (!(typeof window.Element === 'undefined' || 'classList' in document.documentElement)) {
	  var prototype = Array.prototype,
	      push = prototype.push,
	      splice = prototype.splice,
	      join$1 = prototype.join;

	  var DOMTokenList = function DOMTokenList(el) {
	    this.el = el; // The className needs to be trimmed and split on whitespace
	    // to retrieve a list of classes.

	    var classes = el.className.replace(/^\s+|\s+$/g, '').split(/\s+/);

	    for (var i = 0; i < classes.length; i++) {
	      push.call(this, classes[i]);
	    }
	  };

	  DOMTokenList.prototype = {
	    add: function add(token) {
	      if (this.contains(token)) {
	        return;
	      }

	      push.call(this, token);
	      this.el.className = this.toString();
	    },
	    contains: function contains(token) {
	      return this.el.className.indexOf(token) != -1;
	    },
	    item: function item(index) {
	      return this[index] || null;
	    },
	    remove: function remove(token) {
	      if (!this.contains(token)) {
	        return;
	      }

	      var i;

	      for (i = 0; i < this.length; i++) {
	        if (this[i] == token) {
	          break;
	        }
	      }

	      splice.call(this, i, 1);
	      this.el.className = this.toString();
	    },
	    toString: function toString() {
	      return join$1.call(this, ' ');
	    },
	    toggle: function toggle(token) {
	      if (!this.contains(token)) {
	        this.add(token);
	      } else {
	        this.remove(token);
	      }

	      return this.contains(token);
	    }
	  };
	  window.DOMTokenList = DOMTokenList;

	  var defineElementGetter = function defineElementGetter(obj, prop, getter) {
	    if (Object.defineProperty) {
	      Object.defineProperty(obj, prop, {
	        get: getter
	      });
	    } else {
	      // eslint-disable-next-line no-underscore-dangle
	      obj.__defineGetter__(prop, getter);
	    }
	  };

	  defineElementGetter(Element.prototype, 'classList', function () {
	    return new DOMTokenList(this);
	  });
	}

	function _typeof$1(obj) {
	  "@babel/helpers - typeof";

	  if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
	    _typeof$1 = function (obj) {
	      return typeof obj;
	    };
	  } else {
	    _typeof$1 = function (obj) {
	      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
	    };
	  }

	  return _typeof$1(obj);
	}

	function _classCallCheck(instance, Constructor) {
	  if (!(instance instanceof Constructor)) {
	    throw new TypeError("Cannot call a class as a function");
	  }
	}

	function _defineProperties(target, props) {
	  for (var i = 0; i < props.length; i++) {
	    var descriptor = props[i];
	    descriptor.enumerable = descriptor.enumerable || false;
	    descriptor.configurable = true;
	    if ("value" in descriptor) descriptor.writable = true;
	    Object.defineProperty(target, descriptor.key, descriptor);
	  }
	}

	function _createClass(Constructor, protoProps, staticProps) {
	  if (protoProps) _defineProperties(Constructor.prototype, protoProps);
	  if (staticProps) _defineProperties(Constructor, staticProps);
	  return Constructor;
	}

	function _inherits(subClass, superClass) {
	  if (typeof superClass !== "function" && superClass !== null) {
	    throw new TypeError("Super expression must either be null or a function");
	  }

	  subClass.prototype = Object.create(superClass && superClass.prototype, {
	    constructor: {
	      value: subClass,
	      writable: true,
	      configurable: true
	    }
	  });
	  if (superClass) _setPrototypeOf(subClass, superClass);
	}

	function _getPrototypeOf(o) {
	  _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) {
	    return o.__proto__ || Object.getPrototypeOf(o);
	  };
	  return _getPrototypeOf(o);
	}

	function _setPrototypeOf(o, p) {
	  _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
	    o.__proto__ = p;
	    return o;
	  };

	  return _setPrototypeOf(o, p);
	}

	function _isNativeReflectConstruct() {
	  if (typeof Reflect === "undefined" || !Reflect.construct) return false;
	  if (Reflect.construct.sham) return false;
	  if (typeof Proxy === "function") return true;

	  try {
	    Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {}));
	    return true;
	  } catch (e) {
	    return false;
	  }
	}

	function _assertThisInitialized(self) {
	  if (self === void 0) {
	    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
	  }

	  return self;
	}

	function _possibleConstructorReturn(self, call) {
	  if (call && (typeof call === "object" || typeof call === "function")) {
	    return call;
	  } else if (call !== void 0) {
	    throw new TypeError("Derived constructors may only return object or undefined");
	  }

	  return _assertThisInitialized(self);
	}

	function _createSuper(Derived) {
	  var hasNativeReflectConstruct = _isNativeReflectConstruct();

	  return function _createSuperInternal() {
	    var Super = _getPrototypeOf(Derived),
	        result;

	    if (hasNativeReflectConstruct) {
	      var NewTarget = _getPrototypeOf(this).constructor;

	      result = Reflect.construct(Super, arguments, NewTarget);
	    } else {
	      result = Super.apply(this, arguments);
	    }

	    return _possibleConstructorReturn(this, result);
	  };
	}

	function _slicedToArray(arr, i) {
	  return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest();
	}

	function _toArray(arr) {
	  return _arrayWithHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableRest();
	}

	function _toConsumableArray(arr) {
	  return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread();
	}

	function _arrayWithoutHoles(arr) {
	  if (Array.isArray(arr)) return _arrayLikeToArray(arr);
	}

	function _arrayWithHoles(arr) {
	  if (Array.isArray(arr)) return arr;
	}

	function _iterableToArray(iter) {
	  if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter);
	}

	function _iterableToArrayLimit(arr, i) {
	  var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"];

	  if (_i == null) return;
	  var _arr = [];
	  var _n = true;
	  var _d = false;

	  var _s, _e;

	  try {
	    for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) {
	      _arr.push(_s.value);

	      if (i && _arr.length === i) break;
	    }
	  } catch (err) {
	    _d = true;
	    _e = err;
	  } finally {
	    try {
	      if (!_n && _i["return"] != null) _i["return"]();
	    } finally {
	      if (_d) throw _e;
	    }
	  }

	  return _arr;
	}

	function _unsupportedIterableToArray(o, minLen) {
	  if (!o) return;
	  if (typeof o === "string") return _arrayLikeToArray(o, minLen);
	  var n = Object.prototype.toString.call(o).slice(8, -1);
	  if (n === "Object" && o.constructor) n = o.constructor.name;
	  if (n === "Map" || n === "Set") return Array.from(o);
	  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
	}

	function _arrayLikeToArray(arr, len) {
	  if (len == null || len > arr.length) len = arr.length;

	  for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i];

	  return arr2;
	}

	function _nonIterableSpread() {
	  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
	}

	function _nonIterableRest() {
	  throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
	}

	var Url = /*#__PURE__*/function () {
	  function Url() {
	    _classCallCheck(this, Url);
	  }

	  _createClass(Url, [{
	    key: "addQueryString",
	    value: function addQueryString(url, key, value) {
	      key = encodeURI(key);
	      value = encodeURI(value);
	      var urlArray = url.split('?');
	      var queryString = '';
	      var baseUrl = urlArray[0];

	      if (urlArray.length > 1) {
	        queryString = urlArray[1];
	      }

	      var kvp = queryString.split('&');
	      var i = kvp.length;
	      var x;

	      while (i--) {
	        x = kvp[i].split('=');

	        if (x[0] === key) {
	          x[1] = value;
	          kvp[i] = x.join('=');
	          break;
	        }
	      }

	      if (i < 0) {
	        kvp[kvp.length] = [key, value].join('=');
	      }

	      queryString = kvp.join('&');

	      if (queryString.substr(0, 1) === '&') {
	        queryString = queryString.substr(1);
	      }

	      return baseUrl + '?' + queryString;
	    }
	  }, {
	    key: "replaceParam",
	    value: function replaceParam(url) {
	      var available = true;

	      while (available) {
	        var matches = url.match(/{([\w]*)}/);

	        if (matches !== null) {
	          var key = matches[1];
	          var val = null;

	          if ($('#' + key).length > 0) {
	            val = window.cresenity.value('#' + key);
	          }

	          if (val === null) {
	            val = key;
	          }

	          url = url.replace('{' + key + '}', val);
	        } else {
	          available = false;
	        }
	      }

	      return url;
	    }
	  }]);

	  return Url;
	}();

	function debounce$1(func, wait, immediate) {
	  var timeout;
	  return function () {
	    var context = this,
	        args = arguments;

	    var later = function later() {
	      timeout = null;

	      if (!immediate) {
	        func.apply(context, args);
	      }
	    };

	    var callNow = immediate && !timeout;
	    clearTimeout(timeout);
	    timeout = setTimeout(later, wait);

	    if (callNow) {
	      func.apply(context, args);
	    }
	  };
	}

	function cresDirectives(el) {
	  return new DirectiveManager$1(el);
	}

	var DirectiveManager$1 = /*#__PURE__*/function () {
	  function DirectiveManager(el) {
	    _classCallCheck(this, DirectiveManager);

	    this.el = el;
	    this.directives = this.extractTypeModifiersAndValue();
	  }

	  _createClass(DirectiveManager, [{
	    key: "all",
	    value: function all() {
	      return this.directives;
	    }
	  }, {
	    key: "has",
	    value: function has(type) {
	      return this.directives.map(function (directive) {
	        return directive.type;
	      }).includes(type);
	    }
	  }, {
	    key: "missing",
	    value: function missing(type) {
	      return !this.has(type);
	    }
	  }, {
	    key: "get",
	    value: function get(type) {
	      return this.directives.find(function (directive) {
	        return directive.type === type;
	      });
	    }
	  }, {
	    key: "extractTypeModifiersAndValue",
	    value: function extractTypeModifiersAndValue() {
	      var _this = this;

	      return Array.from(this.el.getAttributeNames() // Filter only the cresenity directives.
	      .filter(function (name) {
	        return name.match(new RegExp('cres:'));
	      }) // Parse out the type, modifiers, and value from it.
	      .map(function (name) {
	        var _name$replace$split = name.replace(new RegExp('cres:'), '').split('.'),
	            _name$replace$split2 = _toArray(_name$replace$split),
	            type = _name$replace$split2[0],
	            modifiers = _name$replace$split2.slice(1);

	        return new Directive(type, modifiers, name, _this.el);
	      }));
	    }
	  }]);

	  return DirectiveManager;
	}();

	var Directive = /*#__PURE__*/function () {
	  function Directive(type, modifiers, rawName, el) {
	    _classCallCheck(this, Directive);

	    this.type = type;
	    this.modifiers = modifiers;
	    this.rawName = rawName;
	    this.el = el;
	    this.eventContext;
	  }

	  _createClass(Directive, [{
	    key: "setEventContext",
	    value: function setEventContext(context) {
	      this.eventContext = context;
	    }
	  }, {
	    key: "value",
	    get: function get() {
	      return this.el.getAttribute(this.rawName);
	    }
	  }, {
	    key: "method",
	    get: function get() {
	      var _this$parseOutMethodA = this.parseOutMethodAndParams(this.value),
	          method = _this$parseOutMethodA.method;

	      return method;
	    }
	  }, {
	    key: "params",
	    get: function get() {
	      var _this$parseOutMethodA2 = this.parseOutMethodAndParams(this.value),
	          params = _this$parseOutMethodA2.params;

	      return params;
	    }
	  }, {
	    key: "durationOr",
	    value: function durationOr(defaultDuration) {
	      var durationInMilliSeconds;
	      var durationInMilliSecondsString = this.modifiers.find(function (mod) {
	        return mod.match(/([0-9]+)ms/);
	      });
	      var durationInSecondsString = this.modifiers.find(function (mod) {
	        return mod.match(/([0-9]+)s/);
	      });

	      if (durationInMilliSecondsString) {
	        durationInMilliSeconds = Number(durationInMilliSecondsString.replace('ms', ''));
	      } else if (durationInSecondsString) {
	        durationInMilliSeconds = Number(durationInSecondsString.replace('s', '')) * 1000;
	      }

	      return durationInMilliSeconds || defaultDuration;
	    }
	  }, {
	    key: "parseOutMethodAndParams",
	    value: function parseOutMethodAndParams(rawMethod) {
	      var method = rawMethod;
	      var params = [];
	      var methodAndParamString = method.match(/(.*?)\((.*)\)/);

	      if (methodAndParamString) {
	        // This "$event" is for use inside the cresenity event handler.
	        this.eventContext;
	        method = methodAndParamString[1]; // use a function that returns it's arguments to parse and eval all params

	        /* no-warn=eval */

	        params = eval("(function () {\n                for (var l=arguments.length, p=new Array(l), k=0; k<l; k++) {\n                    p[k] = arguments[k];\n                }\n                return [].concat(p);\n            })(".concat(methodAndParamString[2], ")"));
	        /* warn=eval */
	      }

	      return {
	        method: method,
	        params: params
	      };
	    }
	  }, {
	    key: "cardinalDirectionOr",
	    value: function cardinalDirectionOr() {
	      var fallback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'right';

	      if (this.modifiers.includes('up')) {
	        return 'up';
	      }

	      if (this.modifiers.includes('down')) {
	        return 'down';
	      }

	      if (this.modifiers.includes('left')) {
	        return 'left';
	      }

	      if (this.modifiers.includes('right')) {
	        return 'right';
	      }

	      return fallback;
	    }
	  }]);

	  return Directive;
	}();

	// A little DOM-tree walker.
	// (TreeWalker won't do because I need to conditionaly ignore sub-trees using the callback)
	function walk$1(root, callback) {
	  if (callback(root) === false) {
	    return;
	  }

	  var node = root.firstElementChild;

	  while (node) {
	    walk$1(node, callback);
	    node = node.nextElementSibling;
	  }
	}

	function dispatch$1(eventName) {
	  var event = document.createEvent('Events');
	  event.initEvent(eventName, true, true);
	  document.dispatchEvent(event);
	  return event;
	}

	function getCsrfToken() {
	  var tokenTag = document.head.querySelector('meta[name="csrf-token"]');
	  var token;

	  if (!tokenTag) {

	    token = window.cresenity_token;
	  } else {
	    token = tokenTag.content;
	  }

	  return token;
	}

	var isJson = function isJson(str) {
	  try {
	    JSON.parse(str);
	  } catch (e) {
	    return false;
	  }

	  return true;
	};

	var toggleFullscreen = function toggleFullscreen(element) {
	  if (!element) {
	    element = document.documentElement;
	  }

	  if (!$('body').hasClass('full-screen')) {
	    $('body').addClass('full-screen');

	    if (element.requestFullscreen) {
	      element.requestFullscreen();
	    } else if (element.mozRequestFullScreen) {
	      element.mozRequestFullScreen();
	    } else if (element.webkitRequestFullscreen) {
	      element.webkitRequestFullscreen();
	    } else if (element.msRequestFullscreen) {
	      element.msRequestFullscreen();
	    }
	  } else {
	    $('body').removeClass('full-screen');

	    if (document.exitFullscreen) {
	      document.exitFullscreen();
	    } else if (document.mozCancelFullScreen) {
	      document.mozCancelFullScreen();
	    } else if (document.webkitExitFullscreen) {
	      document.webkitExitFullscreen();
	    }
	  }
	};

	var initIframeModal = function initIframeModal() {
	  var modal = document.getElementById('capp-html-modal');

	  if (typeof modal != 'undefined' && modal != null) {
	    // Modal already exists.
	    modal.innerHTML = '';
	  } else {
	    var _iframe = document.createElement('iframe');

	    _iframe.style.backgroundColor = '#17161A';
	    _iframe.style.borderRadius = '5px';
	    _iframe.style.width = '100%';
	    _iframe.style.height = '100%';
	    modal = document.createElement('div');
	    modal.id = 'capp-html-modal';
	    modal.style.position = 'fixed';
	    modal.style.width = '100vw';
	    modal.style.height = '100vh';
	    modal.style.padding = '50px';
	    modal.style.backgroundColor = 'rgba(0, 0, 0, .6)';
	    modal.style.zIndex = 200000; // Close on click.

	    modal.addEventListener('click', function () {
	      return hideIframeModal(modal);
	    }); // Close on escape key press.

	    modal.setAttribute('tabindex', 0);
	    modal.addEventListener('keydown', function (e) {
	      if (e.key === 'Escape') {
	        hideIframeModal(modal);
	      }
	    });
	    document.body.prepend(modal);
	    modal.appendChild(_iframe);
	  }

	  var iframe = modal.firstChild;
	  iframe.contentWindow.document.innerHTML = '';
	  document.body.style.overflow = 'hidden';
	  modal.focus();
	  return modal;
	};

	var showHtmlModal = function showHtmlModal(html) {

	  if (isJson(html)) {
	    html = JSON.parse(html);
	  }

	  if (_typeof$1(html) === 'object') {
	    html = JSON.stringify(html, null, 4);
	    html = '<pre style="background-color:#fff">' + html + '</pre>';
	  }

	  var page = document.createElement('html');
	  page.innerHTML = html;
	  page.querySelectorAll('a').forEach(function (a) {
	    return a.setAttribute('target', '_top');
	  });
	  var modal = initIframeModal();
	  var iframe = modal.firstChild;
	  iframe.contentWindow.document.open();
	  iframe.contentWindow.document.write(page.outerHTML);
	  iframe.contentWindow.document.close();
	  return modal;
	};
	var showUrlModal = function showUrlModal(url) {
	  var modal = initIframeModal();
	  var iframe = modal.firstChild;
	  iframe.src = url;
	  return modal;
	};
	var hideIframeModal = function hideIframeModal(modal) {
	  if (typeof modal == 'undefined') {
	    modal = document.getElementById('capp-html-modal');
	  }

	  if (typeof modal != 'undefined') {
	    modal.outerHTML = '';
	    document.body.style.overflow = 'visible';
	  }
	};

	function kebabCase$1(subject) {
	  return subject.replace(/([a-z])([A-Z])/g, '$1-$2').replace(/[_\s]/, '-').toLowerCase();
	}

	/**
	* Merge the DEFAULT_SETTINGS with the user defined options if specified
	* @param {Object} options The user defined options
	*/
	var mergeOptions$1 = function mergeOptions(initialOptions, customOptions) {
	  var merged = customOptions;

	  for (var prop in initialOptions) {
	    if (merged.hasOwnProperty(prop)) {
	      if (initialOptions[prop] !== null && initialOptions[prop].constructor === Object) {
	        merged[prop] = mergeOptions(initialOptions[prop], merged[prop]);
	      }
	    } else {
	      merged[prop] = initialOptions[prop];
	    }
	  }

	  return merged;
	};

	var CF = /*#__PURE__*/function () {
	  function CF() {
	    var _this = this;

	    _classCallCheck(this, CF);

	    this.required = typeof this.required === 'undefined' ? [] : this.required;
	    this.cssRequired = typeof this.cssRequired === 'undefined' ? [] : this.cssRequired;
	    this.window = window;
	    this.document = window.document;
	    this.head = this.document.getElementsByTagName('head')[0];
	    this.beforeInitCallback = [];
	    this.afterInitCallback = [];
	    var cappConfig = window.capp;

	    if (typeof cappConfig == 'undefined') {
	      cappConfig = {};
	    }

	    var defaultConfig = {
	      baseUrl: '/',
	      defaultJQueryUrl: 'https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js',
	      haveScrollToTop: false,
	      vscode: {
	        liveReload: {
	          enable: false,
	          protocol: 'ws',
	          host: 'localhost',
	          port: 3717
	        }
	      },
	      requireJs: false,
	      CFVersion: '1.2',
	      isProduction: false,
	      react: {
	        enable: false
	      }
	    };
	    this.config = mergeOptions$1(defaultConfig, cappConfig);

	    if (this.config.cssUrl) {
	      this.config.cssUrl.forEach(function (item) {
	        _this.required.push(item);
	      });
	    }

	    if (this.config.jsUrl) {
	      this.config.jsUrl.forEach(function (item) {
	        _this.required.push(item);
	      });
	    }
	  }

	  _createClass(CF, [{
	    key: "debug",
	    value: function debug(msg) {
	      if (this.getConfig().debug) {
	        window.console.log(msg);
	      }
	    }
	  }, {
	    key: "onBeforeInit",
	    value: function onBeforeInit(callback) {
	      this.beforeInitCallback.push(callback);
	      return this;
	    }
	  }, {
	    key: "onAfterInit",
	    value: function onAfterInit(callback) {
	      this.afterInitCallback.push(callback);
	      return this;
	    }
	  }, {
	    key: "getConfig",
	    value: function getConfig() {
	      return this.config;
	    }
	  }, {
	    key: "isUseRequireJs",
	    value: function isUseRequireJs() {
	      return this.getConfig().requireJs;
	    }
	  }, {
	    key: "CFVersion",
	    value: function CFVersion() {
	      return this.getConfig().CFVersion;
	    }
	  }, {
	    key: "requireCss",
	    value: function requireCss(url, callback) {
	      var _this2 = this;

	      if (!~this.cssRequired.indexOf(url)) {
	        this.cssRequired.push(url);

	        if (document.querySelector('link[href="' + url + '"],script[src="' + url + '"]') !== null) {
	          return;
	        }

	        var string = '<link rel=\'stylesheet\' type=\'text/css\' href=\'' + url + '\' />';

	        if (document.readyState === 'loading'
	        /* || mwd.readyState === 'interactive'*/
	        && !!window.CanvasRenderingContext2D && self === parent) {
	          document.write(string);
	        } else {
	          var el;
	          el = this.document.createElement('link');
	          el.rel = 'stylesheet';
	          el.type = 'text/css';
	          el.href = url; // IE 6 & 7

	          if (typeof callback === 'function') {
	            el.onload = callback;

	            el.onreadystatechange = function () {
	              if (_this2.readyState == 'complete') {
	                callback();
	              }
	            };
	          }

	          this.head.appendChild(el);
	        }
	      } else if (typeof callback === 'function') {
	        callback();
	      }
	    }
	  }, {
	    key: "requireJs",
	    value: function requireJs(url, callback) {
	      var _this3 = this;

	      if (!~this.required.indexOf(url)) {
	        this.required.push(url);

	        if (document.querySelector('link[href="' + url + '"],script[src="' + url + '"]') !== null) {
	          return;
	        }

	        var string = '<script type=\'text/javascript\'  src=\'' + url + '\'></script>';

	        if (document.readyState === 'loading'
	        /* || mwd.readyState === 'interactive'*/
	        && !!window.CanvasRenderingContext2D && self === parent) {
	          document.write(string);
	        } else {
	          var el;
	          el = this.document.createElement('script');
	          el.src = url;
	          el.setAttribute('type', 'text/javascript'); // IE 6 & 7

	          if (typeof callback === 'function') {
	            el.onload = callback;

	            el.onreadystatechange = function () {
	              if (_this3.readyState == 'complete') {
	                callback();
	              }
	            };
	          }

	          this.document.body.appendChild(el);
	        }
	      } else if (typeof callback === 'function') {
	        callback();
	      }
	    }
	  }, {
	    key: "require",
	    value: function require(url, callback) {
	      if (typeof url != 'string') {
	        url = url[0];
	      }

	      if (!url) {
	        return;
	      }

	      var toPush = url.trim();
	      var t = 'js';
	      var urlObject = new URL(toPush, document.baseURI);

	      if (urlObject) {
	        t = urlObject.pathname.split('.').pop();
	      }

	      if (t == 'js') {
	        this.requireJs(toPush, callback);
	      }

	      if (t == 'css') {
	        this.requireCss(toPush, callback);
	      }
	    }
	  }, {
	    key: "isProduction",
	    value: function isProduction() {
	      return this.config.environment == 'production';
	    }
	  }, {
	    key: "loadReact",
	    value: function loadReact(callback) {
	      var _this4 = this;

	      var afterReactLoaded = function afterReactLoaded() {
	        dispatch$1('cresenity:react:loaded');
	        callback();
	      };

	      var reactDevelopmentUrl = 'https://unpkg.com/react@17/umd/react.development.js';
	      var reactDevelopmentDomUrl = 'https://unpkg.com/react-dom@17/umd/react-dom.development.js';
	      var reactProductionUrl = 'https://unpkg.com/react@17/umd/react.production.min.js';
	      var reactProductionDomUrl = 'https://unpkg.com/react-dom@17/umd/react-dom.production.min.js';
	      var reactUrl = this.getConfig().isProduction ? reactProductionUrl : reactDevelopmentUrl;
	      var reactDomUrl = this.getConfig().isProduction ? reactProductionDomUrl : reactDevelopmentDomUrl;

	      var loadReactDom = function loadReactDom() {
	        var fileref = _this4.document.createElement('script');

	        fileref.setAttribute('type', 'text/javascript');
	        fileref.setAttribute('src', reactDomUrl); // IE 6 & 7

	        if (typeof callback === 'function') {
	          fileref.onload = function () {
	            afterReactLoaded();
	          };
	        }

	        _this4.head.appendChild(fileref);
	      };

	      var loadReactBase = function loadReactBase() {
	        var fileref = _this4.document.createElement('script');

	        fileref.setAttribute('type', 'text/javascript');
	        fileref.setAttribute('src', reactUrl); // IE 6 & 7

	        if (typeof callback === 'function') {
	          fileref.onload = function () {
	            loadReactDom();
	          };
	        }

	        _this4.head.appendChild(fileref);
	      };

	      if (typeof React == 'undefined') {
	        loadReactBase();
	      } else {
	        afterReactLoaded();
	      }
	    }
	  }, {
	    key: "loadJQuery",
	    value: function loadJQuery(callback) {
	      var _this5 = this;

	      var jqueryUrl = this.getConfig().defaultJQueryUrl;

	      var afterJQueryLoaded = function afterJQueryLoaded() {
	        _this5.required.push(jqueryUrl);

	        dispatch$1('cresenity:jquery:loaded');
	        callback();
	      };

	      if (typeof jQuery == 'undefined') {
	        var fileref = this.document.createElement('script');
	        fileref.setAttribute('type', 'text/javascript');
	        fileref.setAttribute('src', jqueryUrl); // IE 6 & 7

	        if (typeof callback === 'function') {
	          fileref.onload = function () {
	            afterJQueryLoaded();
	          }; // fileref.onreadystatechange = () => {
	          //     if (fileref.readyState == 'complete') {
	          //         afterJQueryLoaded();
	          //     }
	          // };

	        }

	        this.head.appendChild(fileref);
	      } else {
	        afterJQueryLoaded();
	      }
	    }
	  }, {
	    key: "init",
	    value: function init() {
	      var _this6 = this;

	      this.beforeInitCallback.forEach(function (item) {
	        item();
	      }); //push all item already loaded by html in capp

	      var arrayJsUrl = this.getConfig().jsUrl;
	      var arrayCssUrl = this.getConfig().cssUrl;

	      if (typeof arrayJsUrl !== 'undefined') {
	        arrayJsUrl.forEach(function (item) {
	          _this6.required.push(item);
	        });
	      }

	      if (typeof arrayCssUrl !== 'undefined') {
	        arrayCssUrl.forEach(function (item) {
	          _this6.cssRequired.push(item);
	        });
	      }

	      var resolver = this.getConfig().react.enable ? function (callback) {
	        _this6.loadJQuery(_this6.loadReact(callback));
	      } : function (callback) {
	        _this6.loadJQuery(callback);
	      };
	      resolver(function () {
	        _this6.afterInitCallback.forEach(function (item) {
	          item();
	        });
	      });
	    }
	  }]);

	  return CF;
	}();

	var cf = new CF();

	var ScrollToTop = /*#__PURE__*/function () {
	  //startline: Integer. Number of pixels from top of doc scrollbar is scrolled before showing control
	  //scrollto: Keyword (Integer, or "Scroll_to_Element_ID"). How far to scroll document up when control is clicked on (0=top).
	  function ScrollToTop() {
	    _classCallCheck(this, ScrollToTop);

	    this.setting = {
	      startline: 100,
	      scrollto: 0,
	      scrollduration: 1000,
	      fadeduration: [500, 100]
	    }; //HTML for control, which is auto wrapped in DIV w/ ID="topcontrol"

	    this.controlHTML = cf.config.scrollToTopHtml || '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADMAAAAqCAYAAAAeeGN5AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjM2RTVENEJCODY3RTExRTI5MTFEQzg2NjQyQ0VGQzhDIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjM2RTVENEJDODY3RTExRTI5MTFEQzg2NjQyQ0VGQzhDIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6MzZFNUQ0Qjk4NjdFMTFFMjkxMURDODY2NDJDRUZDOEMiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6MzZFNUQ0QkE4NjdFMTFFMjkxMURDODY2NDJDRUZDOEMiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz6Mw5vNAAAAkUlEQVR42uzYoRGAMBBEUbKKlqAKKAKKogm6oCWQTAQawd0lE/5Xa5/dNEzL2TWSuoYCAwYMGDBgwIAB8zPMsW99E5gMGef1igApApJ3BEgRkCdvkKIgESBFQrxBioZ4glQC4gVSKYgHSCUh1iCVhliCVAPECqRaIBYg1QT5Ckp8zWDAgAEDBgwYMGDAvHQLMACw9mxL+kYUJQAAAABJRU5ErkJggg==" style="width:51px; height:42px" />'; //offset of control relative to right/ bottom of window corner

	    this.controlattrs = {
	      offsetx: 5,
	      offsety: 5
	    }; //Enter href value of HTML anchors on the page that should also act as "Scroll Up" links

	    this.anchorkeyword = '#top';
	    this.state = {
	      isvisible: false,
	      shouldvisible: false
	    };

	    this.keepfixed = function () {
	      var $window = jQuery(window);
	      var controlx = $window.scrollLeft() + $window.width() - this.$control.width() - this.controlattrs.offsetx;
	      var controly = $window.scrollTop() + $window.height() - this.$control.height() - this.controlattrs.offsety;
	      this.$control.css({
	        left: controlx + 'px',
	        top: controly + 'px'
	      });
	    };

	    this.togglecontrol = function () {
	      var scrolltop = jQuery(window).scrollTop();

	      if (!this.cssfixedsupport) {
	        this.keepfixed();
	      }

	      this.state.shouldvisible = scrolltop >= this.setting.startline ? true : false;

	      if (this.state.shouldvisible && !this.state.isvisible) {
	        this.$control.stop().animate({
	          opacity: 1,
	          zIndex: 99999
	        }, this.setting.fadeduration[0]);
	        this.state.isvisible = true;
	      } else if (this.state.shouldvisible === false && this.state.isvisible) {
	        this.$control.stop().animate({
	          opacity: 0,
	          zIndex: -1
	        }, this.setting.fadeduration[1]);
	        this.state.isvisible = false;
	      }
	    };

	    this.init = function () {
	      var _this = this;

	      jQuery(document).ready(function ($) {
	        var mainobj = _this;
	        var iebrws = document.all;
	        mainobj.cssfixedsupport = !iebrws || iebrws && document.compatMode === 'CSS1Compat' && window.XMLHttpRequest; //not IE or IE7+ browsers in standards mode

	        mainobj.$body = window.opera ? document.compatMode === 'CSS1Compat' ? $('html') : $('body') : $('html,body');
	        mainobj.$control = $('<div id="cres-topcontrol">' + mainobj.controlHTML + '</div>').css({
	          position: mainobj.cssfixedsupport ? 'fixed' : 'absolute',
	          bottom: mainobj.controlattrs.offsety,
	          right: mainobj.controlattrs.offsetx,
	          opacity: 0,
	          cursor: 'pointer',
	          zIndex: 99999
	        }).attr({
	          title: 'Scroll Back to Top'
	        }).click(function () {
	          mainobj.scrollup();
	          return false;
	        }).appendTo('body'); //loose check for IE6 and below, plus whether control contains any text

	        if (document.all && !window.XMLHttpRequest && mainobj.$control.text() !== '') {
	          //IE6- seems to require an explicit width on a DIV containing text
	          mainobj.$control.css({
	            width: mainobj.$control.width()
	          });
	        }

	        mainobj.togglecontrol();
	        $('a[href="' + mainobj.anchorkeyword + '"]').click(function () {
	          mainobj.scrollup();
	          return false;
	        });
	        $(window).bind('scroll resize', function () {
	          mainobj.togglecontrol();
	        });
	      });
	    };
	  }

	  _createClass(ScrollToTop, [{
	    key: "scrollup",
	    value: function scrollup() {
	      if (!this.cssfixedsupport) {
	        //if control is positioned using JavaScript
	        this.$control.css({
	          opacity: 0,
	          zIndex: -1
	        });
	      } //hide control immediately after clicking it


	      var dest = isNaN(this.setting.scrollto) ? this.setting.scrollto : parseInt(this.setting.scrollto);

	      if (typeof dest === 'string' && jQuery('#' + dest).length == 1) {
	        //check element set by string exists
	        dest = jQuery('#' + dest).offset().top;
	      } else {
	        dest = 0;
	      }

	      this.$body.animate({
	        scrollTop: dest
	      }, this.setting.scrollduration);
	    }
	  }]);

	  return ScrollToTop;
	}();

	/*!
	 * isobject <https://github.com/jonschlinkert/isobject>
	 *
	 * Copyright (c) 2014-2017, Jon Schlinkert.
	 * Released under the MIT License.
	 */

	var isobject = function isObject(val) {
	  return val != null && typeof val === 'object' && Array.isArray(val) === false;
	};

	/*!
	 * get-value <https://github.com/jonschlinkert/get-value>
	 *
	 * Copyright (c) 2014-2018, Jon Schlinkert.
	 * Released under the MIT License.
	 */

	const isObject = isobject;

	var getValue = function(target, path, options) {
	  if (!isObject(options)) {
	    options = { default: options };
	  }

	  if (!isValidObject(target)) {
	    return typeof options.default !== 'undefined' ? options.default : target;
	  }

	  if (typeof path === 'number') {
	    path = String(path);
	  }

	  const isArray = Array.isArray(path);
	  const isString = typeof path === 'string';
	  const splitChar = options.separator || '.';
	  const joinChar = options.joinChar || (typeof splitChar === 'string' ? splitChar : '.');

	  if (!isString && !isArray) {
	    return target;
	  }

	  if (isString && path in target) {
	    return isValid(path, target, options) ? target[path] : options.default;
	  }

	  let segs = isArray ? path : split(path, splitChar, options);
	  let len = segs.length;
	  let idx = 0;

	  do {
	    let prop = segs[idx];
	    if (typeof prop === 'number') {
	      prop = String(prop);
	    }

	    while (prop && prop.slice(-1) === '\\') {
	      prop = join([prop.slice(0, -1), segs[++idx] || ''], joinChar, options);
	    }

	    if (prop in target) {
	      if (!isValid(prop, target, options)) {
	        return options.default;
	      }

	      target = target[prop];
	    } else {
	      let hasProp = false;
	      let n = idx + 1;

	      while (n < len) {
	        prop = join([prop, segs[n++]], joinChar, options);

	        if ((hasProp = prop in target)) {
	          if (!isValid(prop, target, options)) {
	            return options.default;
	          }

	          target = target[prop];
	          idx = n - 1;
	          break;
	        }
	      }

	      if (!hasProp) {
	        return options.default;
	      }
	    }
	  } while (++idx < len && isValidObject(target));

	  if (idx === len) {
	    return target;
	  }

	  return options.default;
	};

	function join(segs, joinChar, options) {
	  if (typeof options.join === 'function') {
	    return options.join(segs);
	  }
	  return segs[0] + joinChar + segs[1];
	}

	function split(path, splitChar, options) {
	  if (typeof options.split === 'function') {
	    return options.split(path);
	  }
	  return path.split(splitChar);
	}

	function isValid(key, target, options) {
	  if (typeof options.isValid === 'function') {
	    return options.isValid(key, target);
	  }
	  return true;
	}

	function isValidObject(val) {
	  return isObject(val) || Array.isArray(val) || typeof val === 'function';
	}

	var _default$6 = /*#__PURE__*/function () {
	  function _default(el) {
	    var skipWatcher = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

	    _classCallCheck(this, _default);

	    this.el = el;
	    this.skipWatcher = skipWatcher;

	    this.resolveCallback = function () {};

	    this.rejectCallback = function () {};
	  }

	  _createClass(_default, [{
	    key: "toId",
	    value: function toId() {
	      return btoa(encodeURIComponent(this.el.outerHTML));
	    }
	  }, {
	    key: "onResolve",
	    value: function onResolve(callback) {
	      this.resolveCallback = callback;
	    }
	  }, {
	    key: "onReject",
	    value: function onReject(callback) {
	      this.rejectCallback = callback;
	    }
	  }, {
	    key: "resolve",
	    value: function resolve(thing) {
	      this.resolveCallback(thing);
	    }
	  }, {
	    key: "reject",
	    value: function reject(thing) {
	      this.rejectCallback(thing);
	    }
	  }]);

	  return _default;
	}();

	var _default$5 = /*#__PURE__*/function (_Action) {
	  _inherits(_default, _Action);

	  var _super = _createSuper(_default);

	  function _default(event, params, el) {
	    var _this;

	    _classCallCheck(this, _default);

	    _this = _super.call(this, el);
	    _this.type = 'fireEvent';
	    _this.payload = {
	      event: event,
	      params: params
	    };
	    return _this;
	  } // Overriding toId() becuase some EventActions don't have an "el"


	  _createClass(_default, [{
	    key: "toId",
	    value: function toId() {
	      return btoa(encodeURIComponent(this.type, this.payload.event, JSON.stringify(this.payload.params)));
	    }
	  }]);

	  return _default;
	}(_default$6);

	var MessageBus = /*#__PURE__*/function () {
	  function MessageBus() {
	    _classCallCheck(this, MessageBus);

	    this.listeners = {};
	  }

	  _createClass(MessageBus, [{
	    key: "register",
	    value: function register(name, callback) {
	      if (!this.listeners[name]) {
	        this.listeners[name] = [];
	      }

	      this.listeners[name].push(callback);
	    }
	  }, {
	    key: "unregister",
	    value: function unregister(name, callback) {
	      if (!callback) {
	        this.listeners[name] = [];
	      }

	      var index = this.listeners[name].indexOf(callback);

	      if (index > -1) {
	        this.listeners[name].splice(index, 1);
	      }
	    }
	  }, {
	    key: "call",
	    value: function call(name) {
	      for (var _len = arguments.length, params = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
	        params[_key - 1] = arguments[_key];
	      }

	      (this.listeners[name] || []).forEach(function (callback) {
	        callback.apply(void 0, params);
	      });
	    }
	  }, {
	    key: "has",
	    value: function has(name) {
	      return Object.keys(this.listeners).includes(name);
	    }
	  }]);

	  return MessageBus;
	}();

	var HookManager = {
	  availableHooks: [
	  /**
	   * Public Hooks
	   */
	  'component.initialized', 'element.initialized', 'element.updating', 'element.updated', 'element.removed', 'message.sent', 'message.failed', 'message.received', 'message.processed',
	  /**
	   * Private Hooks
	   */
	  'interceptWireModelSetValue', 'interceptWireModelAttachListener', 'beforeReplaceState', 'beforePushState'],
	  bus: new MessageBus(),
	  register: function register(name, callback) {
	    if (!this.availableHooks.includes(name)) {
	      throw Error("Cresenity: Referencing unknown hook: [".concat(name, "]"));
	    }

	    this.bus.register(name, callback);
	  },
	  call: function call(name) {
	    var _this$bus;

	    for (var _len = arguments.length, params = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
	      params[_key - 1] = arguments[_key];
	    }

	    (_this$bus = this.bus).call.apply(_this$bus, [name].concat(params));
	  }
	};

	var DirectiveManager = {
	  directives: new MessageBus(),
	  register: function register(name, callback) {
	    if (this.has(name)) {
	      throw Error("Cresenity: Directive already registered: [".concat(name, "]"));
	    }

	    this.directives.register(name, callback);
	  },
	  call: function call(name, el, directive, component) {
	    this.directives.call(name, el, directive, component);
	  },
	  has: function has(name) {
	    return this.directives.has(name);
	  }
	};

	var store$1 = {
	  componentsById: {},
	  listeners: new MessageBus(),
	  initialRenderIsFinished: false,
	  cresenityIsInBackground: false,
	  cresenityIsOffline: false,
	  sessionHasExpired: false,
	  directives: DirectiveManager,
	  hooks: HookManager,
	  onErrorCallback: function onErrorCallback() {},
	  components: function components() {
	    var _this = this;

	    return Object.keys(this.componentsById).map(function (key) {
	      return _this.componentsById[key];
	    });
	  },
	  addComponent: function addComponent(component) {
	    return this.componentsById[component.id] = component;
	  },
	  findComponent: function findComponent(id) {
	    return this.componentsById[id];
	  },
	  getComponentsByName: function getComponentsByName(name) {
	    return this.components().filter(function (component) {
	      return component.name === name;
	    });
	  },
	  hasComponent: function hasComponent(id) {
	    return !!this.componentsById[id];
	  },
	  tearDownComponents: function tearDownComponents() {
	    var _this2 = this;

	    this.components().forEach(function (component) {
	      _this2.removeComponent(component);
	    });
	  },
	  on: function on(event, callback) {
	    this.listeners.register(event, callback);
	  },
	  off: function off(event, callback) {
	    this.listeners.unregister(event, callback);
	  },
	  emit: function emit(event) {
	    var _this$listeners;

	    for (var _len = arguments.length, params = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
	      params[_key - 1] = arguments[_key];
	    }

	    (_this$listeners = this.listeners).call.apply(_this$listeners, [event].concat(params));

	    this.componentsListeningForEvent(event).forEach(function (component) {
	      return component.addAction(new _default$5(event, params));
	    });
	  },
	  emitUp: function emitUp(el, event) {
	    for (var _len2 = arguments.length, params = new Array(_len2 > 2 ? _len2 - 2 : 0), _key2 = 2; _key2 < _len2; _key2++) {
	      params[_key2 - 2] = arguments[_key2];
	    }

	    this.componentsListeningForEventThatAreTreeAncestors(el, event).forEach(function (component) {
	      return component.addAction(new _default$5(event, params));
	    });
	  },
	  emitSelf: function emitSelf(componentId, event) {
	    var component = this.findComponent(componentId);

	    if (component.listeners.includes(event)) {
	      for (var _len3 = arguments.length, params = new Array(_len3 > 2 ? _len3 - 2 : 0), _key3 = 2; _key3 < _len3; _key3++) {
	        params[_key3 - 2] = arguments[_key3];
	      }

	      component.addAction(new _default$5(event, params));
	    }
	  },
	  emitTo: function emitTo(componentName, event) {
	    for (var _len4 = arguments.length, params = new Array(_len4 > 2 ? _len4 - 2 : 0), _key4 = 2; _key4 < _len4; _key4++) {
	      params[_key4 - 2] = arguments[_key4];
	    }

	    var components = this.getComponentsByName(componentName);
	    components.forEach(function (component) {
	      if (component.listeners.includes(event)) {
	        component.addAction(new _default$5(event, params));
	      }
	    });
	  },
	  componentsListeningForEventThatAreTreeAncestors: function componentsListeningForEventThatAreTreeAncestors(el, event) {
	    var parentIds = [];
	    var parent = el.parentElement.closest('[cres\\:id]');

	    while (parent) {
	      parentIds.push(parent.getAttribute('cres:id'));
	      parent = parent.parentElement.closest('[cres\\:id]');
	    }

	    return this.components().filter(function (component) {
	      return component.listeners.includes(event) && parentIds.includes(component.id);
	    });
	  },
	  componentsListeningForEvent: function componentsListeningForEvent(event) {
	    return this.components().filter(function (component) {
	      return component.listeners.includes(event);
	    });
	  },
	  registerDirective: function registerDirective(name, callback) {
	    this.directives.register(name, callback);
	  },
	  registerHook: function registerHook(name, callback) {
	    this.hooks.register(name, callback);
	  },
	  callHook: function callHook(name) {
	    var _this$hooks;

	    for (var _len5 = arguments.length, params = new Array(_len5 > 1 ? _len5 - 1 : 0), _key5 = 1; _key5 < _len5; _key5++) {
	      params[_key5 - 1] = arguments[_key5];
	    }

	    (_this$hooks = this.hooks).call.apply(_this$hooks, [name].concat(params));
	  },
	  changeComponentId: function changeComponentId(component, newId) {
	    var oldId = component.id;
	    component.id = newId;
	    component.fingerprint.id = newId;
	    this.componentsById[newId] = component;
	    delete this.componentsById[oldId]; // Now go through any parents of this component and change
	    // the component's child id references.

	    this.components().forEach(function (component) {
	      var children = component.serverMemo.children || {};
	      Object.entries(children).forEach(function (_ref) {
	        var _ref2 = _slicedToArray(_ref, 2),
	            key = _ref2[0],
	            _ref2$ = _ref2[1],
	            id = _ref2$.id;
	            _ref2$.tagName;

	        if (id === oldId) {
	          children[key].id = newId;
	        }
	      });
	    });
	  },
	  removeComponent: function removeComponent(component) {
	    // Remove event listeners attached to the DOM.
	    component.tearDown(); // Remove the component from the store.

	    delete this.componentsById[component.id];
	  },
	  onError: function onError(callback) {
	    this.onErrorCallback = callback;
	  },
	  getClosestParentId: function getClosestParentId(childId, subsetOfParentIds) {
	    var _this3 = this;

	    var distancesByParentId = {};
	    subsetOfParentIds.forEach(function (parentId) {
	      var distance = _this3.getDistanceToChild(parentId, childId);

	      if (distance) {
	        distancesByParentId[parentId] = distance;
	      }
	    });
	    var smallestDistance = Math.min.apply(Math, _toConsumableArray(Object.values(distancesByParentId)));
	    var closestParentId;
	    Object.entries(distancesByParentId).forEach(function (_ref3) {
	      var _ref4 = _slicedToArray(_ref3, 2),
	          parentId = _ref4[0],
	          distance = _ref4[1];

	      if (distance === smallestDistance) {
	        closestParentId = parentId;
	      }
	    });
	    return closestParentId;
	  },
	  getDistanceToChild: function getDistanceToChild(parentId, childId) {
	    var distanceMemo = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 1;
	    var parentComponent = this.findComponent(parentId);

	    if (!parentComponent) {
	      return;
	    }

	    var childIds = parentComponent.childIds;

	    if (childIds.includes(childId)) {
	      return distanceMemo;
	    }

	    for (var i = 0; i < childIds.length; i++) {
	      var distance = this.getDistanceToChild(childIds[i], childId, distanceMemo + 1);

	      if (distance) {
	        return distance;
	      }
	    }
	  }
	};

	/**
	 * This is intended to isolate all native DOM operations. The operations that happen
	 * one specific element will be instance methods, the operations you would normally
	 * perform on the "document" (like "document.querySelector") will be static methods.
	 */

	var DOM = {
	  rootComponentElements: function rootComponentElements() {
	    return Array.from(document.querySelectorAll('[cres\\:id]'));
	  },
	  rootComponentElementsWithNoParents: function rootComponentElementsWithNoParents() {
	    var node = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

	    if (node === null) {
	      node = document;
	    } // In CSS, it's simple to select all elements that DO have a certain ancestor.
	    // However, it's not simple (kinda impossible) to select elements that DONT have
	    // a certain ancestor. Therefore, we will flip the logic: select all roots that DO have
	    // have a root ancestor, then select all roots that DONT, then diff the two.
	    // Convert NodeLists to Arrays so we can use ".includes()". Ew.


	    var allEls = Array.from(node.querySelectorAll('[cres\\:initial-data]'));
	    var onlyChildEls = Array.from(node.querySelectorAll('[cres\\:initial-data] [cres\\:initial-data]'));
	    return allEls.filter(function (el) {
	      return !onlyChildEls.includes(el);
	    });
	  },
	  allModelElementsInside: function allModelElementsInside(root) {
	    return Array.from(root.querySelectorAll('[cres\\:model]'));
	  },
	  getByAttributeAndValue: function getByAttributeAndValue(attribute, value) {
	    return document.querySelector("[cres\\:".concat(attribute, "=\"").concat(value, "\"]"));
	  },
	  nextFrame: function nextFrame(fn) {
	    var _this = this;

	    requestAnimationFrame(function () {
	      requestAnimationFrame(fn.bind(_this));
	    });
	  },
	  closestRoot: function closestRoot(el) {
	    return this.closestByAttribute(el, 'id');
	  },
	  closestByAttribute: function closestByAttribute(el, attribute) {
	    var closestEl = el.closest("[cres\\:".concat(attribute, "]"));

	    if (!closestEl) {
	      // eslint-disable-next-line no-throw-literal
	      throw "\nCresenity Error:\n\nCannot find parent element in DOM tree containing attribute: [cres:".concat(attribute, "].\n\nUsually this is caused by Cresenity's DOM-differ not being able to properly track changes.\n\nReference the following guide for common causes: https://laravel-cresenity.com/docs/troubleshooting \n\nReferenced element:\n\n").concat(el.outerHTML, "\n");
	    }

	    return closestEl;
	  },
	  isComponentRootEl: function isComponentRootEl(el) {
	    return this.hasAttribute(el, 'id');
	  },
	  hasAttribute: function hasAttribute(el, attribute) {
	    return el.hasAttribute("cres:".concat(attribute));
	  },
	  getAttribute: function getAttribute(el, attribute) {
	    return el.getAttribute("cres:".concat(attribute));
	  },
	  removeAttribute: function removeAttribute(el, attribute) {
	    return el.removeAttribute("cres:".concat(attribute));
	  },
	  setAttribute: function setAttribute(el, attribute, value) {
	    return el.setAttribute("cres:".concat(attribute), value);
	  },
	  hasFocus: function hasFocus(el) {
	    return el === document.activeElement;
	  },
	  isInput: function isInput(el) {
	    return ['INPUT', 'TEXTAREA', 'SELECT'].includes(el.tagName.toUpperCase());
	  },
	  isTextInput: function isTextInput(el) {
	    return ['INPUT', 'TEXTAREA'].includes(el.tagName.toUpperCase()) && !['checkbox', 'radio'].includes(el.type);
	  },
	  valueFromInput: function valueFromInput(el, component) {
	    if (el.type === 'checkbox') {
	      var modelName = cresDirectives(el).get('model').value; // If there is an update from cres:model.defer in the chamber,
	      // we need to pretend that is the actual data from the server.

	      var modelValue = component.deferredActions[modelName] ? component.deferredActions[modelName].payload.value : getValue(component.data, modelName);

	      if (Array.isArray(modelValue)) {
	        return this.mergeCheckboxValueIntoArray(el, modelValue);
	      }

	      if (el.checked) {
	        return el.getAttribute('value') || true;
	      }

	      return false;
	    } else if (el.tagName === 'SELECT' && el.multiple) {
	      return this.getSelectValues(el);
	    }

	    return el.value;
	  },
	  mergeCheckboxValueIntoArray: function mergeCheckboxValueIntoArray(el, arrayValue) {
	    if (el.checked) {
	      return arrayValue.includes(el.value) ? arrayValue : arrayValue.concat(el.value);
	    }

	    return arrayValue.filter(function (item) {
	      return item !== el.value;
	    });
	  },
	  setInputValueFromModel: function setInputValueFromModel(el, component) {
	    var modelString = cresDirectives(el).get('model').value;
	    var modelValue = getValue(component.data, modelString); // Don't manually set file input's values.

	    if (el.tagName.toLowerCase() === 'input' && el.type === 'file') {
	      return;
	    }

	    this.setInputValue(el, modelValue);
	  },
	  setInputValue: function setInputValue(el, value) {
	    store$1.callHook('interceptWireModelSetValue', value, el);

	    if (el.type === 'radio') {
	      el.checked = el.value == value;
	    } else if (el.type === 'checkbox') {
	      if (Array.isArray(value)) {
	        // I'm purposely not using Array.includes here because it's
	        // strict, and because of Numeric/String mis-casting, I
	        // want the "includes" to be "fuzzy".
	        var valueFound = false;
	        value.forEach(function (val) {
	          if (val == el.value) {
	            valueFound = true;
	          }
	        });
	        el.checked = valueFound;
	      } else {
	        el.checked = !!value;
	      }
	    } else if (el.tagName === 'SELECT') {
	      this.updateSelect(el, value);
	    } else {
	      value = value === undefined ? '' : value;
	      el.value = value;
	    }
	  },
	  getSelectValues: function getSelectValues(el) {
	    return Array.from(el.options).filter(function (option) {
	      return option.selected;
	    }).map(function (option) {
	      return option.value || option.text;
	    });
	  },
	  updateSelect: function updateSelect(el, value) {
	    var arrayWrappedValue = [].concat(value).map(function (value) {
	      return value + '';
	    });
	    Array.from(el.options).forEach(function (option) {
	      option.selected = arrayWrappedValue.includes(option.value);
	    });
	  }
	};

	var Connection = /*#__PURE__*/function () {
	  function Connection() {
	    _classCallCheck(this, Connection);
	  }

	  _createClass(Connection, [{
	    key: "onMessage",
	    value: function onMessage(message, payload) {
	      message.component.receiveMessage(message, payload);
	    }
	  }, {
	    key: "onError",
	    value: function onError(message, status) {
	      message.component.messageSendFailed();
	      return store$1.onErrorCallback(status);
	    }
	  }, {
	    key: "sendMessage",
	    value: function sendMessage(message) {
	      var _this = this;

	      var payload = message.payload(); // eslint-disable-next-line no-underscore-dangle

	      if (window.__testing_request_interceptor) {
	        // eslint-disable-next-line no-underscore-dangle
	        return window.__testing_request_interceptor(payload, this);
	      } // Forward the query string for the ajax requests.


	      fetch("".concat(window.capp.baseUrl, "cresenity/component/message/").concat(payload.fingerprint.name), {
	        method: 'POST',
	        body: JSON.stringify(payload),
	        // This enables "cookies".
	        credentials: 'same-origin',
	        headers: {
	          'Content-Type': 'application/json',
	          Accept: 'text/html, application/xhtml+xml',
	          'X-CSRF-TOKEN': getCsrfToken(),
	          'X-Socket-ID': this.getSocketId(),
	          'X-Cresenity': true,
	          // We'll set this explicitly to mitigate potential interference from ad-blockers/etc.
	          Referer: window.location.href
	        }
	      }).then(function (response) {
	        if (response.ok) {
	          response.text().then(function (response) {
	            if (_this.isOutputFromDump(response)) {
	              _this.onError(message);

	              showHtmlModal(response);
	            } else {
	              _this.onMessage(message, JSON.parse(response));
	            }
	          });
	        } else {
	          if (_this.onError(message, response.status) === false) {
	            return;
	          }

	          if (response.status === 419) {
	            if (store$1.sessionHasExpired) {
	              return;
	            }

	            store$1.sessionHasExpired = true; // eslint-disable-next-line no-alert

	            confirm('This page has expired due to inactivity.\nWould you like to refresh the page?') && window.location.reload();
	          } else {
	            response.text().then(function (response) {
	              showHtmlModal(response);
	            });
	          }
	        }
	      }).catch(function () {
	        _this.onError(message);
	      });
	    }
	  }, {
	    key: "isOutputFromDump",
	    value: function isOutputFromDump(output) {
	      return !!output.match(/<script>Sfdump\(".+"\)<\/script>/);
	    }
	  }, {
	    key: "getSocketId",
	    value: function getSocketId() {
	      if (typeof window.Echo !== 'undefined') {
	        return window.Echo.socketId();
	      }
	    }
	  }]);

	  return Connection;
	}();

	var _default$4 = /*#__PURE__*/function (_Action) {
	  _inherits(_default, _Action);

	  var _super = _createSuper(_default);

	  function _default(method, params, el) {
	    var _this;

	    var skipWatcher = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;

	    _classCallCheck(this, _default);

	    _this = _super.call(this, el, skipWatcher);
	    _this.type = 'callMethod';
	    _this.method = method;
	    _this.payload = {
	      method: method,
	      params: params
	    };
	    return _this;
	  }

	  return _default;
	}(_default$6);

	/* eslint-disable camelcase */
	function Polling () {
	  store$1.registerHook('element.initialized', function (el, component) {
	    var directives = cresDirectives(el);

	    if (directives.missing('poll')) {
	      return;
	    }

	    var intervalId = fireActionOnInterval(el, component);
	    component.addListenerForTeardown(function () {
	      clearInterval(intervalId);
	    });
	    el.__cresenity_polling_interval = intervalId;
	  });
	  store$1.registerHook('element.updating', function (from, to, component) {
	    if (from.__cresenity_polling_interval !== undefined) {
	      return;
	    }

	    if (cresDirectives(from).missing('poll') && cresDirectives(to).has('poll')) {
	      setTimeout(function () {
	        var intervalId = fireActionOnInterval(from, component);
	        component.addListenerForTeardown(function () {
	          clearInterval(intervalId);
	        });
	        from.__cresenity_polling_interval = intervalId;
	      }, 0);
	    }
	  });
	}

	function fireActionOnInterval(node, component) {
	  var interval = cresDirectives(node).get('poll').durationOr(2000);
	  return setInterval(function () {
	    if (node.isConnected === false) {
	      return;
	    }

	    var directives = cresDirectives(node); // Don't poll when directive is removed from element.

	    if (directives.missing('poll')) {
	      return;
	    }

	    var directive = directives.get('poll');
	    var method = directive.method || '$refresh'; // Don't poll when the tab is in the background.
	    // (unless the "cres:poll.keep-alive" modifier is attached)

	    if (store$1.cresenityIsInBackground && !directive.modifiers.includes('keep-alive')) {
	      // This "Math.random" business effectivlly prevents 95% of requests
	      // from executing. We still want "some" requests to get through.
	      if (Math.random() < 0.95) {
	        return;
	      }
	    } // Don't poll if cresenity is offline as well.


	    if (store$1.cresenityIsOffline) {
	      return;
	    }

	    component.addAction(new _default$4(method, directive.params, node));
	  }, interval);
	}

	var _default$3 = /*#__PURE__*/function () {
	  function _default(component, updateQueue) {
	    _classCallCheck(this, _default);

	    this.component = component;
	    this.updateQueue = updateQueue;
	  }

	  _createClass(_default, [{
	    key: "payload",
	    value: function payload() {
	      return {
	        fingerprint: this.component.fingerprint,
	        serverMemo: this.component.serverMemo,
	        // This ensures only the type & payload properties only get sent over.
	        updates: this.updateQueue.map(function (update) {
	          return {
	            type: update.type,
	            payload: update.payload
	          };
	        })
	      };
	    }
	  }, {
	    key: "shouldSkipWatcherForDataKey",
	    value: function shouldSkipWatcherForDataKey(dataKey) {
	      // If the data is dirty, run the watcher.
	      if (this.response.effects.dirty.includes(dataKey)) {
	        return false;
	      }

	      var compareBeforeFirstDot = function compareBeforeFirstDot(subject, value) {
	        if (typeof subject !== 'string' || typeof value !== 'string') {
	          return false;
	        }

	        return subject.split('.')[0] === value.split('.')[0];
	      }; // Otherwise see if there was a defered update for a data key.
	      // In that case, we want to skip running the Cresenity watcher.


	      return this.updateQueue.filter(function (update) {
	        return compareBeforeFirstDot(update.name, dataKey);
	      }).some(function (update) {
	        return update.skipWatcher;
	      });
	    }
	  }, {
	    key: "storeResponse",
	    value: function storeResponse(payload) {
	      return this.response = payload;
	    }
	  }, {
	    key: "resolve",
	    value: function resolve() {
	      var returns = this.response.effects.returns || [];
	      this.updateQueue.forEach(function (update) {
	        if (update.type !== 'callMethod') {
	          return;
	        }

	        update.resolve(returns[update.method] !== undefined ? returns[update.method] : null);
	      });
	    }
	  }, {
	    key: "reject",
	    value: function reject() {
	      this.updateQueue.forEach(function (update) {
	        update.reject();
	      });
	    }
	  }]);

	  return _default;
	}();

	var _default$2 = /*#__PURE__*/function (_Message) {
	  _inherits(_default, _Message);

	  var _super = _createSuper(_default);

	  function _default(component, action) {
	    _classCallCheck(this, _default);

	    return _super.call(this, component, [action]);
	  }

	  _createClass(_default, [{
	    key: "prefetchId",
	    get: function get() {
	      return this.updateQueue[0].toId();
	    }
	  }]);

	  return _default;
	}(_default$3);

	/**
	 * I don't want to look at "value" attributes when diffing.
	 * I commented out all the lines that compare "value"
	 *
	 */
	function morphAttrs(fromNode, toNode) {
	  var attrs = toNode.attributes;
	  var i;
	  var attr;
	  var attrName;
	  var attrNamespaceURI;
	  var attrValue;
	  var fromValue; // update attributes on original DOM element

	  for (i = attrs.length - 1; i >= 0; --i) {
	    attr = attrs[i];
	    attrName = attr.name;
	    attrNamespaceURI = attr.namespaceURI;
	    attrValue = attr.value;

	    if (attrNamespaceURI) {
	      attrName = attr.localName || attrName;
	      fromValue = fromNode.getAttributeNS(attrNamespaceURI, attrName);

	      if (fromValue !== attrValue) {
	        if (attr.prefix === 'xmlns') {
	          attrName = attr.name; // It's not allowed to set an attribute with the XMLNS namespace without specifying the `xmlns` prefix
	        }

	        fromNode.setAttributeNS(attrNamespaceURI, attrName, attrValue);
	      }
	    } else {
	      fromValue = fromNode.getAttribute(attrName);

	      if (fromValue !== attrValue) {
	        fromNode.setAttribute(attrName, attrValue);
	      }
	    }
	  } // Remove any extra attributes found on the original DOM element that
	  // weren't found on the target element.


	  attrs = fromNode.attributes;

	  for (i = attrs.length - 1; i >= 0; --i) {
	    attr = attrs[i];

	    if (attr.specified !== false) {
	      attrName = attr.name;
	      attrNamespaceURI = attr.namespaceURI;

	      if (attrNamespaceURI) {
	        attrName = attr.localName || attrName;

	        if (!toNode.hasAttributeNS(attrNamespaceURI, attrName)) {
	          fromNode.removeAttributeNS(attrNamespaceURI, attrName);
	        }
	      } else if (!toNode.hasAttribute(attrName)) {
	        fromNode.removeAttribute(attrName);
	      }
	    }
	  }
	}

	var range; // Create a range object for efficently rendering strings to elements.

	var NS_XHTML = 'http://www.w3.org/1999/xhtml';
	var doc = typeof document === 'undefined' ? undefined : document;
	var HAS_TEMPLATE_SUPPORT = !!doc && 'content' in doc.createElement('template');
	var HAS_RANGE_SUPPORT = !!doc && doc.createRange && 'createContextualFragment' in doc.createRange();

	function createFragmentFromTemplate(str) {
	  var template = doc.createElement('template');
	  template.innerHTML = str;
	  return template.content.childNodes[0];
	}

	function createFragmentFromRange(str) {
	  if (!range) {
	    range = doc.createRange();
	    range.selectNode(doc.body);
	  }

	  var fragment = range.createContextualFragment(str);
	  return fragment.childNodes[0];
	}

	function createFragmentFromWrap(str) {
	  var fragment = doc.createElement('body');
	  fragment.innerHTML = str;
	  return fragment.childNodes[0];
	}
	/**
	 * This is about the same
	 * var html = new DOMParser().parseFromString(str, 'text/html');
	 * return html.body.firstChild;
	 *
	 * @method toElement
	 * @param {String} str
	 */


	function toElement(str) {
	  str = str.trim();

	  if (HAS_TEMPLATE_SUPPORT) {
	    // avoid restrictions on content for things like `<tr><th>Hi</th></tr>` which
	    // createContextualFragment doesn't support
	    // <template> support not available in IE
	    return createFragmentFromTemplate(str);
	  } else if (HAS_RANGE_SUPPORT) {
	    return createFragmentFromRange(str);
	  }

	  return createFragmentFromWrap(str);
	}
	/**
	 * Returns true if two node's names are the same.
	 *
	 * NOTE: We don't bother checking `namespaceURI` because you will never find two HTML elements with the same
	 *       nodeName and different namespace URIs.
	 *
	 * @param {Element} a
	 * @param {Element} b The target element
	 * @return {boolean}
	 */

	function compareNodeNames(fromEl, toEl) {
	  var fromNodeName = fromEl.nodeName;
	  var toNodeName = toEl.nodeName;

	  if (fromNodeName === toNodeName) {
	    return true;
	  }

	  if (toEl.actualize && fromNodeName.charCodeAt(0) < 91 &&
	  /* from tag name is upper case */
	  toNodeName.charCodeAt(0) > 90
	  /* target tag name is lower case */
	  ) {
	    // If the target element is a virtual DOM node then we may need to normalize the tag name
	    // before comparing. Normal HTML elements that are in the "http://www.w3.org/1999/xhtml"
	    // are converted to upper case
	    return fromNodeName === toNodeName.toUpperCase();
	  } else {
	    return false;
	  }
	}
	/**
	 * Create an element, optionally with a known namespace URI.
	 *
	 * @param {string} name the element name, e.g. 'div' or 'svg'
	 * @param {string} [namespaceURI] the element's namespace URI, i.e. the value of
	 * its `xmlns` attribute or its inferred namespace.
	 *
	 * @return {Element}
	 */

	function createElementNS(name, namespaceURI) {
	  return !namespaceURI || namespaceURI === NS_XHTML ? doc.createElement(name) : doc.createElementNS(namespaceURI, name);
	}
	/**
	 * Copies the children of one DOM element to another DOM element
	 */

	function moveChildren(fromEl, toEl) {
	  var curChild = fromEl.firstChild;

	  while (curChild) {
	    var nextChild = curChild.nextSibling;
	    toEl.appendChild(curChild);
	    curChild = nextChild;
	  }

	  return toEl;
	}

	function syncBooleanAttrProp(fromEl, toEl, name) {
	  if (fromEl[name] !== toEl[name]) {
	    fromEl[name] = toEl[name];

	    if (fromEl[name]) {
	      fromEl.setAttribute(name, '');
	    } else {
	      fromEl.removeAttribute(name);
	    }
	  }
	}

	var specialElHandlers = {
	  OPTION: function OPTION(fromEl, toEl) {
	    var parentNode = fromEl.parentNode;

	    if (parentNode) {
	      var parentName = parentNode.nodeName.toUpperCase();

	      if (parentName === 'OPTGROUP') {
	        parentNode = parentNode.parentNode;
	        parentName = parentNode && parentNode.nodeName.toUpperCase();
	      }

	      if (parentName === 'SELECT' && !parentNode.hasAttribute('multiple')) {
	        if (fromEl.hasAttribute('selected') && !toEl.selected) {
	          // Workaround for MS Edge bug where the 'selected' attribute can only be
	          // removed if set to a non-empty value:
	          // https://developer.microsoft.com/en-us/microsoft-edge/platform/issues/12087679/
	          fromEl.setAttribute('selected', 'selected');
	          fromEl.removeAttribute('selected');
	        } // We have to reset select element's selectedIndex to -1, otherwise setting
	        // fromEl.selected using the syncBooleanAttrProp below has no effect.
	        // The correct selectedIndex will be set in the SELECT special handler below.


	        parentNode.selectedIndex = -1;
	      }
	    }

	    syncBooleanAttrProp(fromEl, toEl, 'selected');
	  },

	  /**
	   * The "value" attribute is special for the <input> element since it sets
	   * the initial value. Changing the "value" attribute without changing the
	   * "value" property will have no effect since it is only used to the set the
	   * initial value.  Similar for the "checked" attribute, and "disabled".
	   */
	  INPUT: function INPUT(fromEl, toEl) {
	    syncBooleanAttrProp(fromEl, toEl, 'checked');
	    syncBooleanAttrProp(fromEl, toEl, 'disabled');

	    if (fromEl.value !== toEl.value) {
	      fromEl.value = toEl.value;
	    }

	    if (!toEl.hasAttribute('value')) {
	      fromEl.removeAttribute('value');
	    }
	  },
	  TEXTAREA: function TEXTAREA(fromEl, toEl) {
	    var newValue = toEl.value;

	    if (fromEl.value !== newValue) {
	      fromEl.value = newValue;
	    }

	    var firstChild = fromEl.firstChild;

	    if (firstChild) {
	      // Needed for IE. Apparently IE sets the placeholder as the
	      // node value and vise versa. This ignores an empty update.
	      var oldValue = firstChild.nodeValue;

	      if (oldValue == newValue || !newValue && oldValue == fromEl.placeholder) {
	        return;
	      }

	      firstChild.nodeValue = newValue;
	    }
	  },
	  SELECT: function SELECT(fromEl, toEl) {
	    if (!toEl.hasAttribute('multiple')) {
	      var selectedIndex = -1;
	      var i = 0; // We have to loop through children of fromEl, not toEl since nodes can be moved
	      // from toEl to fromEl directly when morphing.
	      // At the time this special handler is invoked, all children have already been morphed
	      // and appended to / removed from fromEl, so using fromEl here is safe and correct.

	      var curChild = fromEl.firstChild;
	      var optgroup;
	      var nodeName;

	      while (curChild) {
	        nodeName = curChild.nodeName && curChild.nodeName.toUpperCase();

	        if (nodeName === 'OPTGROUP') {
	          optgroup = curChild;
	          curChild = optgroup.firstChild;
	        } else {
	          if (nodeName === 'OPTION') {
	            if (curChild.hasAttribute('selected')) {
	              selectedIndex = i;
	              break;
	            }

	            i++;
	          }

	          curChild = curChild.nextSibling;

	          if (!curChild && optgroup) {
	            curChild = optgroup.nextSibling;
	            optgroup = null;
	          }
	        }
	      }

	      fromEl.selectedIndex = selectedIndex;
	    }
	  }
	};

	// From Caleb: I had to change all the "isSameNode"s to "isEqualNode"s and now everything is working great!
	var ELEMENT_NODE = 1;
	var DOCUMENT_FRAGMENT_NODE = 11;
	var TEXT_NODE = 3;
	var COMMENT_NODE = 8;

	function noop() {}

	function defaultGetNodeKey(node) {
	  return node.id;
	}

	function callHook(hook) {
	  if (hook.name !== 'getNodeKey' && hook.name !== 'onBeforeElUpdated') ; // Don't call hook on non-"DOMElement" elements.


	  for (var _len = arguments.length, params = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
	    params[_key - 1] = arguments[_key];
	  }

	  if (typeof params[0].hasAttribute !== 'function') {
	    return;
	  }

	  return hook.apply(void 0, params);
	}

	function morphdomFactory(morphAttrs) {
	  return function morphdom(fromNode, toNode, options) {
	    if (!options) {
	      options = {};
	    }

	    if (typeof toNode === 'string') {
	      if (fromNode.nodeName === '#document' || fromNode.nodeName === 'HTML') {
	        var toNodeHtml = toNode;
	        toNode = doc.createElement('html');
	        toNode.innerHTML = toNodeHtml;
	      } else {
	        toNode = toElement(toNode);
	      }
	    }

	    var getNodeKey = options.getNodeKey || defaultGetNodeKey;
	    var onBeforeNodeAdded = options.onBeforeNodeAdded || noop;
	    var onNodeAdded = options.onNodeAdded || noop;
	    var onBeforeElUpdated = options.onBeforeElUpdated || noop;
	    var onElUpdated = options.onElUpdated || noop;
	    var onBeforeNodeDiscarded = options.onBeforeNodeDiscarded || noop;
	    var onNodeDiscarded = options.onNodeDiscarded || noop;
	    var onBeforeElChildrenUpdated = options.onBeforeElChildrenUpdated || noop;
	    var childrenOnly = options.childrenOnly === true; // This object is used as a lookup to quickly find all keyed elements in the original DOM tree.

	    var fromNodesLookup = Object.create(null);
	    var keyedRemovalList = [];

	    function addKeyedRemoval(key) {
	      keyedRemovalList.push(key);
	    }

	    function walkDiscardedChildNodes(node, skipKeyedNodes) {
	      if (node.nodeType === ELEMENT_NODE) {
	        var curChild = node.firstChild;

	        while (curChild) {
	          var key = void 0;

	          if (skipKeyedNodes && (key = callHook(getNodeKey, curChild))) {
	            // If we are skipping keyed nodes then we add the key
	            // to a list so that it can be handled at the very end.
	            addKeyedRemoval(key);
	          } else {
	            // Only report the node as discarded if it is not keyed. We do this because
	            // at the end we loop through all keyed elements that were unmatched
	            // and then discard them in one final pass.
	            callHook(onNodeDiscarded, curChild);

	            if (curChild.firstChild) {
	              walkDiscardedChildNodes(curChild, skipKeyedNodes);
	            }
	          }

	          curChild = curChild.nextSibling;
	        }
	      }
	    }
	    /**
	     * Removes a DOM node out of the original DOM
	     *
	     * @param  {Node} node The node to remove
	     * @param  {Node} parentNode The nodes parent
	     * @param  {Boolean} skipKeyedNodes If true then elements with keys will be skipped and not discarded.
	     * @return {undefined}
	     */


	    function removeNode(node, parentNode, skipKeyedNodes) {
	      if (callHook(onBeforeNodeDiscarded, node) === false) {
	        return;
	      }

	      if (parentNode) {
	        parentNode.removeChild(node);
	      }

	      callHook(onNodeDiscarded, node);
	      walkDiscardedChildNodes(node, skipKeyedNodes);
	    }

	    function indexTree(node) {
	      if (node.nodeType === ELEMENT_NODE || node.nodeType === DOCUMENT_FRAGMENT_NODE) {
	        var curChild = node.firstChild;

	        while (curChild) {
	          var key = callHook(getNodeKey, curChild);

	          if (key) {
	            fromNodesLookup[key] = curChild;
	          } // Walk recursively


	          indexTree(curChild);
	          curChild = curChild.nextSibling;
	        }
	      }
	    }

	    indexTree(fromNode);

	    function handleNodeAdded(el) {
	      callHook(onNodeAdded, el);

	      if (el.skipAddingChildren) {
	        return;
	      }

	      var curChild = el.firstChild;

	      while (curChild) {
	        var nextSibling = curChild.nextSibling;
	        var key = callHook(getNodeKey, curChild);

	        if (key) {
	          var unmatchedFromEl = fromNodesLookup[key];

	          if (unmatchedFromEl && compareNodeNames(curChild, unmatchedFromEl)) {
	            curChild.parentNode.replaceChild(unmatchedFromEl, curChild);
	            morphEl(unmatchedFromEl, curChild); // @cresenityModification
	            // Otherwise, "curChild" will be unnatached when it is passed to "handleNodeAdde"
	            // things like .parent and .closest will break.

	            curChild = unmatchedFromEl;
	          }
	        }

	        handleNodeAdded(curChild);
	        curChild = nextSibling;
	      }
	    }

	    function cleanupFromEl(fromEl, curFromNodeChild, curFromNodeKey) {
	      // We have processed all of the "to nodes". If curFromNodeChild is
	      // non-null then we still have some from nodes left over that need
	      // to be removed
	      while (curFromNodeChild) {
	        var fromNextSibling = curFromNodeChild.nextSibling;

	        if (curFromNodeKey = callHook(getNodeKey, curFromNodeChild)) {
	          // Since the node is keyed it might be matched up later so we defer
	          // the actual removal to later
	          addKeyedRemoval(curFromNodeKey);
	        } else {
	          // NOTE: we skip nested keyed nodes from being removed since there is
	          //       still a chance they will be matched up later
	          removeNode(curFromNodeChild, fromEl, true
	          /* skip keyed nodes */
	          );
	        }

	        curFromNodeChild = fromNextSibling;
	      }
	    }

	    function morphEl(fromEl, toEl, childrenOnly) {
	      var toElKey = callHook(getNodeKey, toEl);

	      if (toElKey) {
	        // If an element with an ID is being morphed then it will be in the final
	        // DOM so clear it out of the saved elements collection
	        delete fromNodesLookup[toElKey];
	      }

	      if (!childrenOnly) {
	        if (callHook(onBeforeElUpdated, fromEl, toEl) === false) {
	          return;
	        } // @cresenityModification.
	        // I added this check to enable cres:ignore.self to not fire
	        // morphAttrs, but not skip updating children as well.
	        // A task that's currently impossible with the provided hooks.


	        if (!fromEl.skipElUpdatingButStillUpdateChildren) {
	          morphAttrs(fromEl, toEl);
	        }

	        callHook(onElUpdated, fromEl);

	        if (callHook(onBeforeElChildrenUpdated, fromEl, toEl) === false) {
	          return;
	        }
	      }

	      if (fromEl.nodeName !== 'TEXTAREA') {
	        morphChildren(fromEl, toEl);
	      } else if (fromEl.innerHTML != toEl.innerHTML) {
	        // @cresenityModification
	        // Only mess with the "value" of textarea if the new dom has something
	        // inside the <textarea></textarea> tag.
	        specialElHandlers.TEXTAREA(fromEl, toEl);
	      }
	    }

	    function morphChildren(fromEl, toEl) {
	      var curToNodeChild = toEl.firstChild;
	      var curFromNodeChild = fromEl.firstChild;
	      var curToNodeKey;
	      var curFromNodeKey;
	      var fromNextSibling;
	      var toNextSibling;
	      var matchingFromEl; // walk the children

	      outer: while (curToNodeChild) {
	        toNextSibling = curToNodeChild.nextSibling;
	        curToNodeKey = callHook(getNodeKey, curToNodeChild); // walk the fromNode children all the way through

	        while (curFromNodeChild) {
	          fromNextSibling = curFromNodeChild.nextSibling;

	          if (curToNodeChild.isSameNode && curToNodeChild.isSameNode(curFromNodeChild)) {
	            curToNodeChild = toNextSibling;
	            curFromNodeChild = fromNextSibling;
	            continue outer;
	          }

	          curFromNodeKey = callHook(getNodeKey, curFromNodeChild);
	          var curFromNodeType = curFromNodeChild.nodeType; // this means if the curFromNodeChild doesnt have a match with the curToNodeChild

	          var isCompatible = void 0;

	          if (curFromNodeType === curToNodeChild.nodeType) {
	            if (curFromNodeType === ELEMENT_NODE) {
	              // Both nodes being compared are Element nodes
	              if (curToNodeKey) {
	                // The target node has a key so we want to match it up with the correct element
	                // in the original DOM tree
	                if (curToNodeKey !== curFromNodeKey) {
	                  // The current element in the original DOM tree does not have a matching key so
	                  // let's check our lookup to see if there is a matching element in the original
	                  // DOM tree
	                  if (matchingFromEl = fromNodesLookup[curToNodeKey]) {
	                    if (fromNextSibling === matchingFromEl) {
	                      // Special case for single element removals. To avoid removing the original
	                      // DOM node out of the tree (since that can break CSS transitions, etc.),
	                      // we will instead discard the current node and wait until the next
	                      // iteration to properly match up the keyed target element with its matching
	                      // element in the original tree
	                      isCompatible = false;
	                    } else {
	                      // We found a matching keyed element somewhere in the original DOM tree.
	                      // Let's move the original DOM node into the current position and morph
	                      // it.
	                      // NOTE: We use insertBefore instead of replaceChild because we want to go through
	                      // the `removeNode()` function for the node that is being discarded so that
	                      // all lifecycle hooks are correctly invoked
	                      fromEl.insertBefore(matchingFromEl, curFromNodeChild); // fromNextSibling = curFromNodeChild.nextSibling;

	                      if (curFromNodeKey) {
	                        // Since the node is keyed it might be matched up later so we defer
	                        // the actual removal to later
	                        addKeyedRemoval(curFromNodeKey);
	                      } else {
	                        // NOTE: we skip nested keyed nodes from being removed since there is
	                        //       still a chance they will be matched up later
	                        removeNode(curFromNodeChild, fromEl, true
	                        /* skip keyed nodes */
	                        );
	                      }

	                      curFromNodeChild = matchingFromEl;
	                    }
	                  } else {
	                    // The nodes are not compatible since the "to" node has a key and there
	                    // is no matching keyed node in the source tree
	                    isCompatible = false;
	                  }
	                }
	              } else if (curFromNodeKey) {
	                // The original has a key
	                isCompatible = false;
	              }

	              isCompatible = isCompatible !== false && compareNodeNames(curFromNodeChild, curToNodeChild);

	              if (isCompatible) {
	                // @cresenityModification
	                // If the two nodes are different, but the next element is an exact match,
	                // we can assume that the new node is meant to be inserted, instead of
	                // used as a morph target.
	                if (!curToNodeChild.isEqualNode(curFromNodeChild) && curToNodeChild.nextElementSibling && curToNodeChild.nextElementSibling.isEqualNode(curFromNodeChild)) {
	                  isCompatible = false;
	                } else {
	                  // We found compatible DOM elements so transform
	                  // the current "from" node to match the current
	                  // target DOM node.
	                  // MORPH
	                  morphEl(curFromNodeChild, curToNodeChild);
	                }
	              }
	            } else if (curFromNodeType === TEXT_NODE || curFromNodeType == COMMENT_NODE) {
	              // Both nodes being compared are Text or Comment nodes
	              isCompatible = true; // Simply update nodeValue on the original node to
	              // change the text value

	              if (curFromNodeChild.nodeValue !== curToNodeChild.nodeValue) {
	                curFromNodeChild.nodeValue = curToNodeChild.nodeValue;
	              }
	            }
	          }

	          if (isCompatible) {
	            // Advance both the "to" child and the "from" child since we found a match
	            // Nothing else to do as we already recursively called morphChildren above
	            curToNodeChild = toNextSibling;
	            curFromNodeChild = fromNextSibling;
	            continue outer;
	          } // @cresenityModification
	          // Before we just remove the original element, let's see if it's the very next
	          // element in the "to" list. If it is, we can assume we can insert the new
	          // element before the original one instead of removing it. This is kind of
	          // a "look-ahead".


	          if (curToNodeChild.nextElementSibling && curToNodeChild.nextElementSibling.isEqualNode(curFromNodeChild)) {
	            var nodeToBeAdded = curToNodeChild.cloneNode(true);
	            fromEl.insertBefore(nodeToBeAdded, curFromNodeChild);
	            handleNodeAdded(nodeToBeAdded);
	            curToNodeChild = curToNodeChild.nextElementSibling.nextSibling;
	            curFromNodeChild = fromNextSibling;
	            continue outer;
	          } else {
	            // No compatible match so remove the old node from the DOM and continue trying to find a
	            // match in the original DOM. However, we only do this if the from node is not keyed
	            // since it is possible that a keyed node might match up with a node somewhere else in the
	            // target tree and we don't want to discard it just yet since it still might find a
	            // home in the final DOM tree. After everything is done we will remove any keyed nodes
	            // that didn't find a home
	            if (curFromNodeKey) {
	              // Since the node is keyed it might be matched up later so we defer
	              // the actual removal to later
	              addKeyedRemoval(curFromNodeKey);
	            } else {
	              // NOTE: we skip nested keyed nodes from being removed since there is
	              //       still a chance they will be matched up later
	              removeNode(curFromNodeChild, fromEl, true
	              /* skip keyed nodes */
	              );
	            }
	          }

	          curFromNodeChild = fromNextSibling;
	        } // END: while(curFromNodeChild) {}
	        // If we got this far then we did not find a candidate match for
	        // our "to node" and we exhausted all of the children "from"
	        // nodes. Therefore, we will just append the current "to" node
	        // to the end


	        if (curToNodeKey && (matchingFromEl = fromNodesLookup[curToNodeKey]) && compareNodeNames(matchingFromEl, curToNodeChild)) {
	          fromEl.appendChild(matchingFromEl); // MORPH

	          morphEl(matchingFromEl, curToNodeChild);
	        } else {
	          var onBeforeNodeAddedResult = callHook(onBeforeNodeAdded, curToNodeChild);

	          if (onBeforeNodeAddedResult !== false) {
	            if (onBeforeNodeAddedResult) {
	              curToNodeChild = onBeforeNodeAddedResult;
	            }

	            if (curToNodeChild.actualize) {
	              curToNodeChild = curToNodeChild.actualize(fromEl.ownerDocument || doc);
	            }

	            fromEl.appendChild(curToNodeChild);
	            handleNodeAdded(curToNodeChild);
	          }
	        }

	        curToNodeChild = toNextSibling;
	        curFromNodeChild = fromNextSibling;
	      }

	      cleanupFromEl(fromEl, curFromNodeChild, curFromNodeKey);
	      var specialElHandler = specialElHandlers[fromEl.nodeName];

	      if (specialElHandler && !fromEl.isCresenityModel) {
	        specialElHandler(fromEl, toEl);
	      }
	    } // END: morphChildren(...)


	    var morphedNode = fromNode;
	    var morphedNodeType = morphedNode.nodeType;
	    var toNodeType = toNode.nodeType;

	    if (!childrenOnly) {
	      // Handle the case where we are given two DOM nodes that are not
	      // compatible (e.g. <div> --> <span> or <div> --> TEXT)
	      if (morphedNodeType === ELEMENT_NODE) {
	        if (toNodeType === ELEMENT_NODE) {
	          if (!compareNodeNames(fromNode, toNode)) {
	            callHook(onNodeDiscarded, fromNode);
	            morphedNode = moveChildren(fromNode, createElementNS(toNode.nodeName, toNode.namespaceURI));
	          }
	        } else {
	          // Going from an element node to a text node
	          morphedNode = toNode;
	        }
	      } else if (morphedNodeType === TEXT_NODE || morphedNodeType === COMMENT_NODE) {
	        // Text or comment node
	        if (toNodeType === morphedNodeType) {
	          if (morphedNode.nodeValue !== toNode.nodeValue) {
	            morphedNode.nodeValue = toNode.nodeValue;
	          }

	          return morphedNode;
	        } // Text node to something else


	        morphedNode = toNode;
	      }
	    }

	    if (morphedNode === toNode) {
	      // The "to node" was not compatible with the "from node" so we had to
	      // toss out the "from node" and use the "to node"
	      callHook(onNodeDiscarded, fromNode);
	    } else {
	      if (toNode.isSameNode && toNode.isSameNode(morphedNode)) {
	        return;
	      }

	      morphEl(morphedNode, toNode, childrenOnly); // We now need to loop over any keyed nodes that might need to be
	      // removed. We only do the removal if we know that the keyed node
	      // never found a match. When a keyed node is matched up we remove
	      // it out of fromNodesLookup and we use fromNodesLookup to determine
	      // if a keyed node has been matched up or not

	      if (keyedRemovalList) {
	        for (var i = 0, len = keyedRemovalList.length; i < len; i++) {
	          var elToRemove = fromNodesLookup[keyedRemovalList[i]];

	          if (elToRemove) {
	            removeNode(elToRemove, elToRemove.parentNode, false);
	          }
	        }
	      }
	    }

	    if (!childrenOnly && morphedNode !== fromNode && fromNode.parentNode) {
	      if (morphedNode.actualize) {
	        morphedNode = morphedNode.actualize(fromNode.ownerDocument || doc);
	      } // If we had to swap out the from node with a new node because the old
	      // node was not compatible with the target node then we need to
	      // replace the old DOM node in the original DOM tree. This is only
	      // possible if the original DOM node was part of a DOM tree which
	      // we know is the case if it has a parent node.


	      fromNode.parentNode.replaceChild(morphedNode, fromNode);
	    }

	    return morphedNode;
	  };
	}

	var morphdom = morphdomFactory(morphAttrs);

	var _default$1 = /*#__PURE__*/function (_Action) {
	  _inherits(_default, _Action);

	  var _super = _createSuper(_default);

	  function _default(name, value, el) {
	    var _this;

	    _classCallCheck(this, _default);

	    _this = _super.call(this, el);
	    _this.type = 'syncInput';
	    _this.name = name;
	    _this.payload = {
	      name: name,
	      value: value
	    };
	    return _this;
	  }

	  return _default;
	}(_default$6);

	var _default = /*#__PURE__*/function (_Action) {
	  _inherits(_default, _Action);

	  var _super = _createSuper(_default);

	  function _default(name, value, el) {
	    var _this;

	    var skipWatcher = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;

	    _classCallCheck(this, _default);

	    _this = _super.call(this, el, skipWatcher);
	    _this.type = 'syncInput';
	    _this.name = name;
	    _this.payload = {
	      name: name,
	      value: value
	    };
	    return _this;
	  }

	  return _default;
	}(_default$6);

	var nodeInitializer = {
	  initialize: function initialize(el, component) {
	    var _this = this;

	    if (store$1.initialRenderIsFinished && el.tagName.toLowerCase() === 'script') {
	      eval(el.innerHTML);
	      return false;
	    }

	    cresDirectives(el).all().forEach(function (directive) {
	      switch (directive.type) {
	        case 'init':
	          _this.fireActionRightAway(el, directive, component);

	          break;

	        case 'model':
	          DOM.setInputValueFromModel(el, component);

	          _this.attachModelListener(el, directive, component);

	          break;

	        default:
	          if (store$1.directives.has(directive.type)) {
	            store$1.directives.call(directive.type, el, directive, component);
	          }

	          _this.attachDomListener(el, directive, component);

	          break;
	      }
	    });
	    store$1.callHook('element.initialized', el, component);
	  },
	  fireActionRightAway: function fireActionRightAway(el, directive, component) {
	    var method = directive.value ? directive.method : '$refresh';
	    component.addAction(new _default$4(method, directive.params, el));
	  },
	  attachModelListener: function attachModelListener(el, directive, component) {
	    // This is used by morphdom: morphdom.js:391
	    el.isCresenityModel = true;
	    var isLazy = directive.modifiers.includes('lazy');

	    var debounceIf = function debounceIf(condition, callback, time) {
	      return condition ? component.modelSyncDebounce(callback, time) : callback;
	    };

	    var hasDebounceModifier = directive.modifiers.includes('debounce');
	    store$1.callHook('interceptWireModelAttachListener', directive, el, component); // File uploads are handled by UploadFiles.js.

	    if (el.tagName.toLowerCase() === 'input' && el.type === 'file') {
	      return;
	    }

	    var event = el.tagName.toLowerCase() === 'select' || ['checkbox', 'radio'].includes(el.type) || directive.modifiers.includes('lazy') ? 'change' : 'input'; // If it's a text input and not .lazy, debounce, otherwise fire immediately.

	    var handler = debounceIf(hasDebounceModifier || DOM.isTextInput(el) && (!isLazy || !el.wasRecentlyAutofilled), function (e) {
	      var model = directive.value;
	      var el = e.target;
	      var value = e instanceof CustomEvent // We have to check for typeof e.detail here for IE 11.
	      && typeof e.detail != 'undefined' && typeof window.document.documentMode == 'undefined' ? e.detail : DOM.valueFromInput(el, component); // These conditions should only be met if the event was fired for a Safari autofill.

	      if (el.wasRecentlyAutofilled && e instanceof CustomEvent && e.detail === null) {
	        value = DOM.valueFromInput(el, component);
	      }

	      if (directive.modifiers.includes('defer')) {
	        component.addAction(new _default(model, value, el));
	      } else {
	        component.addAction(new _default$1(model, value, el));
	      }
	    }, directive.durationOr(150));
	    el.addEventListener(event, handler);
	    component.addListenerForTeardown(function () {
	      el.removeEventListener(event, handler);
	    });
	    el.addEventListener('animationstart', function (e) {
	      if (e.animationName !== 'cresenityautofill') {
	        return;
	      }

	      e.target.wasRecentlyAutofilled = true;
	      setTimeout(function () {
	        delete e.target.wasRecentlyAutofilled;
	      }, 1000);
	    });
	  },
	  attachDomListener: function attachDomListener(el, directive, component) {
	    switch (directive.type) {
	      case 'keydown':
	      case 'keyup':
	        this.attachListener(el, directive, component, function (e) {
	          // Detect system modifier key combinations if specified.
	          var systemKeyModifiers = ['ctrl', 'shift', 'alt', 'meta', 'cmd', 'super'];
	          var selectedSystemKeyModifiers = systemKeyModifiers.filter(function (key) {
	            return directive.modifiers.includes(key);
	          });

	          if (selectedSystemKeyModifiers.length > 0) {
	            var selectedButNotPressedKeyModifiers = selectedSystemKeyModifiers.filter(function (key) {
	              // Alias "cmd" and "super" to "meta"
	              if (key === 'cmd' || key === 'super') {
	                key = 'meta';
	              }

	              return !e["".concat(key, "Key")];
	            });

	            if (selectedButNotPressedKeyModifiers.length > 0) {
	              return false;
	            }
	          } // Handle spacebar


	          if (e.keyCode === 32 || e.key === ' ' || e.key === 'Spacebar') {
	            return directive.modifiers.includes('space');
	          } // Strip 'debounce' modifier and time modifiers from modifiers list


	          var modifiers = directive.modifiers.filter(function (modifier) {
	            return !modifier.match(/^debounce$/) && !modifier.match(/^[0-9]+m?s$/);
	          }); // Only handle listener if no, or matching key modifiers are passed.
	          // It's important to check that e.key exists - OnePassword's extension does weird things.

	          return Boolean(modifiers.length === 0 || e.key && modifiers.includes(kebabCase$1(e.key)));
	        });
	        break;

	      case 'click':
	        this.attachListener(el, directive, component, function (e) {
	          // We only care about elements that have the .self modifier on them.
	          if (!directive.modifiers.includes('self')) {
	            return;
	          } // This ensures a listener is only run if the event originated
	          // on the elemenet that registered it (not children).
	          // This is useful for things like modal back-drop listeners.


	          return el.isSameNode(e.target);
	        });
	        break;

	      default:
	        this.attachListener(el, directive, component);
	        break;
	    }
	  },
	  attachListener: function attachListener(el, directive, component, callback) {
	    var _this2 = this;

	    if (directive.modifiers.includes('prefetch')) {
	      el.addEventListener('mouseenter', function () {
	        component.addPrefetchAction(new _default$4(directive.method, directive.params, el));
	      });
	    }

	    var event = directive.type;

	    var handler = function handler(e) {
	      if (callback && callback(e) === false) {
	        return;
	      }

	      component.callAfterModelDebounce(function () {
	        var el = e.target;
	        directive.setEventContext(e); // This is outside the conditional below so "cres:click.prevent" without
	        // a value still prevents default.

	        _this2.preventAndStop(e, directive.modifiers);

	        var method = directive.method;
	        var params = directive.params;

	        if (params.length === 0 && e instanceof CustomEvent && e.detail) {
	          params.push(e.detail);
	        } // Check for global event emission.


	        if (method === '$emit') {
	          var _component$scopedList;

	          (_component$scopedList = component.scopedListeners).call.apply(_component$scopedList, _toConsumableArray(params));

	          store$1.emit.apply(store$1, _toConsumableArray(params));
	          return;
	        }

	        if (method === '$emitUp') {
	          store$1.emitUp.apply(store$1, [el].concat(_toConsumableArray(params)));
	          return;
	        }

	        if (method === '$emitSelf') {
	          store$1.emitSelf.apply(store$1, [component.id].concat(_toConsumableArray(params)));
	          return;
	        }

	        if (method === '$emitTo') {
	          store$1.emitTo.apply(store$1, _toConsumableArray(params));
	          return;
	        }

	        if (directive.value) {
	          component.addAction(new _default$4(method, params, el));
	        }
	      });
	    };

	    var debounceIf = function debounceIf(condition, callback, time) {
	      return condition ? debounce$1(callback, time) : callback;
	    };

	    var hasDebounceModifier = directive.modifiers.includes('debounce');
	    var debouncedHandler = debounceIf(hasDebounceModifier, handler, directive.durationOr(150));
	    el.addEventListener(event, debouncedHandler);
	    component.addListenerForTeardown(function () {
	      el.removeEventListener(event, debouncedHandler);
	    });
	  },
	  preventAndStop: function preventAndStop(event, modifiers) {
	    modifiers.includes('prevent') && event.preventDefault();
	    modifiers.includes('stop') && event.stopPropagation();
	  }
	};

	var PrefetchManager = /*#__PURE__*/function () {
	  function PrefetchManager(component) {
	    _classCallCheck(this, PrefetchManager);

	    this.component = component;
	    this.prefetchMessagesByActionId = {};
	  }

	  _createClass(PrefetchManager, [{
	    key: "addMessage",
	    value: function addMessage(message) {
	      this.prefetchMessagesByActionId[message.prefetchId] = message;
	    }
	  }, {
	    key: "actionHasPrefetch",
	    value: function actionHasPrefetch(action) {
	      return Object.keys(this.prefetchMessagesByActionId).includes(action.toId());
	    }
	  }, {
	    key: "actionPrefetchResponseHasBeenReceived",
	    value: function actionPrefetchResponseHasBeenReceived(action) {
	      return !!this.getPrefetchMessageByAction(action).response;
	    }
	  }, {
	    key: "getPrefetchMessageByAction",
	    value: function getPrefetchMessageByAction(action) {
	      return this.prefetchMessagesByActionId[action.toId()];
	    }
	  }, {
	    key: "clearPrefetches",
	    value: function clearPrefetches() {
	      this.prefetchMessagesByActionId = {};
	    }
	  }]);

	  return PrefetchManager;
	}();

	function LoadingStates () {
	  store$1.registerHook('component.initialized', function (component) {
	    component.targetedLoadingElsByAction = {};
	    component.genericLoadingEls = [];
	    component.currentlyActiveLoadingEls = [];
	    component.currentlyActiveUploadLoadingEls = [];
	  });
	  store$1.registerHook('element.initialized', function (el, component) {
	    var directives = cresDirectives(el);

	    if (directives.missing('loading')) {
	      return;
	    }

	    var loadingDirectives = directives.directives.filter(function (i) {
	      return i.type === 'loading';
	    });
	    loadingDirectives.forEach(function (directive) {
	      processLoadingDirective(component, el, directive);
	    });
	  });
	  store$1.registerHook('message.sent', function (message, component) {
	    var actions = message.updateQueue.filter(function (action) {
	      return action.type === 'callMethod';
	    }).map(function (action) {
	      return action.payload.method;
	    });
	    var models = message.updateQueue.filter(function (action) {
	      return action.type === 'syncInput';
	    }).map(function (action) {
	      return action.payload.name;
	    });
	    setLoading(component, actions.concat(models));
	  });
	  store$1.registerHook('message.failed', function (message, component) {
	    unsetLoading(component);
	  });
	  store$1.registerHook('message.received', function (message, component) {
	    unsetLoading(component);
	  });
	  store$1.registerHook('element.removed', function (el, component) {
	    removeLoadingEl(component, el);
	  });
	}

	function processLoadingDirective(component, el, directive) {
	  // If this element is going to be dealing with loading states.
	  // We will initialize an "undo" stack upfront, so we don't
	  // have to deal with isset() type conditionals later.
	  // eslint-disable-next-line camelcase
	  el.__cresenity_on_finish_loading = [];
	  var actionNames = false;
	  var directives = cresDirectives(el);

	  if (directives.get('target')) {
	    // cres:target overrides any automatic loading scoping we do.
	    actionNames = directives.get('target').value.split(',').map(function (s) {
	      return s.trim();
	    });
	  } else {
	    // If there is no cres:target, let's check for the existance of a cres:click="foo" or something,
	    // and automatically scope this loading directive to that action.
	    var nonActionOrModelCresenityDirectives = ['init', 'dirty', 'offline', 'target', 'loading', 'poll', 'ignore', 'key', 'id'];
	    actionNames = directives.all().filter(function (i) {
	      return !nonActionOrModelCresenityDirectives.includes(i.type);
	    }).map(function (i) {
	      return i.method;
	    }); // If we found nothing, just set the loading directive to the global component. (run on every request)

	    if (actionNames.length < 1) {
	      actionNames = false;
	    }
	  }

	  addLoadingEl(component, el, directive, actionNames);
	}

	function addLoadingEl(component, el, directive, actionsNames) {
	  if (actionsNames) {
	    actionsNames.forEach(function (actionsName) {
	      if (component.targetedLoadingElsByAction[actionsName]) {
	        component.targetedLoadingElsByAction[actionsName].push({
	          el: el,
	          directive: directive
	        });
	      } else {
	        component.targetedLoadingElsByAction[actionsName] = [{
	          el: el,
	          directive: directive
	        }];
	      }
	    });
	  } else {
	    component.genericLoadingEls.push({
	      el: el,
	      directive: directive
	    });
	  }
	}

	function removeLoadingEl(component, el) {
	  // Look through the global/generic elements for the element to remove.
	  component.genericLoadingEls.forEach(function (element, index) {
	    if (element.el.isSameNode(el)) {
	      component.genericLoadingEls.splice(index, 1);
	    }
	  }); // Look through the targeted elements to remove.

	  Object.keys(component.targetedLoadingElsByAction).forEach(function (key) {
	    component.targetedLoadingElsByAction[key] = component.targetedLoadingElsByAction[key].filter(function (element) {
	      return !element.el.isSameNode(el);
	    });
	  });
	}

	function setLoading(component, actions) {
	  var actionTargetedEls = actions.map(function (action) {
	    return component.targetedLoadingElsByAction[action];
	  }).filter(function (el) {
	    return el;
	  }).flat();
	  var allEls = component.genericLoadingEls.concat(actionTargetedEls);
	  startLoading(allEls);
	  component.currentlyActiveLoadingEls = allEls;
	}

	function setUploadLoading(component, modelName) {
	  var actionTargetedEls = component.targetedLoadingElsByAction[modelName] || [];
	  var allEls = component.genericLoadingEls.concat(actionTargetedEls);
	  startLoading(allEls);
	  component.currentlyActiveUploadLoadingEls = allEls;
	}
	function unsetUploadLoading(component) {
	  endLoading(component.currentlyActiveUploadLoadingEls);
	  component.currentlyActiveUploadLoadingEls = [];
	}

	function unsetLoading(component) {
	  endLoading(component.currentlyActiveLoadingEls);
	  component.currentlyActiveLoadingEls = [];
	}

	function startLoading(els) {
	  els.forEach(function (_ref) {
	    var el = _ref.el,
	        directive = _ref.directive;

	    if (directive.modifiers.includes('class')) {
	      var classes = directive.value.split(' ').filter(Boolean);
	      doAndSetCallbackOnElToUndo(el, directive, function () {
	        var _el$classList;

	        return (_el$classList = el.classList).add.apply(_el$classList, _toConsumableArray(classes));
	      }, function () {
	        var _el$classList2;

	        return (_el$classList2 = el.classList).remove.apply(_el$classList2, _toConsumableArray(classes));
	      });
	    } else if (directive.modifiers.includes('attr')) {
	      doAndSetCallbackOnElToUndo(el, directive, function () {
	        return el.setAttribute(directive.value, true);
	      }, function () {
	        return el.removeAttribute(directive.value);
	      });
	    } else {
	      var cache = window.getComputedStyle(el, null).getPropertyValue('display');
	      doAndSetCallbackOnElToUndo(el, directive, function () {
	        el.style.display = directive.modifiers.includes('remove') ? cache : getDisplayProperty(directive);
	      }, function () {
	        el.style.display = 'none';
	      });
	    }
	  });
	}

	function getDisplayProperty(directive) {
	  return ['inline', 'block', 'table', 'flex', 'grid'].filter(function (i) {
	    return directive.modifiers.includes(i);
	  })[0] || 'inline-block';
	}

	function doAndSetCallbackOnElToUndo(el, directive, doCallback, undoCallback) {
	  if (directive.modifiers.includes('remove')) {
	    var _ref2 = [undoCallback, doCallback];
	    doCallback = _ref2[0];
	    undoCallback = _ref2[1];
	  }

	  if (directive.modifiers.includes('delay')) {
	    var timeout = setTimeout(function () {
	      doCallback();

	      el.__cresenity_on_finish_loading.push(function () {
	        return undoCallback();
	      });
	    }, 200);

	    el.__cresenity_on_finish_loading.push(function () {
	      return clearTimeout(timeout);
	    });
	  } else {
	    doCallback();

	    el.__cresenity_on_finish_loading.push(function () {
	      return undoCallback();
	    });
	  }
	}

	function endLoading(els) {
	  els.forEach(function (_ref3) {
	    var el = _ref3.el;

	    while (el.__cresenity_on_finish_loading.length > 0) {
	      el.__cresenity_on_finish_loading.shift()();
	    }
	  });
	}

	var MessageBag = /*#__PURE__*/function () {
	  function MessageBag() {
	    _classCallCheck(this, MessageBag);

	    this.bag = {};
	  }

	  _createClass(MessageBag, [{
	    key: "add",
	    value: function add(name, thing) {
	      if (!this.bag[name]) {
	        this.bag[name] = [];
	      }

	      this.bag[name].push(thing);
	    }
	  }, {
	    key: "push",
	    value: function push(name, thing) {
	      this.add(name, thing);
	    }
	  }, {
	    key: "first",
	    value: function first(name) {
	      if (!this.bag[name]) {
	        return null;
	      }

	      return this.bag[name][0];
	    }
	  }, {
	    key: "last",
	    value: function last(name) {
	      return this.bag[name].slice(-1)[0];
	    }
	  }, {
	    key: "get",
	    value: function get(name) {
	      return this.bag[name];
	    }
	  }, {
	    key: "shift",
	    value: function shift(name) {
	      return this.bag[name].shift();
	    }
	  }, {
	    key: "call",
	    value: function call(name) {
	      for (var _len = arguments.length, params = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
	        params[_key - 1] = arguments[_key];
	      }

	      (this.listeners[name] || []).forEach(function (callback) {
	        callback.apply(void 0, params);
	      });
	    }
	  }, {
	    key: "has",
	    value: function has(name) {
	      return Object.keys(this.listeners).includes(name);
	    }
	  }]);

	  return MessageBag;
	}();

	var UploadManager = /*#__PURE__*/function () {
	  function UploadManager(component) {
	    _classCallCheck(this, UploadManager);

	    this.component = component;
	    this.uploadBag = new MessageBag();
	    this.removeBag = new MessageBag();
	  }

	  _createClass(UploadManager, [{
	    key: "registerListeners",
	    value: function registerListeners() {
	      var _this = this;

	      this.component.on('upload:generatedSignedUrl', function (name, url) {
	        // We have to add reduntant "setLoading" calls because the dom-patch
	        // from the first response will clear the setUploadLoading call
	        // from the first upload call.
	        setUploadLoading(_this.component, name);

	        _this.handleSignedUrl(name, url);
	      });
	      this.component.on('upload:generatedSignedUrlForS3', function (name, payload) {
	        setUploadLoading(_this.component, name);

	        _this.handleS3PreSignedUrl(name, payload);
	      });
	      this.component.on('upload:finished', function (name, tmpFilenames) {
	        return _this.markUploadFinished(name, tmpFilenames);
	      });
	      this.component.on('upload:errored', function (name) {
	        return _this.markUploadErrored(name);
	      });
	      this.component.on('upload:removed', function (name, tmpFilename) {
	        return _this.removeBag.shift(name).finishCallback(tmpFilename);
	      });
	    }
	  }, {
	    key: "upload",
	    value: function upload(name, file, finishCallback, errorCallback, progressCallback) {
	      this.setUpload(name, {
	        files: [file],
	        multiple: false,
	        finishCallback: finishCallback,
	        errorCallback: errorCallback,
	        progressCallback: progressCallback
	      });
	    }
	  }, {
	    key: "uploadMultiple",
	    value: function uploadMultiple(name, files, finishCallback, errorCallback, progressCallback) {
	      this.setUpload(name, {
	        files: Array.from(files),
	        multiple: true,
	        finishCallback: finishCallback,
	        errorCallback: errorCallback,
	        progressCallback: progressCallback
	      });
	    }
	  }, {
	    key: "removeUpload",
	    value: function removeUpload(name, tmpFilename, finishCallback) {
	      this.removeBag.push(name, {
	        tmpFilename: tmpFilename,
	        finishCallback: finishCallback
	      });
	      this.component.call('removeUpload', name, tmpFilename);
	    }
	  }, {
	    key: "setUpload",
	    value: function setUpload(name, uploadObject) {
	      this.uploadBag.add(name, uploadObject);

	      if (this.uploadBag.get(name).length === 1) {
	        this.startUpload(name, uploadObject);
	      }
	    }
	  }, {
	    key: "handleSignedUrl",
	    value: function handleSignedUrl(name, url) {
	      var formData = new FormData();
	      Array.from(this.uploadBag.first(name).files).forEach(function (file) {
	        return formData.append('files[]', file);
	      });
	      var headers = {
	        'X-CSRF-TOKEN': getCsrfToken(),
	        'Accept': 'application/json'
	      };
	      this.makeRequest(name, formData, 'post', url, headers, function (response) {
	        return response.paths;
	      });
	    }
	  }, {
	    key: "handleS3PreSignedUrl",
	    value: function handleS3PreSignedUrl(name, payload) {
	      var formData = this.uploadBag.first(name).files[0];
	      var headers = payload.headers;
	      if ('Host' in headers) delete headers.Host;
	      var url = payload.url;
	      this.makeRequest(name, formData, 'put', url, headers, function (response) {
	        return [payload.path];
	      });
	    }
	  }, {
	    key: "makeRequest",
	    value: function makeRequest(name, formData, method, url, headers, retrievePaths) {
	      var _this2 = this;

	      var request = new XMLHttpRequest();
	      request.open(method, url);
	      Object.entries(headers).forEach(function (_ref) {
	        var _ref2 = _slicedToArray(_ref, 2),
	            key = _ref2[0],
	            value = _ref2[1];

	        request.setRequestHeader(key, value);
	      });
	      request.upload.addEventListener('progress', function (e) {
	        e.detail = {};
	        e.detail.progress = Math.round(e.loaded * 100 / e.total);

	        _this2.uploadBag.first(name).progressCallback(e);
	      });
	      request.addEventListener('load', function () {
	        if ((request.status + '')[0] === '2') {
	          var paths = retrievePaths(request.response && JSON.parse(request.response));

	          _this2.component.call('finishUpload', name, paths, _this2.uploadBag.first(name).multiple);

	          return;
	        }

	        var errors = null;

	        if (request.status === 422) {
	          errors = request.response;
	        }

	        _this2.component.call('uploadErrored', name, errors, _this2.uploadBag.first(name).multiple);
	      });
	      request.send(formData);
	    }
	  }, {
	    key: "startUpload",
	    value: function startUpload(name, uploadObject) {
	      var fileInfos = uploadObject.files.map(function (file) {
	        return {
	          name: file.name,
	          size: file.size,
	          type: file.type
	        };
	      });
	      this.component.call('startUpload', name, fileInfos, uploadObject.multiple);
	      setUploadLoading(this.component, name);
	    }
	  }, {
	    key: "markUploadFinished",
	    value: function markUploadFinished(name, tmpFilenames) {
	      unsetUploadLoading(this.component);
	      var uploadObject = this.uploadBag.shift(name);
	      uploadObject.finishCallback(uploadObject.multiple ? tmpFilenames : tmpFilenames[0]);
	      if (this.uploadBag.get(name).length > 0) this.startUpload(name, this.uploadBag.last(name));
	    }
	  }, {
	    key: "markUploadErrored",
	    value: function markUploadErrored(name) {
	      unsetUploadLoading(this.component);
	      this.uploadBag.shift(name).errorCallback();
	      if (this.uploadBag.get(name).length > 0) this.startUpload(name, this.uploadBag.last(name));
	    }
	  }]);

	  return UploadManager;
	}();

	function SupportAlpine () {
	  window.addEventListener('cresenity:ui:start', function () {
	    if (!window.Alpine) {
	      return;
	    }

	    refreshAlpineAfterEveryCresenityRequest();
	    addDollarSignCres();
	    supportEntangle();
	  });
	}

	function refreshAlpineAfterEveryCresenityRequest() {
	  if (isV3()) {
	    store$1.registerHook('message.processed', function (message, cresenityComponent) {
	      walk$1(cresenityComponent.el, function (el) {
	        if (el._x_hidePromise) {
	          return;
	        }

	        if (el._x_runEffects) {
	          el._x_runEffects();
	        }
	      });
	    });
	    return;
	  }

	  if (!window.Alpine.onComponentInitialized) {
	    return;
	  }

	  window.Alpine.onComponentInitialized(function (component) {
	    var cresenityEl = component.$el.closest('[cres\\:id]');

	    if (cresenityEl && cresenityEl.__cresenity) {
	      store$1.registerHook('message.processed', function (message, cresenityComponent) {
	        if (cresenityComponent === cresenityEl.__cresenity) {
	          component.updateElements(component.$el);
	        }
	      });
	    }
	  });
	}

	function addDollarSignCres() {
	  if (isV3()) {
	    window.Alpine.magic('cres', function (el) {
	      var cresEl = el.closest('[cres\\:id]');

	      if (!cresEl) {
	        console.warn('Alpine: Cannot reference "$cres" outside a Cresenity component.');
	      }

	      var component = cresEl.__cresenity;
	      return component.$cres;
	    });
	    return;
	  }

	  if (!window.Alpine.addMagicProperty) {
	    return;
	  }

	  window.Alpine.addMagicProperty('cres', function (componentEl) {
	    var cresEl = componentEl.closest('[cres\\:id]');

	    if (!cresEl) {
	      console.warn('Alpine: Cannot reference "$cres" outside a Cresenity component.');
	    }

	    var component = cresEl.__cresenity;
	    return component.$cres;
	  });
	}

	function supportEntangle() {
	  if (isV3()) {
	    return;
	  }

	  if (!window.Alpine.onBeforeComponentInitialized) {
	    return;
	  }

	  window.Alpine.onBeforeComponentInitialized(function (component) {
	    var cresenityEl = component.$el.closest('[cres\\:id]');

	    if (cresenityEl && cresenityEl.__cresenity) {
	      Object.entries(component.unobservedData).forEach(function (_ref) {
	        var _ref2 = _slicedToArray(_ref, 2),
	            key = _ref2[0],
	            value = _ref2[1];

	        if (!!value && _typeof$1(value) === 'object' && value.cresenityEntangle) {
	          // Ok, it looks like someone set an Alpine property to $cres.entangle or @entangle.
	          var cresenityProperty = value.cresenityEntangle;
	          var isDeferred = value.isDeferred;
	          var cresenityComponent = cresenityEl.__cresenity;

	          var cresenityPropertyValue = cresenityEl.__cresenity.get(cresenityProperty); // Check to see if the Cresenity property exists and if not log a console error
	          // and return so everything else keeps running.


	          if (typeof cresenityPropertyValue === 'undefined') {
	            console.error("Cresenity Component Entangle Error: Cresenity Component property '".concat(cresenityProperty, "' cannot be found"));
	            return;
	          } // Let's set the initial value of the Alpine prop to the Cresenity prop's value.


	          component.unobservedData[key] // We need to stringify and parse it though to get a deep clone.
	          = JSON.parse(JSON.stringify(cresenityPropertyValue));
	          var blockAlpineWatcher = false; // Now, we'll watch for changes to the Alpine prop, and fire the update to Cresenity Component.

	          component.unobservedData.$watch(key, function (value) {
	            // Let's also make sure that this watcher isn't a result of a Cresenity Component response.
	            // If it is, we don't need to "re-update" Cresenity Component. (sending an extra useless) request.
	            if (blockAlpineWatcher === true) {
	              blockAlpineWatcher = false;
	              return;
	            } // If the Alpine value is the same as the Cresenity Component value, we'll skip the update for 2 reasons:
	            // - It's just more efficient, why send needless requests.
	            // - This prevents a circular dependancy with the other watcher below.
	            // - Due to the deep clone using stringify, we need to do the same here to compare.


	            if (JSON.stringify(value) == JSON.stringify(cresenityEl.__cresenity.getPropertyValueIncludingDefers(cresenityProperty))) {
	              return;
	            } // We'll tell Cresenity Component to update the property, but we'll also tell Cresenity Component
	            // to not call the normal property watchers on the way back to prevent another
	            // circular dependancy.


	            cresenityComponent.set(cresenityProperty, value, isDeferred, // Block firing of Cresenity Component watchers for this data key when the request comes back.
	            // Unless it is deferred, in which cause we don't know if the state will be the same, so let it run.
	            isDeferred ? false : true);
	          }); // We'll also listen for changes to the Cresenity Component prop, and set them in Alpine.

	          cresenityComponent.watch(cresenityProperty, function (value) {
	            // Ensure data is deep cloned otherwise Alpine mutates Cresenity Component data
	            component.$data[key] = typeof value !== 'undefined' ? JSON.parse(JSON.stringify(value)) : value;
	          });
	        }
	      });
	    }
	  });
	}
	function alpinifyElementsForMorphdom(from, to) {
	  if (isV3()) {
	    return alpinifyElementsForMorphdomV3(from, to);
	  } // If the element we are updating is an Alpine component...


	  if (from.__x) {
	    // Then temporarily clone it (with it's data) to the "to" element.
	    // This should simulate backend Cresenity being aware of Alpine changes.
	    window.Alpine.clone(from.__x, to);
	  } // x-show elements require care because of transitions.


	  if (Array.from(from.attributes).map(function (attr) {
	    return attr.name;
	  }).some(function (name) {
	    return /x-show/.test(name);
	  })) {
	    if (from.__x_transition) {
	      // This covers @entangle('something')
	      from.skipElUpdatingButStillUpdateChildren = true;
	    } else {
	      // This covers x-show="$cres.something"
	      //
	      // If the element has x-show, we need to "reverse" the damage done by "clone",
	      // so that if/when the element has a transition on it, it will occur naturally.
	      if (isHiding(from, to)) {
	        var style = to.getAttribute('style');

	        if (style) {
	          to.setAttribute('style', style.replace('display: none;', ''));
	        }
	      } else if (isShowing(from, to)) {
	        to.style.display = from.style.display;
	      }
	    }
	  }
	}

	function alpinifyElementsForMorphdomV3(from, to) {
	  if (from.nodeType !== 1) {
	    return;
	  } // If the element we are updating is an Alpine component...


	  if (from._x_dataStack) {
	    // Then temporarily clone it (with it's data) to the "to" element.
	    // This should simulate backend Cresenity Component being aware of Alpine changes.
	    window.Alpine.clone(from, to);
	  }
	}

	function isHiding(from, to) {
	  if (beforeAlpineTwoPointSevenPointThree()) {
	    return from.style.display === '' && to.style.display === 'none';
	  }

	  return from.__x_is_shown && !to.__x_is_shown;
	}

	function isShowing(from, to) {
	  if (beforeAlpineTwoPointSevenPointThree()) {
	    return from.style.display === 'none' && to.style.display === '';
	  }

	  return !from.__x_is_shown && to.__x_is_shown;
	}

	function beforeAlpineTwoPointSevenPointThree() {
	  var _window$Alpine$versio = window.Alpine.version.split('.').map(function (i) {
	    return Number(i);
	  }),
	      _window$Alpine$versio2 = _slicedToArray(_window$Alpine$versio, 3),
	      major = _window$Alpine$versio2[0],
	      minor = _window$Alpine$versio2[1],
	      patch = _window$Alpine$versio2[2];

	  return major <= 2 && minor <= 7 && patch <= 2;
	}

	function isV3() {
	  return window.Alpine && window.Alpine.version && /^3\..+\..+$/.test(window.Alpine.version);
	}

	var Component = /*#__PURE__*/function () {
	  function Component(el, connection) {
	    _classCallCheck(this, Component);

	    el.__cresenity = this;
	    this.el = el;
	    this.lastFreshHtml = this.el.outerHTML;
	    this.id = this.el.getAttribute('cres:id');
	    this.connection = connection;
	    var initialData = JSON.parse(this.el.getAttribute('cres:initial-data'));
	    this.el.removeAttribute('cres:initial-data');
	    this.fingerprint = initialData.fingerprint;
	    this.serverMemo = initialData.serverMemo;
	    this.effects = initialData.effects;
	    this.listeners = this.effects.listeners;
	    this.updateQueue = [];
	    this.deferredActions = {};
	    this.tearDownCallbacks = [];
	    this.messageInTransit = undefined;
	    this.scopedListeners = new MessageBus();
	    this.prefetchManager = new PrefetchManager(this);
	    this.uploadManager = new UploadManager(this);
	    this.watchers = {};
	    store$1.callHook('component.initialized', this);
	    this.initialize();
	    this.uploadManager.registerListeners();

	    if (this.effects.redirect) {
	      return this.redirect(this.effects.redirect);
	    }
	  }

	  _createClass(Component, [{
	    key: "name",
	    get: function get() {
	      return this.fingerprint.name;
	    }
	  }, {
	    key: "data",
	    get: function get() {
	      return this.serverMemo.data;
	    }
	  }, {
	    key: "childIds",
	    get: function get() {
	      return Object.values(this.serverMemo.children).map(function (child) {
	        return child.id;
	      });
	    }
	  }, {
	    key: "initialize",
	    value: function initialize() {
	      var _this = this;

	      this.walk( // Will run for every node in the component tree (not child component nodes).
	      function (el) {
	        return nodeInitializer.initialize(el, _this);
	      }, // When new component is encountered in the tree, add it.
	      function (el) {
	        return store$1.addComponent(new Component(el, _this.connection));
	      });
	    }
	  }, {
	    key: "get",
	    value: function get(name) {
	      // The .split() stuff is to support dot-notation.
	      return name.split('.').reduce(function (carry, segment) {
	        return carry[segment];
	      }, this.data);
	    }
	  }, {
	    key: "getPropertyValueIncludingDefers",
	    value: function getPropertyValueIncludingDefers(name) {
	      var action = this.deferredActions[name];

	      if (!action) {
	        return this.get(name);
	      }

	      return action.payload.value;
	    }
	  }, {
	    key: "updateServerMemoFromResponseAndMergeBackIntoResponse",
	    value: function updateServerMemoFromResponseAndMergeBackIntoResponse(message) {
	      var _this2 = this;

	      // We have to do a fair amount of object merging here, but we can't use expressive syntax like {...}
	      // because browsers mess with the object key order which will break Cresenity request checksum checks.
	      Object.entries(message.response.serverMemo).forEach(function (_ref) {
	        var _ref2 = _slicedToArray(_ref, 2),
	            key = _ref2[0],
	            value = _ref2[1];

	        // Because "data" is "partial" from the server, we have to deep merge it.
	        if (key === 'data') {
	          Object.entries(value || {}).forEach(function (_ref3) {
	            var _ref4 = _slicedToArray(_ref3, 2),
	                dataKey = _ref4[0],
	                dataValue = _ref4[1];

	            _this2.serverMemo.data[dataKey] = dataValue;

	            if (message.shouldSkipWatcherForDataKey(dataKey)) {
	              return;
	            } // Because Cresenity (for payload reduction purposes) only returns the data that has changed,
	            // we can use all the data keys from the response as watcher triggers.


	            Object.entries(_this2.watchers).forEach(function (_ref5) {
	              var _ref6 = _slicedToArray(_ref5, 2),
	                  key = _ref6[0],
	                  watchers = _ref6[1];

	              var originalSplitKey = key.split('.');
	              var basePropertyName = originalSplitKey.shift();
	              var restOfPropertyName = originalSplitKey.join('.');

	              if (basePropertyName == dataKey) {
	                // If the key deals with nested data, use the "get" function to get
	                // the most nested data. Otherwise, return the entire data chunk.
	                var potentiallyNestedValue = restOfPropertyName ? getValue(dataValue, restOfPropertyName) : dataValue;
	                watchers.forEach(function (watcher) {
	                  return watcher(potentiallyNestedValue);
	                });
	              }
	            });
	          });
	        } else {
	          // Every other key, we can just overwrite.
	          _this2.serverMemo[key] = value;
	        }
	      }); // Merge back serverMemo changes so the response data is no longer incomplete.

	      message.response.serverMemo = Object.assign({}, this.serverMemo);
	    }
	  }, {
	    key: "watch",
	    value: function watch(name, callback) {
	      if (!this.watchers[name]) {
	        this.watchers[name] = [];
	      }

	      this.watchers[name].push(callback);
	    }
	  }, {
	    key: "set",
	    value: function set(name, value) {
	      var defer = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
	      var skipWatcher = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;

	      if (defer) {
	        this.addAction(new _default(name, value, this.el, skipWatcher));
	      } else {
	        this.addAction(new _default$4('$set', [name, value], this.el, skipWatcher));
	      }
	    }
	  }, {
	    key: "sync",
	    value: function sync(name, value) {
	      var defer = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

	      if (defer) {
	        this.addAction(new _default(name, value, this.el));
	      } else {
	        this.addAction(new _default$1(name, value, this.el));
	      }
	    }
	  }, {
	    key: "call",
	    value: function call(method) {
	      var _this3 = this;

	      for (var _len = arguments.length, params = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
	        params[_key - 1] = arguments[_key];
	      }

	      return new Promise(function (resolve, reject) {
	        var action = new _default$4(method, params, _this3.el);

	        _this3.addAction(action);

	        action.onResolve(function (thing) {
	          return resolve(thing);
	        });
	        action.onReject(function (thing) {
	          return reject(thing);
	        });
	      });
	    }
	  }, {
	    key: "on",
	    value: function on(event, callback) {
	      this.scopedListeners.register(event, callback);
	    }
	  }, {
	    key: "addAction",
	    value: function addAction(action) {
	      if (action instanceof _default) {
	        this.deferredActions[action.name] = action;
	        return;
	      }

	      if (this.prefetchManager.actionHasPrefetch(action) && this.prefetchManager.actionPrefetchResponseHasBeenReceived(action)) {
	        var message = this.prefetchManager.getPrefetchMessageByAction(action);
	        this.handleResponse(message);
	        this.prefetchManager.clearPrefetches();
	        return;
	      }

	      this.updateQueue.push(action); // This debounce is here in-case two events fire at the "same" time:
	      // For example: if you are listening for a click on element A,
	      // and a "blur" on element B. If element B has focus, and then,
	      // you click on element A, the blur event will fire before the "click"
	      // event. This debounce captures them both in the actionsQueue and sends
	      // them off at the same time.
	      // Note: currently, it's set to 5ms, that might not be the right amount, we'll see.

	      debounce$1(this.fireMessage, 5).apply(this); // Clear prefetches.

	      this.prefetchManager.clearPrefetches();
	    }
	  }, {
	    key: "fireMessage",
	    value: function fireMessage() {
	      var _this4 = this;

	      if (this.messageInTransit) {
	        return;
	      }

	      Object.entries(this.deferredActions).forEach(function (_ref7) {
	        var _ref8 = _slicedToArray(_ref7, 2);
	            _ref8[0];
	            var action = _ref8[1];

	        _this4.updateQueue.unshift(action);
	      });
	      this.deferredActions = {};
	      this.messageInTransit = new _default$3(this, this.updateQueue);

	      var sendMessage = function sendMessage() {
	        _this4.connection.sendMessage(_this4.messageInTransit);

	        store$1.callHook('message.sent', _this4.messageInTransit, _this4);
	        _this4.updateQueue = [];
	      };

	      if (window.capturedRequestsForDusk) {
	        window.capturedRequestsForDusk.push(sendMessage);
	      } else {
	        sendMessage();
	      }
	    }
	  }, {
	    key: "messageSendFailed",
	    value: function messageSendFailed() {
	      store$1.callHook('message.failed', this.messageInTransit, this);
	      this.messageInTransit.reject();
	      this.messageInTransit = null;
	    }
	  }, {
	    key: "receiveMessage",
	    value: function receiveMessage(message, payload) {
	      message.storeResponse(payload);

	      if (message instanceof _default$2) {
	        return;
	      }

	      this.handleResponse(message); // This bit of logic ensures that if actions were queued while a request was
	      // out to the server, they are sent when the request comes back.

	      if (this.updateQueue.length > 0) {
	        this.fireMessage();
	      }

	      dispatch$1('cresenity:update');
	    }
	  }, {
	    key: "handleResponse",
	    value: function handleResponse(message) {
	      var _this5 = this;

	      var response = message.response; // This means "$this->redirect()" was called in the component. let's just bail and redirect.

	      if (response.effects.redirect) {
	        this.redirect(response.effects.redirect);
	        return;
	      }

	      this.updateServerMemoFromResponseAndMergeBackIntoResponse(message);
	      store$1.callHook('message.received', message, this);

	      if (response.effects.html) {
	        // If we get HTML from the server, store it for the next time we might not.
	        this.lastFreshHtml = response.effects.html;
	        this.handleMorph(response.effects.html.trim());
	      } else {
	        // It's important to still "morphdom" even when the server HTML hasn't changed,
	        // because Alpine needs to be given the chance to update.
	        this.handleMorph(this.lastFreshHtml);
	      }

	      if (response.effects.dirty) {
	        this.forceRefreshDataBoundElementsMarkedAsDirty(response.effects.dirty);
	      }

	      if (!message.replaying) {
	        this.messageInTransit && this.messageInTransit.resolve();
	        this.messageInTransit = null;

	        if (response.effects.emits && response.effects.emits.length > 0) {
	          response.effects.emits.forEach(function (event) {
	            var _this5$scopedListener;

	            (_this5$scopedListener = _this5.scopedListeners).call.apply(_this5$scopedListener, [event.event].concat(_toConsumableArray(event.params)));

	            if (event.selfOnly) {
	              store$1.emitSelf.apply(store$1, [_this5.id, event.event].concat(_toConsumableArray(event.params)));
	            } else if (event.to) {
	              store$1.emitTo.apply(store$1, [event.to, event.event].concat(_toConsumableArray(event.params)));
	            } else if (event.ancestorsOnly) {
	              store$1.emitUp.apply(store$1, [_this5.el, event.event].concat(_toConsumableArray(event.params)));
	            } else {
	              store$1.emit.apply(store$1, [event.event].concat(_toConsumableArray(event.params)));
	            }
	          });
	        }

	        if (response.effects.dispatches && response.effects.dispatches.length > 0) {
	          response.effects.dispatches.forEach(function (event) {
	            var data = event.data ? event.data : {};
	            var e = new CustomEvent(event.event, {
	              bubbles: true,
	              detail: data
	            });

	            _this5.el.dispatchEvent(e);
	          });
	        }
	      }

	      store$1.callHook('message.processed', message, this);
	    }
	  }, {
	    key: "redirect",
	    value: function redirect(url) {
	      if (window.Turbolinks && window.Turbolinks.supported) {
	        window.Turbolinks.visit(url);
	      } else {
	        window.location.href = url;
	      }
	    }
	  }, {
	    key: "forceRefreshDataBoundElementsMarkedAsDirty",
	    value: function forceRefreshDataBoundElementsMarkedAsDirty(dirtyInputs) {
	      var _this6 = this;

	      this.walk(function (el) {
	        var directives = cresDirectives(el);

	        if (directives.missing('model')) {
	          return;
	        }

	        var modelValue = directives.get('model').value;

	        if (DOM.hasFocus(el) && !dirtyInputs.includes(modelValue)) {
	          return;
	        }

	        if (el.wasRecentlyAutofilled) {
	          return;
	        }

	        DOM.setInputValueFromModel(el, _this6);
	      });
	    }
	  }, {
	    key: "addPrefetchAction",
	    value: function addPrefetchAction(action) {
	      if (this.prefetchManager.actionHasPrefetch(action)) {
	        return;
	      }

	      var message = new _default$2(this, action);
	      this.prefetchManager.addMessage(message);
	      this.connection.sendMessage(message);
	    }
	  }, {
	    key: "handleMorph",
	    value: function handleMorph(dom) {
	      var _this7 = this;

	      this.morphChanges = {
	        changed: [],
	        added: [],
	        removed: []
	      };
	      morphdom(this.el, dom, {
	        childrenOnly: false,
	        getNodeKey: function getNodeKey(node) {
	          // This allows the tracking of elements by the "key" attribute, like in VueJs.
	          return node.hasAttribute('cres:key') ? node.getAttribute('cres:key') : // If no "key", then first check for "cres:id", then "id"
	          node.hasAttribute('cres:id') ? node.getAttribute('cres:id') : node.id;
	        },
	        onBeforeNodeAdded: function onBeforeNodeAdded(node) {//
	        },
	        onBeforeNodeDiscarded: function onBeforeNodeDiscarded(node) {
	          // If the node is from x-if with a transition.
	          if (node.__x_inserted_me && Array.from(node.attributes).some(function (attr) {
	            return /x-transition/.test(attr.name);
	          })) {
	            return false;
	          }
	        },
	        onNodeDiscarded: function onNodeDiscarded(node) {
	          store$1.callHook('element.removed', node, _this7);

	          if (node.__cresenity) {
	            store$1.removeComponent(node.__cresenity);
	          }

	          _this7.morphChanges.removed.push(node);
	        },
	        onBeforeElChildrenUpdated: function onBeforeElChildrenUpdated(node) {//
	        },
	        onBeforeElUpdated: function onBeforeElUpdated(from, to) {
	          // Because morphdom also supports vDom nodes, it uses isSameNode to detect
	          // sameness. When dealing with DOM nodes, we want isEqualNode, otherwise
	          // isSameNode will ALWAYS return false.
	          if (from.isEqualNode(to)) {
	            return false;
	          }

	          store$1.callHook('element.updating', from, to, _this7); // Reset the index of cres:modeled select elements in the
	          // "to" node before doing the diff, so that the options
	          // have the proper in-memory .selected value set.

	          if (from.hasAttribute('cres:model') && from.tagName.toUpperCase() === 'SELECT') {
	            to.selectedIndex = -1;
	          }

	          var fromDirectives = cresDirectives(from); // Honor the "cres:ignore" attribute or the .__cresenity_ignore element property.

	          if (fromDirectives.has('ignore') || from.__cresenity_ignore === true || from.__cresenity_ignore_self === true) {
	            if (fromDirectives.has('ignore') && fromDirectives.get('ignore').modifiers.includes('self') || from.__cresenity_ignore_self === true) {
	              // Don't update children of "cres:ingore.self" attribute.
	              from.skipElUpdatingButStillUpdateChildren = true;
	            } else {
	              return false;
	            }
	          } // Children will update themselves.


	          if (DOM.isComponentRootEl(from) && from.getAttribute('cres:id') !== _this7.id) {
	            return false;
	          } // Give the root Cresenity "to" element, the same object reference as the "from"
	          // element. This ensures new Alpine magics like $cres and @entangle can
	          // initialize in the context of a real Cresenity component object.


	          if (DOM.isComponentRootEl(from)) {
	            to.__cresenity = _this7;
	          }

	          alpinifyElementsForMorphdom(from, to);
	        },
	        onElUpdated: function onElUpdated(node) {
	          _this7.morphChanges.changed.push(node);

	          store$1.callHook('element.updated', node, _this7);
	        },
	        onNodeAdded: function onNodeAdded(node) {
	          var closestComponentId = DOM.closestRoot(node).getAttribute('cres:id');

	          if (closestComponentId === _this7.id) {
	            if (nodeInitializer.initialize(node, _this7) === false) {
	              return false;
	            }
	          } else if (DOM.isComponentRootEl(node)) {
	            store$1.addComponent(new Component(node, _this7.connection)); // We don't need to initialize children, the
	            // new Component constructor will do that for us.

	            node.skipAddingChildren = true;
	          }

	          _this7.morphChanges.added.push(node);
	        }
	      });
	      window.skipShow = false;
	    }
	  }, {
	    key: "walk",
	    value: function walk(callback) {
	      var _this8 = this;

	      var callbackWhenNewComponentIsEncountered = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : function (el) {};

	      walk$1(this.el, function (el) {
	        // Skip the root component element.
	        if (el.isSameNode(_this8.el)) {
	          callback(el);
	          return;
	        } // If we encounter a nested component, skip walking that tree.


	        if (el.hasAttribute('cres:id')) {
	          callbackWhenNewComponentIsEncountered(el);
	          return false;
	        }

	        if (callback(el) === false) {
	          return false;
	        }
	      });
	    }
	  }, {
	    key: "modelSyncDebounce",
	    value: function modelSyncDebounce(callback, time) {
	      // Prepare yourself for what's happening here.
	      // Any text input with cres:model on it should be "debounced" by ~150ms by default.
	      // We can't use a simple debounce function because we need a way to clear all the pending
	      // debounces if a user submits a form or performs some other action.
	      // This is a modified debounce function that acts just like a debounce, except it stores
	      // the pending callbacks in a global property so we can "clear them" on command instead
	      // of waiting for their setTimeouts to expire. I know.
	      if (!this.modelDebounceCallbacks) {
	        this.modelDebounceCallbacks = [];
	      } // This is a "null" callback. Each cres:model will resister one of these upon initialization.


	      var callbackRegister = {
	        callback: function callback() {}
	      };
	      this.modelDebounceCallbacks.push(callbackRegister); // This is a normal "timeout" for a debounce function.

	      var timeout;
	      return function (e) {
	        clearTimeout(timeout);
	        timeout = setTimeout(function () {
	          callback(e);
	          timeout = undefined; // Because we just called the callback, let's return the
	          // callback register to it's normal "null" state.

	          callbackRegister.callback = function () {};
	        }, time); // Register the current callback in the register as a kind-of "escape-hatch".

	        callbackRegister.callback = function () {
	          clearTimeout(timeout);
	          callback(e);
	        };
	      };
	    }
	  }, {
	    key: "callAfterModelDebounce",
	    value: function callAfterModelDebounce(callback) {
	      // This is to protect against the following scenario:
	      // A user is typing into a debounced input, and hits the enter key.
	      // If the enter key submits a form or something, the submission
	      // will happen BEFORE the model input finishes syncing because
	      // of the debounce. This makes sure to clear anything in the debounce queue.
	      if (this.modelDebounceCallbacks) {
	        this.modelDebounceCallbacks.forEach(function (callbackRegister) {
	          callbackRegister.callback();

	          callbackRegister = function callbackRegister() {};
	        });
	      }

	      callback();
	    }
	  }, {
	    key: "addListenerForTeardown",
	    value: function addListenerForTeardown(teardownCallback) {
	      this.tearDownCallbacks.push(teardownCallback);
	    }
	  }, {
	    key: "tearDown",
	    value: function tearDown() {
	      this.tearDownCallbacks.forEach(function (callback) {
	        return callback();
	      });
	    }
	  }, {
	    key: "upload",
	    value: function upload(name, file) {
	      var finishCallback = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : function () {};
	      var errorCallback = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : function () {};
	      var progressCallback = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : function () {};
	      this.uploadManager.upload(name, file, finishCallback, errorCallback, progressCallback);
	    }
	  }, {
	    key: "uploadMultiple",
	    value: function uploadMultiple(name, files) {
	      var finishCallback = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : function () {};
	      var errorCallback = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : function () {};
	      var progressCallback = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : function () {};
	      this.uploadManager.uploadMultiple(name, files, finishCallback, errorCallback, progressCallback);
	    }
	  }, {
	    key: "removeUpload",
	    value: function removeUpload(name, tmpFilename) {
	      var finishCallback = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : function () {};
	      var errorCallback = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : function () {};
	      this.uploadManager.removeUpload(name, tmpFilename, finishCallback, errorCallback);
	    }
	  }, {
	    key: "$cres",
	    get: function get() {
	      if (this.dollarWireProxy) {
	        return this.dollarWireProxy;
	      }

	      var refObj = {};
	      var component = this;
	      return this.dollarWireProxy = new Proxy(refObj, {
	        get: function get(object, property) {
	          if (property === 'entangle') {
	            return function (name) {
	              var defer = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
	              return {
	                isDeferred: defer,
	                cresenityEntangle: name,

	                get defer() {
	                  this.isDeferred = true;
	                  return this;
	                }

	              };
	            };
	          }

	          if (property === '__instance') {
	            return component;
	          } // Forward "emits" to base Cresenity object.


	          if (typeof property === 'string' && property.match(/^emit.*/)) {
	            return function () {
	              for (var _len2 = arguments.length, args = new Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
	                args[_key2] = arguments[_key2];
	              }

	              if (property === 'emitSelf') {
	                return store$1.emitSelf.apply(store$1, [component.id].concat(args));
	              }

	              return store$1[property].apply(component, args);
	            };
	          }

	          if (['get', 'set', 'sync', 'call', 'on', 'upload', 'uploadMultiple', 'removeUpload'].includes(property)) {
	            // Forward public API methods right away.
	            return function () {
	              for (var _len3 = arguments.length, args = new Array(_len3), _key3 = 0; _key3 < _len3; _key3++) {
	                args[_key3] = arguments[_key3];
	              }

	              return component[property].apply(component, args);
	            };
	          } // If the property exists on the data, return it.


	          var getResult = component.get(property); // If the property does not exist, try calling the method on the class.

	          if (getResult === undefined) {
	            return function () {
	              for (var _len4 = arguments.length, args = new Array(_len4), _key4 = 0; _key4 < _len4; _key4++) {
	                args[_key4] = arguments[_key4];
	              }

	              return component.call.apply(component, [property].concat(args));
	            };
	          }

	          return getResult;
	        },
	        set: function set(obj, prop, value) {
	          component.set(prop, value);
	          return true;
	        }
	      });
	    }
	  }]);

	  return Component;
	}();

	function FileUploads () {
	  var _this = this;

	  store$1.registerHook('interceptWireModelAttachListener', function (directive, el, component) {
	    if (!(el.tagName.toLowerCase() === 'input' && el.type === 'file')) {
	      return;
	    }

	    var start = function start() {
	      return el.dispatchEvent(new CustomEvent('cresenity-upload-start', {
	        bubbles: true
	      }));
	    };

	    var finish = function finish() {
	      return el.dispatchEvent(new CustomEvent('cresenity-upload-finish', {
	        bubbles: true
	      }));
	    };

	    var error = function error() {
	      return el.dispatchEvent(new CustomEvent('cresenity-upload-error', {
	        bubbles: true
	      }));
	    };

	    var progress = function progress(progressEvent) {
	      var percentCompleted = Math.round(progressEvent.loaded * 100 / progressEvent.total);
	      el.dispatchEvent(new CustomEvent('cresenity-upload-progress', {
	        bubbles: true,
	        detail: {
	          progress: percentCompleted
	        }
	      }));
	    };

	    var eventHandler = function eventHandler(e) {
	      if (e.target.files.length === 0) {
	        return;
	      }

	      start();

	      if (e.target.multiple) {
	        component.uploadMultiple(directive.value, e.target.files, finish, error, progress);
	      } else {
	        component.upload(directive.value, e.target.files[0], finish, error, progress);
	      }
	    };

	    el.addEventListener('change', eventHandler); // There's a bug in browsers where selecting a file, removing it,
	    // then re-adding it doesn't fire the change event. This fixes it.
	    // Reference: https://stackoverflow.com/questions/12030686/html-input-file-selection-event-not-firing-upon-selecting-the-same-file

	    var clearFileInputValue = function clearFileInputValue(e) {
	      _this.value = null;
	    };

	    el.addEventListener('click', clearFileInputValue);
	    component.addListenerForTeardown(function () {
	      el.removeEventListener('change', eventHandler);
	      el.removeEventListener('click', clearFileInputValue);
	    });
	  });
	}

	function LaravelEcho () {
	  store$1.registerHook('component.initialized', function (component) {
	    if (Array.isArray(component.listeners)) {
	      component.listeners.forEach(function (event) {
	        if (event.startsWith('echo')) {
	          if (typeof Echo === 'undefined') {
	            console.warn('Laravel Echo cannot be found');
	            return;
	          }

	          var event_parts = event.split(/(echo:|echo-)|:|,/);

	          if (event_parts[1] == 'echo:') {
	            event_parts.splice(2, 0, 'channel', undefined);
	          }

	          if (event_parts[2] == 'notification') {
	            event_parts.push(undefined, undefined);
	          }

	          var _event_parts = _slicedToArray(event_parts, 7);
	              _event_parts[0];
	              _event_parts[1];
	              var channel_type = _event_parts[2];
	              _event_parts[3];
	              var channel = _event_parts[4];
	              _event_parts[5];
	              var event_name = _event_parts[6];

	          if (['channel', 'private'].includes(channel_type)) {
	            Echo[channel_type](channel).listen(event_name, function (e) {
	              store$1.emit(event, e);
	            });
	          } else if (channel_type == 'presence') {
	            Echo.join(channel)[event_name](function (e) {
	              store$1.emit(event, e);
	            });
	          } else if (channel_type == 'notification') {
	            Echo.private(channel).notification(function (notification) {
	              store$1.emit(event, notification);
	            });
	          } else {
	            console.warn('Echo channel type not yet supported');
	          }
	        }
	      });
	    }
	  });
	}

	function DirtyStates () {
	  store$1.registerHook('component.initialized', function (component) {
	    component.dirtyEls = [];
	  });
	  store$1.registerHook('element.initialized', function (el, component) {
	    if (cresDirectives(el).missing('dirty')) {
	      return;
	    }

	    component.dirtyEls.push(el);
	  });
	  store$1.registerHook('interceptWireModelAttachListener', function (directive, el, component) {
	    var property = directive.value;
	    el.addEventListener('input', function () {
	      component.dirtyEls.forEach(function (dirtyEl) {
	        var directives = cresDirectives(dirtyEl);

	        if (directives.has('model') && directives.get('model').value === property || directives.has('target') && directives.get('target').value.split(',').map(function (s) {
	          return s.trim();
	        }).includes(property)) {
	          var isDirty = DOM.valueFromInput(el, component) != component.get(property);
	          setDirtyState(dirtyEl, isDirty);
	        }
	      });
	    });
	  });
	  store$1.registerHook('message.received', function (message, component) {
	    component.dirtyEls.forEach(function (element) {
	      if (element.__cresenity_dirty_cleanup) {
	        element.__cresenity_dirty_cleanup();

	        delete element.__cresenity_dirty_cleanup;
	      }
	    });
	  });
	  store$1.registerHook('element.removed', function (el, component) {
	    component.dirtyEls.forEach(function (element, index) {
	      if (element.isSameNode(el)) {
	        component.dirtyEls.splice(index, 1);
	      }
	    });
	  });
	}

	function setDirtyState(el, isDirty) {
	  var directive = cresDirectives(el).get('dirty');

	  if (directive.modifiers.includes('class')) {
	    var classes = directive.value.split(' ');

	    if (directive.modifiers.includes('remove') !== isDirty) {
	      var _el$classList;

	      (_el$classList = el.classList).add.apply(_el$classList, _toConsumableArray(classes));

	      el.__cresenity_dirty_cleanup = function () {
	        var _el$classList2;

	        return (_el$classList2 = el.classList).remove.apply(_el$classList2, _toConsumableArray(classes));
	      };
	    } else {
	      var _el$classList3;

	      (_el$classList3 = el.classList).remove.apply(_el$classList3, _toConsumableArray(classes));

	      el.__cresenity_dirty_cleanup = function () {
	        var _el$classList4;

	        return (_el$classList4 = el.classList).add.apply(_el$classList4, _toConsumableArray(classes));
	      };
	    }
	  } else if (directive.modifiers.includes('attr')) {
	    if (directive.modifiers.includes('remove') !== isDirty) {
	      el.setAttribute(directive.value, true);

	      el.__cresenity_dirty_cleanup = function () {
	        return el.removeAttribute(directive.value);
	      };
	    } else {
	      el.removeAttribute(directive.value);

	      el.__cresenity_dirty_cleanup = function () {
	        return el.setAttribute(directive.value, true);
	      };
	    }
	  } else if (!cresDirectives(el).get('model')) {
	    el.style.display = isDirty ? 'inline-block' : 'none';

	    el.__cresenity_dirty_cleanup = function () {
	      return el.style.display = isDirty ? 'none' : 'inline-block';
	    };
	  }
	}

	var cleanupStackByComponentId = {};
	function DisableForms () {
	  store$1.registerHook('element.initialized', function (el, component) {
	    var directives = cresDirectives(el);

	    if (directives.missing('submit')) {
	      return;
	    } // Set a forms "disabled" state on inputs and buttons.
	    // Cresenity will clean it all up automatically when the form
	    // submission returns and the new DOM lacks these additions.


	    el.addEventListener('submit', function () {
	      cleanupStackByComponentId[component.id] = [];
	      component.walk(function (node) {
	        if (!el.contains(node)) {
	          return;
	        }

	        if (node.hasAttribute('cres:ignore')) {
	          return false;
	        }

	        if ( // <button type="submit">
	        node.tagName.toLowerCase() === 'button' && node.type === 'submit' || // <select>
	        node.tagName.toLowerCase() === 'select' || node.tagName.toLowerCase() === 'input' && (node.type === 'checkbox' || node.type === 'radio')) {
	          if (!node.disabled) {
	            cleanupStackByComponentId[component.id].push(function () {
	              return node.disabled = false;
	            });
	          }

	          node.disabled = true;
	        } else if ( // <input type="text">
	        node.tagName.toLowerCase() === 'input' || // <textarea>
	        node.tagName.toLowerCase() === 'textarea') {
	          if (!node.readOnly) {
	            cleanupStackByComponentId[component.id].push(function () {
	              return node.readOnly = false;
	            });
	          }

	          node.readOnly = true;
	        }
	      });
	    });
	  });
	  store$1.registerHook('message.failed', function (message, component) {
	    return cleanup(component);
	  });
	  store$1.registerHook('message.received', function (message, component) {
	    return cleanup(component);
	  });
	}

	function cleanup(component) {
	  if (!cleanupStackByComponentId[component.id]) {
	    return;
	  }

	  while (cleanupStackByComponentId[component.id].length > 0) {
	    cleanupStackByComponentId[component.id].shift()();
	  }
	}

	function FileDownloads () {
	  store$1.registerHook('message.received', function (message, component) {
	    var response = message.response;

	    if (!response.effects.download) {
	      return;
	    } // We need to use window.webkitURL so downloads work on iOS Sarfari.


	    var urlObject = window.webkitURL || window.URL;
	    var url = urlObject.createObjectURL(base64toBlob(response.effects.download.content));
	    var invisibleLink = document.createElement('a');
	    invisibleLink.style.display = 'none';
	    invisibleLink.href = url;
	    invisibleLink.download = response.effects.download.name;
	    document.body.appendChild(invisibleLink);
	    invisibleLink.click();
	    setTimeout(function () {
	      urlObject.revokeObjectURL(url);
	    }, 0);
	  });
	}

	function base64toBlob(b64Data) {
	  var contentType = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
	  var sliceSize = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 512;
	  var byteCharacters = atob(b64Data);
	  var byteArrays = [];

	  for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
	    var slice = byteCharacters.slice(offset, offset + sliceSize);
	    var byteNumbers = new Array(slice.length);

	    for (var i = 0; i < slice.length; i++) {
	      byteNumbers[i] = slice.charCodeAt(i);
	    }

	    var byteArray = new Uint8Array(byteNumbers);
	    byteArrays.push(byteArray);
	  }

	  return new Blob(byteArrays, {
	    type: contentType
	  });
	}

	var offlineEls = [];
	function OfflineStates () {
	  store$1.registerHook('element.initialized', function (el) {
	    if (cresDirectives(el).missing('offline')) {
	      return;
	    }

	    offlineEls.push(el);
	  });
	  window.addEventListener('offline', function () {
	    store$1.cresenityIsOffline = true;
	    offlineEls.forEach(function (el) {
	      toggleOffline(el, true);
	    });
	  });
	  window.addEventListener('online', function () {
	    store$1.cresenityIsOffline = false;
	    offlineEls.forEach(function (el) {
	      toggleOffline(el, false);
	    });
	  });
	  store$1.registerHook('element.removed', function (el) {
	    offlineEls = offlineEls.filter(function (el) {
	      return !el.isSameNode(el);
	    });
	  });
	}

	function toggleOffline(el, isOffline) {
	  var directives = cresDirectives(el);
	  var directive = directives.get('offline');

	  if (directive.modifiers.includes('class')) {
	    var classes = directive.value.split(' ');

	    if (directive.modifiers.includes('remove') !== isOffline) {
	      var _el$classList;

	      (_el$classList = el.classList).add.apply(_el$classList, _toConsumableArray(classes));
	    } else {
	      var _el$classList2;

	      (_el$classList2 = el.classList).remove.apply(_el$classList2, _toConsumableArray(classes));
	    }
	  } else if (directive.modifiers.includes('attr')) {
	    if (directive.modifiers.includes('remove') !== isOffline) {
	      el.setAttribute(directive.value, true);
	    } else {
	      el.removeAttribute(directive.value);
	    }
	  } else if (!directives.get('model')) {
	    el.style.display = isOffline ? 'inline-block' : 'none';
	  }
	}

	function SyncBrowserHistory () {
	  var initializedPath = false;
	  var componentIdsThatAreWritingToHistoryState = new Set();
	  CresenityStateManager.clearState();
	  store$1.registerHook('component.initialized', function (component) {
	    if (!component.effects.path) {
	      return;
	    } // We are using setTimeout() to make sure all the components on the page have
	    // loaded before we store anything in the history state (because the position
	    // of a component on a page matters for generating its state signature).


	    setTimeout(function () {
	      var url = onlyChangeThePathAndQueryString(initializedPath ? undefined : component.effects.path); // Generate faux response.

	      var response = {
	        serverMemo: component.serverMemo,
	        effects: component.effects
	      };
	      normalizeResponse(response, component);
	      CresenityStateManager.replaceState(url, response, component);
	      componentIdsThatAreWritingToHistoryState.add(component.id);
	      initializedPath = true;
	    });
	  });
	  store$1.registerHook('message.processed', function (message, component) {
	    // Preventing a circular dependancy.
	    if (message.replaying) {
	      return;
	    }

	    var response = message.response;
	    var effects = response.effects || {};
	    normalizeResponse(response, component);

	    if ('path' in effects && effects.path !== window.location.href) {
	      var url = onlyChangeThePathAndQueryString(effects.path);
	      CresenityStateManager.pushState(url, response, component);
	      componentIdsThatAreWritingToHistoryState.add(component.id);
	    } else {
	      // If the current component has changed it's state, but hasn't written
	      // anything new to the URL, we still need to update it's data in the
	      // history state so that when a back button is hit, it is caught
	      // up to the most recent known data state.
	      if (componentIdsThatAreWritingToHistoryState.has(component.id)) {
	        CresenityStateManager.replaceState(window.location.href, response, component);
	      }
	    }
	  });
	  window.addEventListener('popstate', function (event) {
	    if (CresenityStateManager.missingState(event)) {
	      return;
	    }

	    CresenityStateManager.replayResponses(event, function (response, component) {
	      var message = new _default$3(component, []);
	      message.storeResponse(response);
	      message.replaying = true;
	      component.handleResponse(message);
	    });
	  });

	  function normalizeResponse(response, component) {
	    // Add ALL properties as "dirty" so that when the back button is pressed,
	    // they ALL are forced to refresh on the page (even if the HTML didn't change).
	    response.effects.dirty = Object.keys(response.serverMemo.data); // Sometimes Cresenity doesn't return html from the server to save on bandwidth.
	    // So we need to set the HTML no matter what.

	    response.effects.html = component.lastFreshHtml;
	  }

	  function onlyChangeThePathAndQueryString(url) {
	    if (!url) {
	      return;
	    }

	    var destination = new URL(url);
	    var afterOrigin = destination.href.replace(destination.origin, '');
	    return window.location.origin + afterOrigin + window.location.hash;
	  }

	  store$1.registerHook('element.updating', function (from, to, component) {
	    // It looks like the element we are about to update is the root
	    // element of the component. Let's store this knowledge to
	    // reference after update in the "element.updated" hook.
	    if (from.getAttribute('cres:id') === component.id) {
	      component.lastKnownDomId = component.id;
	    }
	  });
	  store$1.registerHook('element.updated', function (node, component) {
	    // If the element that was just updated was the root DOM element.
	    if (component.lastKnownDomId) {
	      // Let's check and see if the cres:id was the thing that changed.
	      if (node.getAttribute('cres:id') !== component.lastKnownDomId) {
	        // If so, we need to change this ID globally everwhere it's referenced.
	        store$1.changeComponentId(component, node.getAttribute('cres:id'));
	      } // Either way, we'll unset this for the next update.


	      delete component.lastKnownDomId;
	    } // We have to update the component ID because we are replaying responses
	    // from similar components but with completely different IDs. If didn't
	    // update the component ID, the checksums would fail.

	  });
	}
	var CresenityStateManager = {
	  replaceState: function replaceState(url, response, component) {
	    this.updateState('replaceState', url, response, component);
	  },
	  pushState: function pushState(url, response, component) {
	    this.updateState('pushState', url, response, component);
	  },
	  updateState: function updateState(method, url, response, component) {
	    var state = this.currentState();
	    state.storeResponse(response, component);
	    var stateArray = state.toStateArray(); // Copy over existing history state if it's an object, so we don't overwrite it.

	    var fullstateObject = Object.assign(history.state || {}, {
	      cresenity: stateArray
	    });

	    var capitalize = function capitalize(subject) {
	      return subject.charAt(0).toUpperCase() + subject.slice(1);
	    };

	    store$1.callHook('before' + capitalize(method), fullstateObject, url, component);

	    try {
	      history[method](fullstateObject, '', url);
	    } catch (error) {
	      // Firefox has a 160kb limit to history state entries.
	      // If that limit is reached, we'll instead put it in
	      // sessionStorage and store a reference to it.
	      if (error.name === 'NS_ERROR_ILLEGAL_VALUE') {
	        var key = this.storeInSession(stateArray);
	        fullstateObject.cresenity = key;
	        history[method](fullstateObject, '', url);
	      }
	    }
	  },
	  replayResponses: function replayResponses(event, callback) {
	    if (!event.state.cresenity) {
	      return;
	    }

	    var state = typeof event.state.cresenity === 'string' ? new CresenityState(this.getFromSession(event.state.cresenity)) : new CresenityState(event.state.cresenity);
	    state.replayResponses(callback);
	  },
	  currentState: function currentState() {
	    if (!history.state) {
	      return new CresenityState();
	    }

	    if (!history.state.cresenity) {
	      return new CresenityState();
	    }

	    var state = typeof history.state.cresenity === 'string' ? new CresenityState(this.getFromSession(history.state.cresenity)) : new CresenityState(history.state.cresenity);
	    return state;
	  },
	  missingState: function missingState(event) {
	    return !(event.state && event.state.cresenity);
	  },
	  clearState: function clearState() {
	    // This is to prevent exponentially increasing the size of our state on page refresh.
	    if (window.history.state) {
	      window.history.state.cresenity = new CresenityState().toStateArray();
	    }
	  },
	  storeInSession: function storeInSession(value) {
	    var key = 'cresenity:' + new Date().getTime();
	    var stringifiedValue = JSON.stringify(value);
	    this.tryToStoreInSession(key, stringifiedValue);
	    return key;
	  },
	  tryToStoreInSession: function tryToStoreInSession(key, value) {
	    // sessionStorage has a max storage limit (usally 5MB).
	    // If we meet that limit, we'll start removing entries
	    // (oldest first), until there's enough space to store
	    // the new one.
	    try {
	      sessionStorage.setItem(key, value);
	    } catch (error) {
	      // 22 is Chrome, 1-14 is other browsers.
	      if (![22, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14].includes(error.code)) {
	        return;
	      }

	      var oldestTimestamp = Object.keys(sessionStorage).map(function (key) {
	        return Number(key.replace('cresenity:', ''));
	      }).sort().shift();

	      if (!oldestTimestamp) {
	        return;
	      }

	      sessionStorage.removeItem('cresenity:' + oldestTimestamp);
	      this.tryToStoreInSession(key, value);
	    }
	  },
	  getFromSession: function getFromSession(key) {
	    var item = sessionStorage.getItem(key);

	    if (!item) {
	      return;
	    }

	    return JSON.parse(item);
	  }
	};

	var CresenityState = /*#__PURE__*/function () {
	  function CresenityState() {
	    var stateArray = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];

	    _classCallCheck(this, CresenityState);

	    this.items = stateArray;
	  }

	  _createClass(CresenityState, [{
	    key: "toStateArray",
	    value: function toStateArray() {
	      return this.items;
	    }
	  }, {
	    key: "pushItemInProperOrder",
	    value: function pushItemInProperOrder(signature, response, component) {
	      var _this = this;

	      var targetItem = {
	        signature: signature,
	        response: response
	      }; // First, we'll check if this signature already has an entry, if so, replace it.

	      var existingIndex = this.items.findIndex(function (item) {
	        return item.signature === signature;
	      }); // eslint-disable-next-line no-return-assign

	      if (existingIndex !== -1) {
	        return this.items[existingIndex] = targetItem;
	      } // If it doesn't already exist, we'll add it, but we MUST first see if any of its
	      // parents components have entries, and insert it immediately before them.
	      // This way, when we replay responses, we will always start with the most
	      // inward components and go outwards.


	      var closestParentId = store$1.getClosestParentId(component.id, this.componentIdsWithStoredResponses());

	      if (!closestParentId) {
	        return this.items.unshift(targetItem);
	      }

	      var closestParentIndex = this.items.findIndex(function (item) {
	        var _this$parseSignature = _this.parseSignature(item.signature),
	            originalComponentId = _this$parseSignature.originalComponentId;

	        if (originalComponentId === closestParentId) {
	          return true;
	        }
	      });
	      this.items.splice(closestParentIndex, 0, targetItem);
	    }
	  }, {
	    key: "storeResponse",
	    value: function storeResponse(response, component) {
	      var signature = this.getComponentNameBasedSignature(component);
	      this.pushItemInProperOrder(signature, response, component);
	    }
	  }, {
	    key: "replayResponses",
	    value: function replayResponses(callback) {
	      var _this2 = this;

	      this.items.forEach(function (_ref) {
	        var signature = _ref.signature,
	            response = _ref.response;

	        var component = _this2.findComponentBySignature(signature);

	        if (!component) {
	          return;
	        }

	        callback(response, component);
	      });
	    } // We can't just store component reponses by their id because
	    // ids change on every refresh, so history state won't have
	    // a component to apply it's changes to. Instead we must
	    // generate a unique id based on the components name
	    // and it's relative position amongst others with
	    // the same name that are loaded on the page.

	  }, {
	    key: "getComponentNameBasedSignature",
	    value: function getComponentNameBasedSignature(component) {
	      var componentName = component.fingerprint.name;
	      var sameNamedComponents = store$1.getComponentsByName(componentName);
	      var componentIndex = sameNamedComponents.indexOf(component);
	      return "".concat(component.id, ":").concat(componentName, ":").concat(componentIndex);
	    }
	  }, {
	    key: "findComponentBySignature",
	    value: function findComponentBySignature(signature) {
	      var _this$parseSignature2 = this.parseSignature(signature),
	          componentName = _this$parseSignature2.componentName,
	          componentIndex = _this$parseSignature2.componentIndex;

	      var sameNamedComponents = store$1.getComponentsByName(componentName); // If we found the component in the proper place, return it,
	      // otherwise return the first one.
	      // eslint-disable-next-line no-console

	      return sameNamedComponents[componentIndex] || sameNamedComponents[0] || console.warn("Cresenity: couldn't find component on page: ".concat(componentName));
	    }
	  }, {
	    key: "parseSignature",
	    value: function parseSignature(signature) {
	      var _signature$split = signature.split(':'),
	          _signature$split2 = _slicedToArray(_signature$split, 3),
	          originalComponentId = _signature$split2[0],
	          componentName = _signature$split2[1],
	          componentIndex = _signature$split2[2];

	      return {
	        originalComponentId: originalComponentId,
	        componentName: componentName,
	        componentIndex: componentIndex
	      };
	    }
	  }, {
	    key: "componentIdsWithStoredResponses",
	    value: function componentIdsWithStoredResponses() {
	      var _this3 = this;

	      return this.items.map(function (_ref2) {
	        var signature = _ref2.signature;

	        var _this3$parseSignature = _this3.parseSignature(signature),
	            originalComponentId = _this3$parseSignature.originalComponentId;

	        return originalComponentId;
	      });
	    }
	  }]);

	  return CresenityState;
	}();

	// Find exact position of element
	function isWindow(obj) {
	  return obj !== null && obj === obj.window;
	}

	function getWindow(elem) {
	  return isWindow(elem) ? elem : elem.nodeType === 9 && elem.defaultView;
	}

	function offset(elem) {
	  var docElem,
	      win,
	      box = {
	    top: 0,
	    left: 0
	  },
	      doc = elem && elem.ownerDocument;
	  docElem = doc.documentElement;

	  if (_typeof$1(elem.getBoundingClientRect) !== ("undefined" )) {
	    box = elem.getBoundingClientRect();
	  }

	  win = getWindow(doc);
	  return {
	    top: box.top + win.pageYOffset - docElem.clientTop,
	    left: box.left + win.pageXOffset - docElem.clientLeft
	  };
	}

	function convertStyle(obj) {
	  var style = '';

	  for (var a in obj) {
	    if (obj.hasOwnProperty(a)) {
	      style += a + ':' + obj[a] + ';';
	    }
	  }

	  return style;
	}

	var Effect = {
	  // Effect delay
	  duration: 750,
	  show: function show(e, element) {
	    // Disable right click
	    if (e.button === 2) {
	      return false;
	    }

	    var el = element || this; // Create ripple

	    var ripple = document.createElement('div');
	    ripple.className = 'cres-waves-ripple';
	    el.appendChild(ripple); // Get click coordinate and element witdh

	    var pos = offset(el);
	    var relativeY = e.pageY - pos.top;
	    var relativeX = e.pageX - pos.left;
	    var scale = 'scale(' + el.clientWidth / 100 * 10 + ')'; // Support for touch devices

	    if ('touches' in e) {
	      relativeY = e.touches[0].pageY - pos.top;
	      relativeX = e.touches[0].pageX - pos.left;
	    } // Attach data to element


	    ripple.setAttribute('data-hold', Date.now());
	    ripple.setAttribute('data-scale', scale);
	    ripple.setAttribute('data-x', relativeX);
	    ripple.setAttribute('data-y', relativeY); // Set ripple position

	    var rippleStyle = {
	      top: relativeY + 'px',
	      left: relativeX + 'px'
	    };
	    ripple.className = ripple.className + ' waves-notransition';
	    ripple.setAttribute('style', convertStyle(rippleStyle));
	    ripple.className = ripple.className.replace('waves-notransition', ''); // Scale the ripple

	    rippleStyle['-webkit-transform'] = scale;
	    rippleStyle['-moz-transform'] = scale;
	    rippleStyle['-ms-transform'] = scale;
	    rippleStyle['-o-transform'] = scale;
	    rippleStyle.transform = scale;
	    rippleStyle.opacity = '1';
	    rippleStyle['-webkit-transition-duration'] = Effect.duration + 'ms';
	    rippleStyle['-moz-transition-duration'] = Effect.duration + 'ms';
	    rippleStyle['-o-transition-duration'] = Effect.duration + 'ms';
	    rippleStyle['transition-duration'] = Effect.duration + 'ms';
	    rippleStyle['-webkit-transition-timing-function'] = 'cubic-bezier(0.250, 0.460, 0.450, 0.940)';
	    rippleStyle['-moz-transition-timing-function'] = 'cubic-bezier(0.250, 0.460, 0.450, 0.940)';
	    rippleStyle['-o-transition-timing-function'] = 'cubic-bezier(0.250, 0.460, 0.450, 0.940)';
	    rippleStyle['transition-timing-function'] = 'cubic-bezier(0.250, 0.460, 0.450, 0.940)';
	    ripple.setAttribute('style', convertStyle(rippleStyle));
	  },
	  hide: function hide(e) {
	    TouchHandler.touchup(e);
	    var el = this; //var width = el.clientWidth * 1.4;
	    // Get first ripple

	    var ripple = null;
	    var ripples = el.getElementsByClassName('cres-waves-ripple');

	    if (ripples.length > 0) {
	      ripple = ripples[ripples.length - 1];
	    } else {
	      return false;
	    }

	    var relativeX = ripple.getAttribute('data-x');
	    var relativeY = ripple.getAttribute('data-y');
	    var scale = ripple.getAttribute('data-scale'); // Get delay beetween mousedown and mouse leave

	    var diff = Date.now() - Number(ripple.getAttribute('data-hold'));
	    var delay = 350 - diff;

	    if (delay < 0) {
	      delay = 0;
	    } // Fade out ripple after delay


	    setTimeout(function () {
	      var style = {
	        top: relativeY + 'px',
	        left: relativeX + 'px',
	        opacity: '0',
	        // Duration
	        '-webkit-transition-duration': Effect.duration + 'ms',
	        '-moz-transition-duration': Effect.duration + 'ms',
	        '-o-transition-duration': Effect.duration + 'ms',
	        'transition-duration': Effect.duration + 'ms',
	        '-webkit-transform': scale,
	        '-moz-transform': scale,
	        '-ms-transform': scale,
	        '-o-transform': scale,
	        transform: scale
	      };
	      ripple.setAttribute('style', convertStyle(style));
	      setTimeout(function () {
	        try {
	          el.removeChild(ripple);
	        } catch (eee) {
	          return false;
	        }
	      }, Effect.duration);
	    }, delay);
	  },
	  // Little hack to make <input> can perform waves effect
	  wrapInput: function wrapInput(elements) {
	    for (var a = 0; a < elements.length; a++) {
	      var el = elements[a];

	      if (el.tagName.toLowerCase() === 'input') {
	        var parent = el.parentNode; // If input already have parent just pass through

	        if (parent.tagName.toLowerCase() === 'i' && parent.className.indexOf('waves-effect') !== -1) {
	          continue;
	        } // Put element class and style to the specified parent


	        var wrapper = document.createElement('i');
	        wrapper.className = el.className + ' waves-input-wrapper';
	        var elementStyle = el.getAttribute('style');

	        if (!elementStyle) {
	          elementStyle = '';
	        }

	        wrapper.setAttribute('style', elementStyle);
	        el.className = 'waves-button-input';
	        el.removeAttribute('style'); // Put element as child

	        parent.replaceChild(wrapper, el);
	        wrapper.appendChild(el);
	      }
	    }
	  }
	};
	/**
	 * Disable mousedown event for 500ms during and after touch
	 */

	var TouchHandler = {
	  /* uses an integer rather than bool so there's no issues with
	   * needing to clear timeouts if another touch event occurred
	   * within the 500ms. Cannot mouseup between touchstart and
	   * touchend, nor in the 500ms after touchend.
	   */
	  touches: 0,
	  allowEvent: function allowEvent(e) {
	    var allow = true;

	    if (e.type === 'touchstart') {
	      TouchHandler.touches += 1; //push
	    } else if (e.type === 'touchend' || e.type === 'touchcancel') {
	      setTimeout(function () {
	        if (TouchHandler.touches > 0) {
	          TouchHandler.touches -= 1; //pop after 500ms
	        }
	      }, 500);
	    } else if (e.type === 'mousedown' && TouchHandler.touches > 0) {
	      allow = false;
	    }

	    return allow;
	  },
	  touchup: function touchup(e) {
	    TouchHandler.allowEvent(e);
	  }
	};
	/**
	 * Delegated click handler for .waves-effect element.
	 * returns null when .waves-effect element not in "click tree"
	 */

	function getWavesEffectElement(e) {
	  if (TouchHandler.allowEvent(e) === false) {
	    return null;
	  }

	  var element = null;
	  var target = e.target || e.srcElement;

	  while (target.parentNode !== null) {
	    if (!(target instanceof SVGElement) && target.className.indexOf('waves-effect') !== -1) {
	      element = target;
	      break;
	    }

	    target = target.parentNode;
	  }

	  return element;
	}
	/**
	 * Bubble the click and show effect if .waves-effect elem was found
	 */


	function showEffect(e) {
	  var element = getWavesEffectElement(e);

	  if (element !== null) {
	    Effect.show(e, element);

	    if ('ontouchstart' in window) {
	      element.addEventListener('touchend', Effect.hide, false);
	      element.addEventListener('touchcancel', Effect.hide, false);
	    }

	    element.addEventListener('mouseup', Effect.hide, false);
	    element.addEventListener('mouseleave', Effect.hide, false);
	    element.addEventListener('dragend', Effect.hide, false);
	  }
	}
	/**
	 * Attach Waves to an input element (or any element which doesn't
	 * bubble mouseup/mousedown events).
	 *   Intended to be used with dynamically loaded forms/inputs, or
	 * where the user doesn't want a delegated click handler.
	 */


	var attachWaves = function attachWaves(element) {
	  //FUTURE: automatically add waves classes and allow users
	  // to specify them with an options param? Eg. light/classic/button
	  if (element.tagName.toLowerCase() === 'input') {
	    Effect.wrapInput([element]);
	    element = element.parentNode;
	  }

	  if ('ontouchstart' in window) {
	    element.addEventListener('touchstart', showEffect, false);
	  }

	  element.addEventListener('mousedown', showEffect, false);
	};

	var UI = /*#__PURE__*/function () {
	  function UI() {
	    _classCallCheck(this, UI);

	    this.connection = new Connection();
	    this.components = store$1;
	    this.devToolsEnabled = false;

	    this.onLoadCallback = function () {};

	    this.waves = {
	      attach: attachWaves
	    };
	  }

	  _createClass(UI, [{
	    key: "first",
	    value: function first() {
	      return Object.values(this.components.componentsById)[0].$cres;
	    }
	  }, {
	    key: "find",
	    value: function find(componentId) {
	      return this.components.componentsById[componentId].$cres;
	    }
	  }, {
	    key: "all",
	    value: function all() {
	      return Object.values(this.components.componentsById).map(function (component) {
	        return component.$cres;
	      });
	    }
	  }, {
	    key: "directive",
	    value: function directive(name, callback) {
	      this.components.registerDirective(name, callback);
	    }
	  }, {
	    key: "hook",
	    value: function hook(name, callback) {
	      this.components.registerHook(name, callback);
	    }
	  }, {
	    key: "onLoad",
	    value: function onLoad(callback) {
	      this.onLoadCallback = callback;
	    }
	  }, {
	    key: "onError",
	    value: function onError(callback) {
	      this.components.onErrorCallback = callback;
	    }
	  }, {
	    key: "emit",
	    value: function emit(event) {
	      var _this$components;

	      for (var _len = arguments.length, params = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
	        params[_key - 1] = arguments[_key];
	      }

	      (_this$components = this.components).emit.apply(_this$components, [event].concat(params));
	    }
	  }, {
	    key: "emitTo",
	    value: function emitTo(name, event) {
	      var _this$components2;

	      for (var _len2 = arguments.length, params = new Array(_len2 > 2 ? _len2 - 2 : 0), _key2 = 2; _key2 < _len2; _key2++) {
	        params[_key2 - 2] = arguments[_key2];
	      }

	      (_this$components2 = this.components).emitTo.apply(_this$components2, [name, event].concat(params));
	    }
	  }, {
	    key: "on",
	    value: function on(event, callback) {
	      this.components.on(event, callback);
	    }
	  }, {
	    key: "off",
	    value: function off(event, callback) {
	      this.components.off(event, callback);
	    }
	  }, {
	    key: "devTools",
	    value: function devTools(enableDevtools) {
	      this.devToolsEnabled = enableDevtools;
	    }
	  }, {
	    key: "restart",
	    value: function restart() {
	      this.stop();
	      this.start();
	    }
	  }, {
	    key: "stop",
	    value: function stop() {
	      this.components.tearDownComponents();
	    }
	  }, {
	    key: "start",
	    value: function start() {
	      var _this = this;

	      DOM.rootComponentElementsWithNoParents().forEach(function (el) {
	        _this.components.addComponent(new Component(el, _this.connection));
	      });
	      this.onLoadCallback();
	      dispatch$1('cresenity:ui:start');
	      document.addEventListener('visibilitychange', function () {
	        _this.components.cresenityIsInBackground = document.hidden;
	      }, false);
	      this.components.initialRenderIsFinished = true;
	    }
	  }, {
	    key: "rescan",
	    value: function rescan() {
	      var _this2 = this;

	      var node = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
	      DOM.rootComponentElementsWithNoParents(node).forEach(function (el) {
	        var componentId = cresDirectives(el).get('id').value;

	        if (_this2.components.hasComponent(componentId)) {
	          return;
	        }

	        _this2.components.addComponent(new Component(el, _this2.connection));
	      });
	    }
	  }]);

	  return UI;
	}();

	SyncBrowserHistory();
	SupportAlpine();
	FileDownloads();
	OfflineStates();
	LoadingStates();
	DisableForms();
	FileUploads();
	LaravelEcho();
	DirtyStates();
	Polling();

	var keyStr = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';

	var utf8Encode = function utf8Encode(e) {
	  e = e.replace(/rn/g, 'n');
	  var t = '';

	  for (var n = 0; n < e.length; n++) {
	    var r = e.charCodeAt(n);

	    if (r < 128) {
	      t = t + String.fromCharCode(r);
	    } else if (r > 127 && r < 2048) {
	      t = t + String.fromCharCode(r >> 6 | 192);
	      t = t + String.fromCharCode(r & 63 | 128);
	    } else {
	      t = t + String.fromCharCode(r >> 12 | 224);
	      t = t + String.fromCharCode(r >> 6 & 63 | 128);
	      t = t + String.fromCharCode(r & 63 | 128);
	    }
	  }

	  return t;
	};

	var utf8Decode = function utf8Decode(e) {
	  var t = '';
	  var n = 0;
	  var c2 = 0;
	  var c3 = 0;
	  var r = 0;

	  while (n < e.length) {
	    r = e.charCodeAt(n);

	    if (r < 128) {
	      t = t + String.fromCharCode(r);
	      n++;
	    } else if (r > 191 && r < 224) {
	      c2 = e.charCodeAt(n + 1);
	      t = t + String.fromCharCode((r & 31) << 6 | c2 & 63);
	      n = n + 2;
	    } else {
	      c2 = e.charCodeAt(n + 1);
	      c3 = e.charCodeAt(n + 2);
	      t = t + String.fromCharCode((r & 15) << 12 | (c2 & 63) << 6 | c3 & 63);
	      n = n + 3;
	    }
	  }

	  return t;
	};

	var encode = function encode(e) {
	  var t = '';
	  var n, r, i, s, o, u, a;
	  var f = 0;
	  e = utf8Encode(e);

	  while (f < e.length) {
	    n = e.charCodeAt(f++);
	    r = e.charCodeAt(f++);
	    i = e.charCodeAt(f++);
	    s = n >> 2;
	    o = (n & 3) << 4 | r >> 4;
	    u = (r & 15) << 2 | i >> 6;
	    a = i & 63;

	    if (isNaN(r)) {
	      u = a = 64;
	    } else if (isNaN(i)) {
	      a = 64;
	    }

	    t = t + keyStr.charAt(s) + keyStr.charAt(o) + keyStr.charAt(u) + keyStr.charAt(a);
	  }

	  return t;
	};

	var decode = function decode(e) {
	  var t = '';
	  var n, r, i;
	  var s, o, u, a;
	  var f = 0;
	  e = e.replace(/[^A-Za-z0-9+/=]/g, '');

	  while (f < e.length) {
	    s = keyStr.indexOf(e.charAt(f++));
	    o = keyStr.indexOf(e.charAt(f++));
	    u = keyStr.indexOf(e.charAt(f++));
	    a = keyStr.indexOf(e.charAt(f++));
	    n = s << 2 | o >> 4;
	    r = (o & 15) << 4 | u >> 2;
	    i = (u & 3) << 6 | a;
	    t = t + String.fromCharCode(n);

	    if (u != 64) {
	      t = t + String.fromCharCode(r);
	    }

	    if (a != 64) {
	      t = t + String.fromCharCode(i);
	    }
	  }

	  t = utf8Decode(t);
	  return t;
	};

	var echo = function echo() {
	  //  discuss at: https://locutus.io/php/echo/
	  // original by: Philip Peterson
	  // improved by: echo is bad
	  // improved by: Nate
	  // improved by: Brett Zamir (https://brett-zamir.me)
	  // improved by: Brett Zamir (https://brett-zamir.me)
	  // improved by: Brett Zamir (https://brett-zamir.me)
	  //  revised by: Der Simon (https://innerdom.sourceforge.net/)
	  // bugfixed by: Eugene Bulkin (https://doubleaw.com/)
	  // bugfixed by: Brett Zamir (https://brett-zamir.me)
	  // bugfixed by: Brett Zamir (https://brett-zamir.me)
	  // bugfixed by: EdorFaus
	  //      note 1: In 1.3.2 and earlier, this function wrote to the body of the document when it
	  //      note 1: was called in webbrowsers, in addition to supporting XUL.
	  //      note 1: This involved >100 lines of boilerplate to do this in a safe way.
	  //      note 1: Since I can't imageine a complelling use-case for this, and XUL is deprecated
	  //      note 1: I have removed this behavior in favor of just calling `console.log`
	  //      note 2: You'll see functions depends on `echo` instead of `console.log` as we'll want
	  //      note 2: to have 1 contact point to interface with the outside world, so that it's easy
	  //      note 2: to support other ways of printing output.
	  //  revised by: Kevin van Zonneveld (https://kvz.io)
	  //    input by: JB
	  //   example 1: echo('Hello world')
	  //   returns 1: undefined

	  var args = Array.prototype.slice.call(arguments);
	  return console.log(args.join(' '));
	};

	var ucfirst = function ucfirst(str) {
	  //  discuss at: https://locutus.io/php/ucfirst/
	  // original by: Kevin van Zonneveld (https://kvz.io)
	  // bugfixed by: Onno Marsman (https://twitter.com/onnomarsman)
	  // improved by: Brett Zamir (https://brett-zamir.me)
	  //   example 1: ucfirst('kevin van zonneveld')
	  //   returns 1: 'Kevin van zonneveld'

	  str += '';
	  var f = str.charAt(0).toUpperCase();
	  return f + str.substr(1);
	};

	var reSpace = '[ \\t]+';
	var reSpaceOpt = '[ \\t]*';
	var reMeridian = '(?:([ap])\\.?m\\.?([\\t ]|$))';
	var reHour24 = '(2[0-4]|[01]?[0-9])';
	var reHour24lz = '([01][0-9]|2[0-4])';
	var reHour12 = '(0?[1-9]|1[0-2])';
	var reMinute = '([0-5]?[0-9])';
	var reMinutelz = '([0-5][0-9])';
	var reSecond = '(60|[0-5]?[0-9])';
	var reSecondlz = '(60|[0-5][0-9])';
	var reFrac = '(?:\\.([0-9]+))';

	var reDayfull = 'sunday|monday|tuesday|wednesday|thursday|friday|saturday';
	var reDayabbr = 'sun|mon|tue|wed|thu|fri|sat';
	var reDaytext = reDayfull + '|' + reDayabbr + '|weekdays?';

	var reReltextnumber = 'first|second|third|fourth|fifth|sixth|seventh|eighth?|ninth|tenth|eleventh|twelfth';
	var reReltexttext = 'next|last|previous|this';
	var reReltextunit = '(?:second|sec|minute|min|hour|day|fortnight|forthnight|month|year)s?|weeks|' + reDaytext;

	var reYear = '([0-9]{1,4})';
	var reYear2 = '([0-9]{2})';
	var reYear4 = '([0-9]{4})';
	var reYear4withSign = '([+-]?[0-9]{4})';
	var reMonth = '(1[0-2]|0?[0-9])';
	var reMonthlz = '(0[0-9]|1[0-2])';
	var reDay = '(?:(3[01]|[0-2]?[0-9])(?:st|nd|rd|th)?)';
	var reDaylz = '(0[0-9]|[1-2][0-9]|3[01])';

	var reMonthFull = 'january|february|march|april|may|june|july|august|september|october|november|december';
	var reMonthAbbr = 'jan|feb|mar|apr|may|jun|jul|aug|sept?|oct|nov|dec';
	var reMonthroman = 'i[vx]|vi{0,3}|xi{0,2}|i{1,3}';
	var reMonthText = '(' + reMonthFull + '|' + reMonthAbbr + '|' + reMonthroman + ')';

	var reTzCorrection = '((?:GMT)?([+-])' + reHour24 + ':?' + reMinute + '?)';
	var reTzAbbr = '\\(?([a-zA-Z]{1,6})\\)?';
	var reDayOfYear = '(00[1-9]|0[1-9][0-9]|[12][0-9][0-9]|3[0-5][0-9]|36[0-6])';
	var reWeekOfYear = '(0[1-9]|[1-4][0-9]|5[0-3])';

	var reDateNoYear = reMonthText + '[ .\\t-]*' + reDay + '[,.stndrh\\t ]*';

	function processMeridian(hour, meridian) {
	  meridian = meridian && meridian.toLowerCase();

	  switch (meridian) {
	    case 'a':
	      hour += hour === 12 ? -12 : 0;
	      break;
	    case 'p':
	      hour += hour !== 12 ? 12 : 0;
	      break;
	  }

	  return hour;
	}

	function processYear(yearStr) {
	  var year = +yearStr;

	  if (yearStr.length < 4 && year < 100) {
	    year += year < 70 ? 2000 : 1900;
	  }

	  return year;
	}

	function lookupMonth(monthStr) {
	  return {
	    jan: 0,
	    january: 0,
	    i: 0,
	    feb: 1,
	    february: 1,
	    ii: 1,
	    mar: 2,
	    march: 2,
	    iii: 2,
	    apr: 3,
	    april: 3,
	    iv: 3,
	    may: 4,
	    v: 4,
	    jun: 5,
	    june: 5,
	    vi: 5,
	    jul: 6,
	    july: 6,
	    vii: 6,
	    aug: 7,
	    august: 7,
	    viii: 7,
	    sep: 8,
	    sept: 8,
	    september: 8,
	    ix: 8,
	    oct: 9,
	    october: 9,
	    x: 9,
	    nov: 10,
	    november: 10,
	    xi: 10,
	    dec: 11,
	    december: 11,
	    xii: 11
	  }[monthStr.toLowerCase()];
	}

	function lookupWeekday(dayStr) {
	  var desiredSundayNumber = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 0;

	  var dayNumbers = {
	    mon: 1,
	    monday: 1,
	    tue: 2,
	    tuesday: 2,
	    wed: 3,
	    wednesday: 3,
	    thu: 4,
	    thursday: 4,
	    fri: 5,
	    friday: 5,
	    sat: 6,
	    saturday: 6,
	    sun: 0,
	    sunday: 0
	  };

	  return dayNumbers[dayStr.toLowerCase()] || desiredSundayNumber;
	}

	function lookupRelative(relText) {
	  var relativeNumbers = {
	    last: -1,
	    previous: -1,
	    this: 0,
	    first: 1,
	    next: 1,
	    second: 2,
	    third: 3,
	    fourth: 4,
	    fifth: 5,
	    sixth: 6,
	    seventh: 7,
	    eight: 8,
	    eighth: 8,
	    ninth: 9,
	    tenth: 10,
	    eleventh: 11,
	    twelfth: 12
	  };

	  var relativeBehavior = {
	    this: 1
	  };

	  var relTextLower = relText.toLowerCase();

	  return {
	    amount: relativeNumbers[relTextLower],
	    behavior: relativeBehavior[relTextLower] || 0
	  };
	}

	function processTzCorrection(tzOffset, oldValue) {
	  var reTzCorrectionLoose = /(?:GMT)?([+-])(\d+)(:?)(\d{0,2})/i;
	  tzOffset = tzOffset && tzOffset.match(reTzCorrectionLoose);

	  if (!tzOffset) {
	    return oldValue;
	  }

	  var sign = tzOffset[1] === '-' ? -1 : 1;
	  var hours = +tzOffset[2];
	  var minutes = +tzOffset[4];

	  if (!tzOffset[4] && !tzOffset[3]) {
	    minutes = Math.floor(hours % 100);
	    hours = Math.floor(hours / 100);
	  }

	  // timezone offset in seconds
	  return sign * (hours * 60 + minutes) * 60;
	}

	// tz abbrevation : tz offset in seconds
	var tzAbbrOffsets = {
	  acdt: 37800,
	  acst: 34200,
	  addt: -7200,
	  adt: -10800,
	  aedt: 39600,
	  aest: 36000,
	  ahdt: -32400,
	  ahst: -36000,
	  akdt: -28800,
	  akst: -32400,
	  amt: -13840,
	  apt: -10800,
	  ast: -14400,
	  awdt: 32400,
	  awst: 28800,
	  awt: -10800,
	  bdst: 7200,
	  bdt: -36000,
	  bmt: -14309,
	  bst: 3600,
	  cast: 34200,
	  cat: 7200,
	  cddt: -14400,
	  cdt: -18000,
	  cemt: 10800,
	  cest: 7200,
	  cet: 3600,
	  cmt: -15408,
	  cpt: -18000,
	  cst: -21600,
	  cwt: -18000,
	  chst: 36000,
	  dmt: -1521,
	  eat: 10800,
	  eddt: -10800,
	  edt: -14400,
	  eest: 10800,
	  eet: 7200,
	  emt: -26248,
	  ept: -14400,
	  est: -18000,
	  ewt: -14400,
	  ffmt: -14660,
	  fmt: -4056,
	  gdt: 39600,
	  gmt: 0,
	  gst: 36000,
	  hdt: -34200,
	  hkst: 32400,
	  hkt: 28800,
	  hmt: -19776,
	  hpt: -34200,
	  hst: -36000,
	  hwt: -34200,
	  iddt: 14400,
	  idt: 10800,
	  imt: 25025,
	  ist: 7200,
	  jdt: 36000,
	  jmt: 8440,
	  jst: 32400,
	  kdt: 36000,
	  kmt: 5736,
	  kst: 30600,
	  lst: 9394,
	  mddt: -18000,
	  mdst: 16279,
	  mdt: -21600,
	  mest: 7200,
	  met: 3600,
	  mmt: 9017,
	  mpt: -21600,
	  msd: 14400,
	  msk: 10800,
	  mst: -25200,
	  mwt: -21600,
	  nddt: -5400,
	  ndt: -9052,
	  npt: -9000,
	  nst: -12600,
	  nwt: -9000,
	  nzdt: 46800,
	  nzmt: 41400,
	  nzst: 43200,
	  pddt: -21600,
	  pdt: -25200,
	  pkst: 21600,
	  pkt: 18000,
	  plmt: 25590,
	  pmt: -13236,
	  ppmt: -17340,
	  ppt: -25200,
	  pst: -28800,
	  pwt: -25200,
	  qmt: -18840,
	  rmt: 5794,
	  sast: 7200,
	  sdmt: -16800,
	  sjmt: -20173,
	  smt: -13884,
	  sst: -39600,
	  tbmt: 10751,
	  tmt: 12344,
	  uct: 0,
	  utc: 0,
	  wast: 7200,
	  wat: 3600,
	  wemt: 7200,
	  west: 3600,
	  wet: 0,
	  wib: 25200,
	  wita: 28800,
	  wit: 32400,
	  wmt: 5040,
	  yddt: -25200,
	  ydt: -28800,
	  ypt: -28800,
	  yst: -32400,
	  ywt: -28800,
	  a: 3600,
	  b: 7200,
	  c: 10800,
	  d: 14400,
	  e: 18000,
	  f: 21600,
	  g: 25200,
	  h: 28800,
	  i: 32400,
	  k: 36000,
	  l: 39600,
	  m: 43200,
	  n: -3600,
	  o: -7200,
	  p: -10800,
	  q: -14400,
	  r: -18000,
	  s: -21600,
	  t: -25200,
	  u: -28800,
	  v: -32400,
	  w: -36000,
	  x: -39600,
	  y: -43200,
	  z: 0
	};

	var formats = {
	  yesterday: {
	    regex: /^yesterday/i,
	    name: 'yesterday',
	    callback: function callback() {
	      this.rd -= 1;
	      return this.resetTime();
	    }
	  },

	  now: {
	    regex: /^now/i,
	    name: 'now'
	    // do nothing
	  },

	  noon: {
	    regex: /^noon/i,
	    name: 'noon',
	    callback: function callback() {
	      return this.resetTime() && this.time(12, 0, 0, 0);
	    }
	  },

	  midnightOrToday: {
	    regex: /^(midnight|today)/i,
	    name: 'midnight | today',
	    callback: function callback() {
	      return this.resetTime();
	    }
	  },

	  tomorrow: {
	    regex: /^tomorrow/i,
	    name: 'tomorrow',
	    callback: function callback() {
	      this.rd += 1;
	      return this.resetTime();
	    }
	  },

	  timestamp: {
	    regex: /^@(-?\d+)/i,
	    name: 'timestamp',
	    callback: function callback(match, timestamp) {
	      this.rs += +timestamp;
	      this.y = 1970;
	      this.m = 0;
	      this.d = 1;
	      this.dates = 0;

	      return this.resetTime() && this.zone(0);
	    }
	  },

	  firstOrLastDay: {
	    regex: /^(first|last) day of/i,
	    name: 'firstdayof | lastdayof',
	    callback: function callback(match, day) {
	      if (day.toLowerCase() === 'first') {
	        this.firstOrLastDayOfMonth = 1;
	      } else {
	        this.firstOrLastDayOfMonth = -1;
	      }
	    }
	  },

	  backOrFrontOf: {
	    regex: RegExp('^(back|front) of ' + reHour24 + reSpaceOpt + reMeridian + '?', 'i'),
	    name: 'backof | frontof',
	    callback: function callback(match, side, hours, meridian) {
	      var back = side.toLowerCase() === 'back';
	      var hour = +hours;
	      var minute = 15;

	      if (!back) {
	        hour -= 1;
	        minute = 45;
	      }

	      hour = processMeridian(hour, meridian);

	      return this.resetTime() && this.time(hour, minute, 0, 0);
	    }
	  },

	  weekdayOf: {
	    regex: RegExp('^(' + reReltextnumber + '|' + reReltexttext + ')' + reSpace + '(' + reDayfull + '|' + reDayabbr + ')' + reSpace + 'of', 'i'),
	    name: 'weekdayof'
	    // todo
	  },

	  mssqltime: {
	    regex: RegExp('^' + reHour12 + ':' + reMinutelz + ':' + reSecondlz + '[:.]([0-9]+)' + reMeridian, 'i'),
	    name: 'mssqltime',
	    callback: function callback(match, hour, minute, second, frac, meridian) {
	      return this.time(processMeridian(+hour, meridian), +minute, +second, +frac.substr(0, 3));
	    }
	  },

	  timeLong12: {
	    regex: RegExp('^' + reHour12 + '[:.]' + reMinute + '[:.]' + reSecondlz + reSpaceOpt + reMeridian, 'i'),
	    name: 'timelong12',
	    callback: function callback(match, hour, minute, second, meridian) {
	      return this.time(processMeridian(+hour, meridian), +minute, +second, 0);
	    }
	  },

	  timeShort12: {
	    regex: RegExp('^' + reHour12 + '[:.]' + reMinutelz + reSpaceOpt + reMeridian, 'i'),
	    name: 'timeshort12',
	    callback: function callback(match, hour, minute, meridian) {
	      return this.time(processMeridian(+hour, meridian), +minute, 0, 0);
	    }
	  },

	  timeTiny12: {
	    regex: RegExp('^' + reHour12 + reSpaceOpt + reMeridian, 'i'),
	    name: 'timetiny12',
	    callback: function callback(match, hour, meridian) {
	      return this.time(processMeridian(+hour, meridian), 0, 0, 0);
	    }
	  },

	  soap: {
	    regex: RegExp('^' + reYear4 + '-' + reMonthlz + '-' + reDaylz + 'T' + reHour24lz + ':' + reMinutelz + ':' + reSecondlz + reFrac + reTzCorrection + '?', 'i'),
	    name: 'soap',
	    callback: function callback(match, year, month, day, hour, minute, second, frac, tzCorrection) {
	      return this.ymd(+year, month - 1, +day) && this.time(+hour, +minute, +second, +frac.substr(0, 3)) && this.zone(processTzCorrection(tzCorrection));
	    }
	  },

	  wddx: {
	    regex: RegExp('^' + reYear4 + '-' + reMonth + '-' + reDay + 'T' + reHour24 + ':' + reMinute + ':' + reSecond),
	    name: 'wddx',
	    callback: function callback(match, year, month, day, hour, minute, second) {
	      return this.ymd(+year, month - 1, +day) && this.time(+hour, +minute, +second, 0);
	    }
	  },

	  exif: {
	    regex: RegExp('^' + reYear4 + ':' + reMonthlz + ':' + reDaylz + ' ' + reHour24lz + ':' + reMinutelz + ':' + reSecondlz, 'i'),
	    name: 'exif',
	    callback: function callback(match, year, month, day, hour, minute, second) {
	      return this.ymd(+year, month - 1, +day) && this.time(+hour, +minute, +second, 0);
	    }
	  },

	  xmlRpc: {
	    regex: RegExp('^' + reYear4 + reMonthlz + reDaylz + 'T' + reHour24 + ':' + reMinutelz + ':' + reSecondlz),
	    name: 'xmlrpc',
	    callback: function callback(match, year, month, day, hour, minute, second) {
	      return this.ymd(+year, month - 1, +day) && this.time(+hour, +minute, +second, 0);
	    }
	  },

	  xmlRpcNoColon: {
	    regex: RegExp('^' + reYear4 + reMonthlz + reDaylz + '[Tt]' + reHour24 + reMinutelz + reSecondlz),
	    name: 'xmlrpcnocolon',
	    callback: function callback(match, year, month, day, hour, minute, second) {
	      return this.ymd(+year, month - 1, +day) && this.time(+hour, +minute, +second, 0);
	    }
	  },

	  clf: {
	    regex: RegExp('^' + reDay + '/(' + reMonthAbbr + ')/' + reYear4 + ':' + reHour24lz + ':' + reMinutelz + ':' + reSecondlz + reSpace + reTzCorrection, 'i'),
	    name: 'clf',
	    callback: function callback(match, day, month, year, hour, minute, second, tzCorrection) {
	      return this.ymd(+year, lookupMonth(month), +day) && this.time(+hour, +minute, +second, 0) && this.zone(processTzCorrection(tzCorrection));
	    }
	  },

	  iso8601long: {
	    regex: RegExp('^t?' + reHour24 + '[:.]' + reMinute + '[:.]' + reSecond + reFrac, 'i'),
	    name: 'iso8601long',
	    callback: function callback(match, hour, minute, second, frac) {
	      return this.time(+hour, +minute, +second, +frac.substr(0, 3));
	    }
	  },

	  dateTextual: {
	    regex: RegExp('^' + reMonthText + '[ .\\t-]*' + reDay + '[,.stndrh\\t ]+' + reYear, 'i'),
	    name: 'datetextual',
	    callback: function callback(match, month, day, year) {
	      return this.ymd(processYear(year), lookupMonth(month), +day);
	    }
	  },

	  pointedDate4: {
	    regex: RegExp('^' + reDay + '[.\\t-]' + reMonth + '[.-]' + reYear4),
	    name: 'pointeddate4',
	    callback: function callback(match, day, month, year) {
	      return this.ymd(+year, month - 1, +day);
	    }
	  },

	  pointedDate2: {
	    regex: RegExp('^' + reDay + '[.\\t]' + reMonth + '\\.' + reYear2),
	    name: 'pointeddate2',
	    callback: function callback(match, day, month, year) {
	      return this.ymd(processYear(year), month - 1, +day);
	    }
	  },

	  timeLong24: {
	    regex: RegExp('^t?' + reHour24 + '[:.]' + reMinute + '[:.]' + reSecond),
	    name: 'timelong24',
	    callback: function callback(match, hour, minute, second) {
	      return this.time(+hour, +minute, +second, 0);
	    }
	  },

	  dateNoColon: {
	    regex: RegExp('^' + reYear4 + reMonthlz + reDaylz),
	    name: 'datenocolon',
	    callback: function callback(match, year, month, day) {
	      return this.ymd(+year, month - 1, +day);
	    }
	  },

	  pgydotd: {
	    regex: RegExp('^' + reYear4 + '\\.?' + reDayOfYear),
	    name: 'pgydotd',
	    callback: function callback(match, year, day) {
	      return this.ymd(+year, 0, +day);
	    }
	  },

	  timeShort24: {
	    regex: RegExp('^t?' + reHour24 + '[:.]' + reMinute, 'i'),
	    name: 'timeshort24',
	    callback: function callback(match, hour, minute) {
	      return this.time(+hour, +minute, 0, 0);
	    }
	  },

	  iso8601noColon: {
	    regex: RegExp('^t?' + reHour24lz + reMinutelz + reSecondlz, 'i'),
	    name: 'iso8601nocolon',
	    callback: function callback(match, hour, minute, second) {
	      return this.time(+hour, +minute, +second, 0);
	    }
	  },

	  iso8601dateSlash: {
	    // eventhough the trailing slash is optional in PHP
	    // here it's mandatory and inputs without the slash
	    // are handled by dateslash
	    regex: RegExp('^' + reYear4 + '/' + reMonthlz + '/' + reDaylz + '/'),
	    name: 'iso8601dateslash',
	    callback: function callback(match, year, month, day) {
	      return this.ymd(+year, month - 1, +day);
	    }
	  },

	  dateSlash: {
	    regex: RegExp('^' + reYear4 + '/' + reMonth + '/' + reDay),
	    name: 'dateslash',
	    callback: function callback(match, year, month, day) {
	      return this.ymd(+year, month - 1, +day);
	    }
	  },

	  american: {
	    regex: RegExp('^' + reMonth + '/' + reDay + '/' + reYear),
	    name: 'american',
	    callback: function callback(match, month, day, year) {
	      return this.ymd(processYear(year), month - 1, +day);
	    }
	  },

	  americanShort: {
	    regex: RegExp('^' + reMonth + '/' + reDay),
	    name: 'americanshort',
	    callback: function callback(match, month, day) {
	      return this.ymd(this.y, month - 1, +day);
	    }
	  },

	  gnuDateShortOrIso8601date2: {
	    // iso8601date2 is complete subset of gnudateshort
	    regex: RegExp('^' + reYear + '-' + reMonth + '-' + reDay),
	    name: 'gnudateshort | iso8601date2',
	    callback: function callback(match, year, month, day) {
	      return this.ymd(processYear(year), month - 1, +day);
	    }
	  },

	  iso8601date4: {
	    regex: RegExp('^' + reYear4withSign + '-' + reMonthlz + '-' + reDaylz),
	    name: 'iso8601date4',
	    callback: function callback(match, year, month, day) {
	      return this.ymd(+year, month - 1, +day);
	    }
	  },

	  gnuNoColon: {
	    regex: RegExp('^t?' + reHour24lz + reMinutelz, 'i'),
	    name: 'gnunocolon',
	    callback: function callback(match, hour, minute) {
	      // this rule is a special case
	      // if time was already set once by any preceding rule, it sets the captured value as year
	      switch (this.times) {
	        case 0:
	          return this.time(+hour, +minute, 0, this.f);
	        case 1:
	          this.y = hour * 100 + +minute;
	          this.times++;

	          return true;
	        default:
	          return false;
	      }
	    }
	  },

	  gnuDateShorter: {
	    regex: RegExp('^' + reYear4 + '-' + reMonth),
	    name: 'gnudateshorter',
	    callback: function callback(match, year, month) {
	      return this.ymd(+year, month - 1, 1);
	    }
	  },

	  pgTextReverse: {
	    // note: allowed years are from 32-9999
	    // years below 32 should be treated as days in datefull
	    regex: RegExp('^' + '(\\d{3,4}|[4-9]\\d|3[2-9])-(' + reMonthAbbr + ')-' + reDaylz, 'i'),
	    name: 'pgtextreverse',
	    callback: function callback(match, year, month, day) {
	      return this.ymd(processYear(year), lookupMonth(month), +day);
	    }
	  },

	  dateFull: {
	    regex: RegExp('^' + reDay + '[ \\t.-]*' + reMonthText + '[ \\t.-]*' + reYear, 'i'),
	    name: 'datefull',
	    callback: function callback(match, day, month, year) {
	      return this.ymd(processYear(year), lookupMonth(month), +day);
	    }
	  },

	  dateNoDay: {
	    regex: RegExp('^' + reMonthText + '[ .\\t-]*' + reYear4, 'i'),
	    name: 'datenoday',
	    callback: function callback(match, month, year) {
	      return this.ymd(+year, lookupMonth(month), 1);
	    }
	  },

	  dateNoDayRev: {
	    regex: RegExp('^' + reYear4 + '[ .\\t-]*' + reMonthText, 'i'),
	    name: 'datenodayrev',
	    callback: function callback(match, year, month) {
	      return this.ymd(+year, lookupMonth(month), 1);
	    }
	  },

	  pgTextShort: {
	    regex: RegExp('^(' + reMonthAbbr + ')-' + reDaylz + '-' + reYear, 'i'),
	    name: 'pgtextshort',
	    callback: function callback(match, month, day, year) {
	      return this.ymd(processYear(year), lookupMonth(month), +day);
	    }
	  },

	  dateNoYear: {
	    regex: RegExp('^' + reDateNoYear, 'i'),
	    name: 'datenoyear',
	    callback: function callback(match, month, day) {
	      return this.ymd(this.y, lookupMonth(month), +day);
	    }
	  },

	  dateNoYearRev: {
	    regex: RegExp('^' + reDay + '[ .\\t-]*' + reMonthText, 'i'),
	    name: 'datenoyearrev',
	    callback: function callback(match, day, month) {
	      return this.ymd(this.y, lookupMonth(month), +day);
	    }
	  },

	  isoWeekDay: {
	    regex: RegExp('^' + reYear4 + '-?W' + reWeekOfYear + '(?:-?([0-7]))?'),
	    name: 'isoweekday | isoweek',
	    callback: function callback(match, year, week, day) {
	      day = day ? +day : 1;

	      if (!this.ymd(+year, 0, 1)) {
	        return false;
	      }

	      // get day of week for Jan 1st
	      var dayOfWeek = new Date(this.y, this.m, this.d).getDay();

	      // and use the day to figure out the offset for day 1 of week 1
	      dayOfWeek = 0 - (dayOfWeek > 4 ? dayOfWeek - 7 : dayOfWeek);

	      this.rd += dayOfWeek + (week - 1) * 7 + day;
	    }
	  },

	  relativeText: {
	    regex: RegExp('^(' + reReltextnumber + '|' + reReltexttext + ')' + reSpace + '(' + reReltextunit + ')', 'i'),
	    name: 'relativetext',
	    callback: function callback(match, relValue, relUnit) {
	      // todo: implement handling of 'this time-unit'
	      // eslint-disable-next-line no-unused-vars
	      var _lookupRelative = lookupRelative(relValue),
	          amount = _lookupRelative.amount;

	      switch (relUnit.toLowerCase()) {
	        case 'sec':
	        case 'secs':
	        case 'second':
	        case 'seconds':
	          this.rs += amount;
	          break;
	        case 'min':
	        case 'mins':
	        case 'minute':
	        case 'minutes':
	          this.ri += amount;
	          break;
	        case 'hour':
	        case 'hours':
	          this.rh += amount;
	          break;
	        case 'day':
	        case 'days':
	          this.rd += amount;
	          break;
	        case 'fortnight':
	        case 'fortnights':
	        case 'forthnight':
	        case 'forthnights':
	          this.rd += amount * 14;
	          break;
	        case 'week':
	        case 'weeks':
	          this.rd += amount * 7;
	          break;
	        case 'month':
	        case 'months':
	          this.rm += amount;
	          break;
	        case 'year':
	        case 'years':
	          this.ry += amount;
	          break;
	        case 'mon':case 'monday':
	        case 'tue':case 'tuesday':
	        case 'wed':case 'wednesday':
	        case 'thu':case 'thursday':
	        case 'fri':case 'friday':
	        case 'sat':case 'saturday':
	        case 'sun':case 'sunday':
	          this.resetTime();
	          this.weekday = lookupWeekday(relUnit, 7);
	          this.weekdayBehavior = 1;
	          this.rd += (amount > 0 ? amount - 1 : amount) * 7;
	          break;
	      }
	    }
	  },

	  relative: {
	    regex: RegExp('^([+-]*)[ \\t]*(\\d+)' + reSpaceOpt + '(' + reReltextunit + '|week)', 'i'),
	    name: 'relative',
	    callback: function callback(match, signs, relValue, relUnit) {
	      var minuses = signs.replace(/[^-]/g, '').length;

	      var amount = +relValue * Math.pow(-1, minuses);

	      switch (relUnit.toLowerCase()) {
	        case 'sec':
	        case 'secs':
	        case 'second':
	        case 'seconds':
	          this.rs += amount;
	          break;
	        case 'min':
	        case 'mins':
	        case 'minute':
	        case 'minutes':
	          this.ri += amount;
	          break;
	        case 'hour':
	        case 'hours':
	          this.rh += amount;
	          break;
	        case 'day':
	        case 'days':
	          this.rd += amount;
	          break;
	        case 'fortnight':
	        case 'fortnights':
	        case 'forthnight':
	        case 'forthnights':
	          this.rd += amount * 14;
	          break;
	        case 'week':
	        case 'weeks':
	          this.rd += amount * 7;
	          break;
	        case 'month':
	        case 'months':
	          this.rm += amount;
	          break;
	        case 'year':
	        case 'years':
	          this.ry += amount;
	          break;
	        case 'mon':case 'monday':
	        case 'tue':case 'tuesday':
	        case 'wed':case 'wednesday':
	        case 'thu':case 'thursday':
	        case 'fri':case 'friday':
	        case 'sat':case 'saturday':
	        case 'sun':case 'sunday':
	          this.resetTime();
	          this.weekday = lookupWeekday(relUnit, 7);
	          this.weekdayBehavior = 1;
	          this.rd += (amount > 0 ? amount - 1 : amount) * 7;
	          break;
	      }
	    }
	  },

	  dayText: {
	    regex: RegExp('^(' + reDaytext + ')', 'i'),
	    name: 'daytext',
	    callback: function callback(match, dayText) {
	      this.resetTime();
	      this.weekday = lookupWeekday(dayText, 0);

	      if (this.weekdayBehavior !== 2) {
	        this.weekdayBehavior = 1;
	      }
	    }
	  },

	  relativeTextWeek: {
	    regex: RegExp('^(' + reReltexttext + ')' + reSpace + 'week', 'i'),
	    name: 'relativetextweek',
	    callback: function callback(match, relText) {
	      this.weekdayBehavior = 2;

	      switch (relText.toLowerCase()) {
	        case 'this':
	          this.rd += 0;
	          break;
	        case 'next':
	          this.rd += 7;
	          break;
	        case 'last':
	        case 'previous':
	          this.rd -= 7;
	          break;
	      }

	      if (isNaN(this.weekday)) {
	        this.weekday = 1;
	      }
	    }
	  },

	  monthFullOrMonthAbbr: {
	    regex: RegExp('^(' + reMonthFull + '|' + reMonthAbbr + ')', 'i'),
	    name: 'monthfull | monthabbr',
	    callback: function callback(match, month) {
	      return this.ymd(this.y, lookupMonth(month), this.d);
	    }
	  },

	  tzCorrection: {
	    regex: RegExp('^' + reTzCorrection, 'i'),
	    name: 'tzcorrection',
	    callback: function callback(tzCorrection) {
	      return this.zone(processTzCorrection(tzCorrection));
	    }
	  },

	  tzAbbr: {
	    regex: RegExp('^' + reTzAbbr),
	    name: 'tzabbr',
	    callback: function callback(match, abbr) {
	      var offset = tzAbbrOffsets[abbr.toLowerCase()];

	      if (isNaN(offset)) {
	        return false;
	      }

	      return this.zone(offset);
	    }
	  },

	  ago: {
	    regex: /^ago/i,
	    name: 'ago',
	    callback: function callback() {
	      this.ry = -this.ry;
	      this.rm = -this.rm;
	      this.rd = -this.rd;
	      this.rh = -this.rh;
	      this.ri = -this.ri;
	      this.rs = -this.rs;
	      this.rf = -this.rf;
	    }
	  },

	  year4: {
	    regex: RegExp('^' + reYear4),
	    name: 'year4',
	    callback: function callback(match, year) {
	      this.y = +year;
	      return true;
	    }
	  },

	  whitespace: {
	    regex: /^[ .,\t]+/,
	    name: 'whitespace'
	    // do nothing
	  },

	  dateShortWithTimeLong: {
	    regex: RegExp('^' + reDateNoYear + 't?' + reHour24 + '[:.]' + reMinute + '[:.]' + reSecond, 'i'),
	    name: 'dateshortwithtimelong',
	    callback: function callback(match, month, day, hour, minute, second) {
	      return this.ymd(this.y, lookupMonth(month), +day) && this.time(+hour, +minute, +second, 0);
	    }
	  },

	  dateShortWithTimeLong12: {
	    regex: RegExp('^' + reDateNoYear + reHour12 + '[:.]' + reMinute + '[:.]' + reSecondlz + reSpaceOpt + reMeridian, 'i'),
	    name: 'dateshortwithtimelong12',
	    callback: function callback(match, month, day, hour, minute, second, meridian) {
	      return this.ymd(this.y, lookupMonth(month), +day) && this.time(processMeridian(+hour, meridian), +minute, +second, 0);
	    }
	  },

	  dateShortWithTimeShort: {
	    regex: RegExp('^' + reDateNoYear + 't?' + reHour24 + '[:.]' + reMinute, 'i'),
	    name: 'dateshortwithtimeshort',
	    callback: function callback(match, month, day, hour, minute) {
	      return this.ymd(this.y, lookupMonth(month), +day) && this.time(+hour, +minute, 0, 0);
	    }
	  },

	  dateShortWithTimeShort12: {
	    regex: RegExp('^' + reDateNoYear + reHour12 + '[:.]' + reMinutelz + reSpaceOpt + reMeridian, 'i'),
	    name: 'dateshortwithtimeshort12',
	    callback: function callback(match, month, day, hour, minute, meridian) {
	      return this.ymd(this.y, lookupMonth(month), +day) && this.time(processMeridian(+hour, meridian), +minute, 0, 0);
	    }
	  }
	};

	var resultProto = {
	  // date
	  y: NaN,
	  m: NaN,
	  d: NaN,
	  // time
	  h: NaN,
	  i: NaN,
	  s: NaN,
	  f: NaN,

	  // relative shifts
	  ry: 0,
	  rm: 0,
	  rd: 0,
	  rh: 0,
	  ri: 0,
	  rs: 0,
	  rf: 0,

	  // weekday related shifts
	  weekday: NaN,
	  weekdayBehavior: 0,

	  // first or last day of month
	  // 0 none, 1 first, -1 last
	  firstOrLastDayOfMonth: 0,

	  // timezone correction in minutes
	  z: NaN,

	  // counters
	  dates: 0,
	  times: 0,
	  zones: 0,

	  // helper functions
	  ymd: function ymd(y, m, d) {
	    if (this.dates > 0) {
	      return false;
	    }

	    this.dates++;
	    this.y = y;
	    this.m = m;
	    this.d = d;
	    return true;
	  },
	  time: function time(h, i, s, f) {
	    if (this.times > 0) {
	      return false;
	    }

	    this.times++;
	    this.h = h;
	    this.i = i;
	    this.s = s;
	    this.f = f;

	    return true;
	  },
	  resetTime: function resetTime() {
	    this.h = 0;
	    this.i = 0;
	    this.s = 0;
	    this.f = 0;
	    this.times = 0;

	    return true;
	  },
	  zone: function zone(minutes) {
	    if (this.zones <= 1) {
	      this.zones++;
	      this.z = minutes;
	      return true;
	    }

	    return false;
	  },
	  toDate: function toDate(relativeTo) {
	    if (this.dates && !this.times) {
	      this.h = this.i = this.s = this.f = 0;
	    }

	    // fill holes
	    if (isNaN(this.y)) {
	      this.y = relativeTo.getFullYear();
	    }

	    if (isNaN(this.m)) {
	      this.m = relativeTo.getMonth();
	    }

	    if (isNaN(this.d)) {
	      this.d = relativeTo.getDate();
	    }

	    if (isNaN(this.h)) {
	      this.h = relativeTo.getHours();
	    }

	    if (isNaN(this.i)) {
	      this.i = relativeTo.getMinutes();
	    }

	    if (isNaN(this.s)) {
	      this.s = relativeTo.getSeconds();
	    }

	    if (isNaN(this.f)) {
	      this.f = relativeTo.getMilliseconds();
	    }

	    // adjust special early
	    switch (this.firstOrLastDayOfMonth) {
	      case 1:
	        this.d = 1;
	        break;
	      case -1:
	        this.d = 0;
	        this.m += 1;
	        break;
	    }

	    if (!isNaN(this.weekday)) {
	      var date = new Date(relativeTo.getTime());
	      date.setFullYear(this.y, this.m, this.d);
	      date.setHours(this.h, this.i, this.s, this.f);

	      var dow = date.getDay();

	      if (this.weekdayBehavior === 2) {
	        // To make "this week" work, where the current day of week is a "sunday"
	        if (dow === 0 && this.weekday !== 0) {
	          this.weekday = -6;
	        }

	        // To make "sunday this week" work, where the current day of week is not a "sunday"
	        if (this.weekday === 0 && dow !== 0) {
	          this.weekday = 7;
	        }

	        this.d -= dow;
	        this.d += this.weekday;
	      } else {
	        var diff = this.weekday - dow;

	        // some PHP magic
	        if (this.rd < 0 && diff < 0 || this.rd >= 0 && diff <= -this.weekdayBehavior) {
	          diff += 7;
	        }

	        if (this.weekday >= 0) {
	          this.d += diff;
	        } else {
	          this.d -= 7 - (Math.abs(this.weekday) - dow);
	        }

	        this.weekday = NaN;
	      }
	    }

	    // adjust relative
	    this.y += this.ry;
	    this.m += this.rm;
	    this.d += this.rd;

	    this.h += this.rh;
	    this.i += this.ri;
	    this.s += this.rs;
	    this.f += this.rf;

	    this.ry = this.rm = this.rd = 0;
	    this.rh = this.ri = this.rs = this.rf = 0;

	    var result = new Date(relativeTo.getTime());
	    // since Date constructor treats years <= 99 as 1900+
	    // it can't be used, thus this weird way
	    result.setFullYear(this.y, this.m, this.d);
	    result.setHours(this.h, this.i, this.s, this.f);

	    // note: this is done twice in PHP
	    // early when processing special relatives
	    // and late
	    // todo: check if the logic can be reduced
	    // to just one time action
	    switch (this.firstOrLastDayOfMonth) {
	      case 1:
	        result.setDate(1);
	        break;
	      case -1:
	        result.setMonth(result.getMonth() + 1, 0);
	        break;
	    }

	    // adjust timezone
	    if (!isNaN(this.z) && result.getTimezoneOffset() !== this.z) {
	      result.setUTCFullYear(result.getFullYear(), result.getMonth(), result.getDate());

	      result.setUTCHours(result.getHours(), result.getMinutes(), result.getSeconds() - this.z, result.getMilliseconds());
	    }

	    return result;
	  }
	};

	var strtotime = function strtotime(str, now) {
	  //       discuss at: https://locutus.io/php/strtotime/
	  //      original by: Caio Ariede (https://caioariede.com)
	  //      improved by: Kevin van Zonneveld (https://kvz.io)
	  //      improved by: Caio Ariede (https://caioariede.com)
	  //      improved by: A. Matías Quezada (https://amatiasq.com)
	  //      improved by: preuter
	  //      improved by: Brett Zamir (https://brett-zamir.me)
	  //      improved by: Mirko Faber
	  //         input by: David
	  //      bugfixed by: Wagner B. Soares
	  //      bugfixed by: Artur Tchernychev
	  //      bugfixed by: Stephan Bösch-Plepelits (https://github.com/plepe)
	  // reimplemented by: Rafał Kukawski
	  //           note 1: Examples all have a fixed timestamp to prevent
	  //           note 1: tests to fail because of variable time(zones)
	  //        example 1: strtotime('+1 day', 1129633200)
	  //        returns 1: 1129719600
	  //        example 2: strtotime('+1 week 2 days 4 hours 2 seconds', 1129633200)
	  //        returns 2: 1130425202
	  //        example 3: strtotime('last month', 1129633200)
	  //        returns 3: 1127041200
	  //        example 4: strtotime('2009-05-04 08:30:00+00')
	  //        returns 4: 1241425800
	  //        example 5: strtotime('2009-05-04 08:30:00+02:00')
	  //        returns 5: 1241418600
	  //        example 6: strtotime('2009-05-04 08:30:00 YWT')
	  //        returns 6: 1241454600

	  if (now == null) {
	    now = Math.floor(Date.now() / 1000);
	  }

	  // the rule order is important
	  // if multiple rules match, the longest match wins
	  // if multiple rules match the same string, the first match wins
	  var rules = [formats.yesterday, formats.now, formats.noon, formats.midnightOrToday, formats.tomorrow, formats.timestamp, formats.firstOrLastDay, formats.backOrFrontOf,
	  // formats.weekdayOf, // not yet implemented
	  formats.timeTiny12, formats.timeShort12, formats.timeLong12, formats.mssqltime, formats.timeShort24, formats.timeLong24, formats.iso8601long, formats.gnuNoColon, formats.iso8601noColon, formats.americanShort, formats.american, formats.iso8601date4, formats.iso8601dateSlash, formats.dateSlash, formats.gnuDateShortOrIso8601date2, formats.gnuDateShorter, formats.dateFull, formats.pointedDate4, formats.pointedDate2, formats.dateNoDay, formats.dateNoDayRev, formats.dateTextual, formats.dateNoYear, formats.dateNoYearRev, formats.dateNoColon, formats.xmlRpc, formats.xmlRpcNoColon, formats.soap, formats.wddx, formats.exif, formats.pgydotd, formats.isoWeekDay, formats.pgTextShort, formats.pgTextReverse, formats.clf, formats.year4, formats.ago, formats.dayText, formats.relativeTextWeek, formats.relativeText, formats.monthFullOrMonthAbbr, formats.tzCorrection, formats.tzAbbr, formats.dateShortWithTimeShort12, formats.dateShortWithTimeLong12, formats.dateShortWithTimeShort, formats.dateShortWithTimeLong, formats.relative, formats.whitespace];

	  var result = Object.create(resultProto);

	  while (str.length) {
	    var longestMatch = null;
	    var finalRule = null;

	    for (var i = 0, l = rules.length; i < l; i++) {
	      var format = rules[i];

	      var match = str.match(format.regex);

	      if (match) {
	        if (!longestMatch || match[0].length > longestMatch[0].length) {
	          longestMatch = match;
	          finalRule = format;
	        }
	      }
	    }

	    if (!finalRule || finalRule.callback && finalRule.callback.apply(result, longestMatch) === false) {
	      return false;
	    }

	    str = str.substr(longestMatch[0].length);
	    finalRule = null;
	    longestMatch = null;
	  }

	  return Math.floor(result.toDate(new Date(now * 1000)) / 1000);
	};

	var is_numeric = function is_numeric(mixedVar) {
	  // eslint-disable-line camelcase
	  //  discuss at: https://locutus.io/php/is_numeric/
	  // original by: Kevin van Zonneveld (https://kvz.io)
	  // improved by: David
	  // improved by: taith
	  // bugfixed by: Tim de Koning
	  // bugfixed by: WebDevHobo (https://webdevhobo.blogspot.com/)
	  // bugfixed by: Brett Zamir (https://brett-zamir.me)
	  // bugfixed by: Denis Chenu (https://shnoulle.net)
	  //   example 1: is_numeric(186.31)
	  //   returns 1: true
	  //   example 2: is_numeric('Kevin van Zonneveld')
	  //   returns 2: false
	  //   example 3: is_numeric(' +186.31e2')
	  //   returns 3: true
	  //   example 4: is_numeric('')
	  //   returns 4: false
	  //   example 5: is_numeric([])
	  //   returns 5: false
	  //   example 6: is_numeric('1 ')
	  //   returns 6: false

	  var whitespace = [' ', '\n', '\r', '\t', '\f', '\x0b', '\xa0', '\u2000', '\u2001', '\u2002', '\u2003', '\u2004', '\u2005', '\u2006', '\u2007', '\u2008', '\u2009', '\u200A', '\u200B', '\u2028', '\u2029', '\u3000'].join('');

	  // @todo: Break this up using many single conditions with early returns
	  return (typeof mixedVar === 'number' || typeof mixedVar === 'string' && whitespace.indexOf(mixedVar.slice(-1)) === -1) && mixedVar !== '' && !isNaN(mixedVar);
	};

	var array_diff = function array_diff(arr1) {
	  // eslint-disable-line camelcase
	  //  discuss at: https://locutus.io/php/array_diff/
	  // original by: Kevin van Zonneveld (https://kvz.io)
	  // improved by: Sanjoy Roy
	  //  revised by: Brett Zamir (https://brett-zamir.me)
	  //   example 1: array_diff(['Kevin', 'van', 'Zonneveld'], ['van', 'Zonneveld'])
	  //   returns 1: {0:'Kevin'}

	  var retArr = {};
	  var argl = arguments.length;
	  var k1 = '';
	  var i = 1;
	  var k = '';
	  var arr = {};

	  arr1keys: for (k1 in arr1) {
	    // eslint-disable-line no-labels
	    for (i = 1; i < argl; i++) {
	      arr = arguments[i];
	      for (k in arr) {
	        if (arr[k] === arr1[k1]) {
	          // If it reaches here, it was found in at least one array, so try next value
	          continue arr1keys; // eslint-disable-line no-labels
	        }
	      }
	      retArr[k1] = arr1[k1];
	    }
	  }

	  return retArr;
	};

	var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

	var str_replace = function str_replace(search, replace, subject, countObj) {
	  // eslint-disable-line camelcase
	  //  discuss at: https://locutus.io/php/str_replace/
	  // original by: Kevin van Zonneveld (https://kvz.io)
	  // improved by: Gabriel Paderni
	  // improved by: Philip Peterson
	  // improved by: Simon Willison (https://simonwillison.net)
	  // improved by: Kevin van Zonneveld (https://kvz.io)
	  // improved by: Onno Marsman (https://twitter.com/onnomarsman)
	  // improved by: Brett Zamir (https://brett-zamir.me)
	  //  revised by: Jonas Raoni Soares Silva (https://www.jsfromhell.com)
	  // bugfixed by: Anton Ongson
	  // bugfixed by: Kevin van Zonneveld (https://kvz.io)
	  // bugfixed by: Oleg Eremeev
	  // bugfixed by: Glen Arason (https://CanadianDomainRegistry.ca)
	  // bugfixed by: Glen Arason (https://CanadianDomainRegistry.ca)
	  // bugfixed by: Mahmoud Saeed
	  //    input by: Onno Marsman (https://twitter.com/onnomarsman)
	  //    input by: Brett Zamir (https://brett-zamir.me)
	  //    input by: Oleg Eremeev
	  //      note 1: The countObj parameter (optional) if used must be passed in as a
	  //      note 1: object. The count will then be written by reference into it's `value` property
	  //   example 1: str_replace(' ', '.', 'Kevin van Zonneveld')
	  //   returns 1: 'Kevin.van.Zonneveld'
	  //   example 2: str_replace(['{name}', 'l'], ['hello', 'm'], '{name}, lars')
	  //   returns 2: 'hemmo, mars'
	  //   example 3: str_replace(Array('S','F'),'x','ASDFASDF')
	  //   returns 3: 'AxDxAxDx'
	  //   example 4: var countObj = {}
	  //   example 4: str_replace(['A','D'], ['x','y'] , 'ASDFASDF' , countObj)
	  //   example 4: var $result = countObj.value
	  //   returns 4: 4
	  //   example 5: str_replace('', '.', 'aaa')
	  //   returns 5: 'aaa'

	  var i = 0;
	  var j = 0;
	  var temp = '';
	  var repl = '';
	  var sl = 0;
	  var fl = 0;
	  var f = [].concat(search);
	  var r = [].concat(replace);
	  var s = subject;
	  var ra = Object.prototype.toString.call(r) === '[object Array]';
	  var sa = Object.prototype.toString.call(s) === '[object Array]';
	  s = [].concat(s);

	  var $global = typeof window !== 'undefined' ? window : commonjsGlobal;
	  $global.$locutus = $global.$locutus || {};
	  var $locutus = $global.$locutus;
	  $locutus.php = $locutus.php || {};

	  if ((typeof search === 'undefined' ? 'undefined' : _typeof(search)) === 'object' && typeof replace === 'string') {
	    temp = replace;
	    replace = [];
	    for (i = 0; i < search.length; i += 1) {
	      replace[i] = temp;
	    }
	    temp = '';
	    r = [].concat(replace);
	    ra = Object.prototype.toString.call(r) === '[object Array]';
	  }

	  if (typeof countObj !== 'undefined') {
	    countObj.value = 0;
	  }

	  for (i = 0, sl = s.length; i < sl; i++) {
	    if (s[i] === '') {
	      continue;
	    }
	    for (j = 0, fl = f.length; j < fl; j++) {
	      if (f[j] === '') {
	        continue;
	      }
	      temp = s[i] + '';
	      repl = ra ? r[j] !== undefined ? r[j] : '' : r[0];
	      s[i] = temp.split(f[j]).join(repl);
	      if (typeof countObj !== 'undefined') {
	        countObj.value += temp.split(f[j]).length - 1;
	      }
	    }
	  }
	  return sa ? s : s[0];
	};

	var php = {
	  echo: echo,
	  strtotime: strtotime,
	  is_numeric: is_numeric,
	  array_diff: array_diff,
	  ucfirst: ucfirst,
	  str_replace: str_replace
	};

	/**
	 * Returns a promise that resolves when an element with a selector appears on the page for the first time.
	 * Note: Use elementReadyRAF if this is too slow or unreliable.
	 * @param {string} selector querySelector string
	 * @return {Promise} promise onready
	 */
	var elementReady = function elementReady(selector) {
	  // eslint-disable-next-line no-unused-vars
	  return new Promise(function (resolve, reject) {
	    // eslint-disable-next-line no-unused-vars
	    var observer = new MutationObserver(function (mutations) {
	      var elements = document.querySelectorAll(selector);
	      elements.forEach(function (element) {
	        if (!element.ready) {
	          element.ready = true;
	          observer.disconnect();
	          resolve(element);
	        }
	      });
	    });
	    observer.observe(document.documentElement, {
	      childList: true,
	      subtree: true
	    });
	  });
	};
	/**
	 * Calls the callback function whenever an element with the selector gets rendered
	 * @param {string} selector querySelector string
	 * @param {function} callback function to fire when an element gets rendered
	 * @return {MutationObserver} the object that checks for the elements
	 */

	var elementRendered = function elementRendered(selector, callback) {
	  var renderedElements = []; // eslint-disable-next-line no-unused-vars

	  var observer = new MutationObserver(function (mutations) {
	    var elements = document.querySelectorAll(selector);
	    elements.forEach(function (element) {
	      if (!renderedElements.includes(element)) {
	        renderedElements.push(element);
	        callback(element);
	      }
	    });
	  });
	  observer.observe(document.documentElement, {
	    childList: true,
	    subtree: true
	  });
	  return observer;
	};

	var defaultConfirmHandler = function defaultConfirmHandler(el, options, confirmCallback) {
	  if (window.bootbox) {
	    return bootboxConfirmhandler(el, options, confirmCallback);
	  } // eslint-disable-next-line no-alert


	  var confirmed = window.confirm(options.message);
	  confirmCallback(confirmed);
	};

	var bootboxConfirmhandler = function bootboxConfirmhandler(el, options, confirmCallback) {
	  window.bootbox.confirm({
	    className: 'capp-modal-confirm',
	    message: options.message,
	    callback: confirmCallback
	  });
	};

	var confirmFromElement = function confirmFromElement(el, handler, defaultMessage) {
	  var ahref = $(el).attr('href');
	  var message = $(el).attr('data-confirm-message');
	  var noDouble = $(el).attr('data-no-double');
	  var clicked = $(el).attr('data-clicked');
	  var btn = $(el);
	  btn.attr('data-clicked', '1');

	  if (btn.attr('type') === 'submit') {
	    btn.attr('data-submitted', '1');
	  }

	  if (noDouble) {
	    if (clicked) {
	      return false;
	    }
	  }

	  var confirmCallback = function confirmCallback(confirmed) {
	    if (confirmed) {
	      if (ahref) {
	        window.location.href = ahref;
	      } else if (btn.attr('type') === 'submit') {
	        btn.closest('form').submit();
	      } else {
	        btn.on('click');
	      }
	    } else {
	      btn.removeAttr('data-clicked');
	      btn.removeAttr('data-submitted');
	    }

	    setTimeout(function () {
	      var modalExists = $('.modal:visible').length > 0;

	      if (!modalExists) {
	        $('body').removeClass('modal-open');
	      } else {
	        $('body').addClass('modal-open');
	      }
	    }, 750);
	  };

	  message = message ? message : defaultMessage ? defaultMessage : 'Are you sure?';
	  var options = {
	    message: message
	  };
	  handler(btn, options, confirmCallback);
	};

	var ini_get = function ini_get(varname) {
	  // eslint-disable-line camelcase
	  //  discuss at: https://locutus.io/php/ini_get/
	  // original by: Brett Zamir (https://brett-zamir.me)
	  //      note 1: The ini values must be set by ini_set or manually within an ini file
	  //   example 1: ini_set('date.timezone', 'Asia/Hong_Kong')
	  //   example 1: ini_get('date.timezone')
	  //   returns 1: 'Asia/Hong_Kong'

	  var $global = typeof window !== 'undefined' ? window : commonjsGlobal;
	  $global.$locutus = $global.$locutus || {};
	  var $locutus = $global.$locutus;
	  $locutus.php = $locutus.php || {};
	  $locutus.php.ini = $locutus.php.ini || {};

	  if ($locutus.php.ini[varname] && $locutus.php.ini[varname].local_value !== undefined) {
	    if ($locutus.php.ini[varname].local_value === null) {
	      return '';
	    }
	    return $locutus.php.ini[varname].local_value;
	  }

	  return '';
	};

	var strlen = function strlen(string) {
	  //  discuss at: https://locutus.io/php/strlen/
	  // original by: Kevin van Zonneveld (https://kvz.io)
	  // improved by: Sakimori
	  // improved by: Kevin van Zonneveld (https://kvz.io)
	  //    input by: Kirk Strobeck
	  // bugfixed by: Onno Marsman (https://twitter.com/onnomarsman)
	  //  revised by: Brett Zamir (https://brett-zamir.me)
	  //      note 1: May look like overkill, but in order to be truly faithful to handling all Unicode
	  //      note 1: characters and to this function in PHP which does not count the number of bytes
	  //      note 1: but counts the number of characters, something like this is really necessary.
	  //   example 1: strlen('Kevin van Zonneveld')
	  //   returns 1: 19
	  //   example 2: ini_set('unicode.semantics', 'on')
	  //   example 2: strlen('A\ud87e\udc04Z')
	  //   returns 2: 3

	  var str = string + '';

	  var iniVal = (typeof commonjsRequire !== 'undefined' ? ini_get('unicode.semantics') : undefined) || 'off';
	  if (iniVal === 'off') {
	    return str.length;
	  }

	  var i = 0;
	  var lgth = 0;

	  var getWholeChar = function getWholeChar(str, i) {
	    var code = str.charCodeAt(i);
	    var next = '';
	    var prev = '';
	    if (code >= 0xD800 && code <= 0xDBFF) {
	      // High surrogate (could change last hex to 0xDB7F to
	      // treat high private surrogates as single characters)
	      if (str.length <= i + 1) {
	        throw new Error('High surrogate without following low surrogate');
	      }
	      next = str.charCodeAt(i + 1);
	      if (next < 0xDC00 || next > 0xDFFF) {
	        throw new Error('High surrogate without following low surrogate');
	      }
	      return str.charAt(i) + str.charAt(i + 1);
	    } else if (code >= 0xDC00 && code <= 0xDFFF) {
	      // Low surrogate
	      if (i === 0) {
	        throw new Error('Low surrogate without preceding high surrogate');
	      }
	      prev = str.charCodeAt(i - 1);
	      if (prev < 0xD800 || prev > 0xDBFF) {
	        // (could change last hex to 0xDB7F to treat high private surrogates
	        // as single characters)
	        throw new Error('Low surrogate without preceding high surrogate');
	      }
	      // We can pass over low surrogates now as the second
	      // component in a pair which we have already processed
	      return false;
	    }
	    return str.charAt(i);
	  };

	  for (i = 0, lgth = 0; i < str.length; i++) {
	    if (getWholeChar(str, i) === false) {
	      continue;
	    }
	    // Adapt this line at the top of any loop, passing in the whole string and
	    // the current iteration and returning a variable to represent the individual character;
	    // purpose is to treat the first part of a surrogate pair as the whole character and then
	    // ignore the second part
	    lgth++;
	  }

	  return lgth;
	};

	var DateFormatter, $h;
	/**
	     * Global helper object
	     */

	$h = {
	  DAY: 1000 * 60 * 60 * 24,
	  HOUR: 3600,
	  defaults: {
	    dateSettings: {
	      days: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
	      daysShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
	      months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
	      monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
	      meridiem: ['AM', 'PM'],
	      ordinal: function ordinal(number) {
	        var n = number % 10,
	            suffixes = {
	          1: 'st',
	          2: 'nd',
	          3: 'rd'
	        };
	        return Math.floor(number % 100 / 10) === 1 || !suffixes[n] ? 'th' : suffixes[n];
	      }
	    },
	    separators: /[ \-+\/.:@]/g,
	    validParts: /[dDjlNSwzWFmMntLoYyaABgGhHisueTIOPZcrU]/g,
	    intParts: /[djwNzmnyYhHgGis]/g,
	    tzParts: /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
	    tzClip: /[^-+\dA-Z]/g
	  },
	  getInt: function getInt(str, radix) {
	    return parseInt(str, radix ? radix : 10);
	  },
	  compare: function compare(str1, str2) {
	    return typeof str1 === 'string' && typeof str2 === 'string' && str1.toLowerCase() === str2.toLowerCase();
	  },
	  lpad: function lpad(value, length, chr) {
	    var val = value.toString();
	    chr = chr || '0';
	    return val.length < length ? $h.lpad(chr + val, length) : val;
	  },
	  merge: function merge(out) {
	    var i, obj;
	    out = out || {};

	    for (i = 1; i < arguments.length; i++) {
	      obj = arguments[i];

	      if (!obj) {
	        continue;
	      }

	      for (var key in obj) {
	        if (obj.hasOwnProperty(key)) {
	          if (_typeof$1(obj[key]) === 'object') {
	            $h.merge(out[key], obj[key]);
	          } else {
	            out[key] = obj[key];
	          }
	        }
	      }
	    }

	    return out;
	  },
	  getIndex: function getIndex(val, arr) {
	    for (var i = 0; i < arr.length; i++) {
	      if (arr[i].toLowerCase() === val.toLowerCase()) {
	        return i;
	      }
	    }

	    return -1;
	  }
	};

	DateFormatter = function DateFormatter(options) {
	  var self = this,
	      config = $h.merge($h.defaults, options);
	  self.dateSettings = config.dateSettings;
	  self.separators = config.separators;
	  self.validParts = config.validParts;
	  self.intParts = config.intParts;
	  self.tzParts = config.tzParts;
	  self.tzClip = config.tzClip;
	};
	/**
	     * DateFormatter Library Prototype
	     */


	DateFormatter.prototype = {
	  constructor: DateFormatter,
	  getMonth: function getMonth(val) {
	    var self = this,
	        i;
	    i = $h.getIndex(val, self.dateSettings.monthsShort) + 1;

	    if (i === 0) {
	      i = $h.getIndex(val, self.dateSettings.months) + 1;
	    }

	    return i;
	  },
	  parseDate: function parseDate(vDate, vFormat) {
	    var self = this,
	        vFormatParts,
	        vDateParts,
	        i,
	        vDateFlag = false,
	        vTimeFlag = false,
	        vDatePart,
	        iDatePart,
	        vSettings = self.dateSettings,
	        vMonth,
	        vMeriIndex,
	        vMeriOffset,
	        len,
	        mer,
	        out = {
	      date: null,
	      year: null,
	      month: null,
	      day: null,
	      hour: 0,
	      min: 0,
	      sec: 0
	    };

	    if (!vDate) {
	      return null;
	    }

	    if (vDate instanceof Date) {
	      return vDate;
	    }

	    if (vFormat === 'U') {
	      i = $h.getInt(vDate);
	      return i ? new Date(i * 1000) : vDate;
	    }

	    switch (_typeof$1(vDate)) {
	      case 'number':
	        return new Date(vDate);

	      case 'string':
	        break;

	      default:
	        return null;
	    }

	    vFormatParts = vFormat.match(self.validParts);

	    if (!vFormatParts || vFormatParts.length === 0) {
	      throw new Error('Invalid date format definition.');
	    }

	    for (i = vFormatParts.length - 1; i >= 0; i--) {
	      if (vFormatParts[i] === 'S') {
	        vFormatParts.splice(i, 1);
	      }
	    }

	    vDateParts = vDate.replace(self.separators, '\0').split('\0');

	    for (i = 0; i < vDateParts.length; i++) {
	      vDatePart = vDateParts[i];
	      iDatePart = $h.getInt(vDatePart);

	      switch (vFormatParts[i]) {
	        case 'y':
	        case 'Y':
	          if (iDatePart) {
	            len = vDatePart.length;
	            out.year = len === 2 ? $h.getInt((iDatePart < 70 ? '20' : '19') + vDatePart) : iDatePart;
	          } else {
	            return null;
	          }

	          vDateFlag = true;
	          break;

	        case 'm':
	        case 'n':
	        case 'M':
	        case 'F':
	          if (isNaN(iDatePart)) {
	            vMonth = self.getMonth(vDatePart);

	            if (vMonth > 0) {
	              out.month = vMonth;
	            } else {
	              return null;
	            }
	          } else {
	            if (iDatePart >= 1 && iDatePart <= 12) {
	              out.month = iDatePart;
	            } else {
	              return null;
	            }
	          }

	          vDateFlag = true;
	          break;

	        case 'd':
	        case 'j':
	          if (iDatePart >= 1 && iDatePart <= 31) {
	            out.day = iDatePart;
	          } else {
	            return null;
	          }

	          vDateFlag = true;
	          break;

	        case 'g':
	        case 'h':
	          vMeriIndex = vFormatParts.indexOf('a') > -1 ? vFormatParts.indexOf('a') : vFormatParts.indexOf('A') > -1 ? vFormatParts.indexOf('A') : -1;
	          mer = vDateParts[vMeriIndex];

	          if (vMeriIndex !== -1) {
	            vMeriOffset = $h.compare(mer, vSettings.meridiem[0]) ? 0 : $h.compare(mer, vSettings.meridiem[1]) ? 12 : -1;

	            if (iDatePart >= 1 && iDatePart <= 12 && vMeriOffset !== -1) {
	              out.hour = iDatePart % 12 === 0 ? vMeriOffset : iDatePart + vMeriOffset;
	            } else {
	              if (iDatePart >= 0 && iDatePart <= 23) {
	                out.hour = iDatePart;
	              }
	            }
	          } else {
	            if (iDatePart >= 0 && iDatePart <= 23) {
	              out.hour = iDatePart;
	            } else {
	              return null;
	            }
	          }

	          vTimeFlag = true;
	          break;

	        case 'G':
	        case 'H':
	          if (iDatePart >= 0 && iDatePart <= 23) {
	            out.hour = iDatePart;
	          } else {
	            return null;
	          }

	          vTimeFlag = true;
	          break;

	        case 'i':
	          if (iDatePart >= 0 && iDatePart <= 59) {
	            out.min = iDatePart;
	          } else {
	            return null;
	          }

	          vTimeFlag = true;
	          break;

	        case 's':
	          if (iDatePart >= 0 && iDatePart <= 59) {
	            out.sec = iDatePart;
	          } else {
	            return null;
	          }

	          vTimeFlag = true;
	          break;
	      }
	    }

	    if (vDateFlag === true) {
	      var varY = out.year || 0,
	          varM = out.month ? out.month - 1 : 0,
	          varD = out.day || 1;
	      out.date = new Date(varY, varM, varD, out.hour, out.min, out.sec, 0);
	    } else {
	      if (vTimeFlag !== true) {
	        return null;
	      }

	      out.date = new Date(0, 0, 0, out.hour, out.min, out.sec, 0);
	    }

	    return out.date;
	  },
	  guessDate: function guessDate(vDateStr, vFormat) {
	    if (typeof vDateStr !== 'string') {
	      return vDateStr;
	    }

	    var self = this,
	        vParts = vDateStr.replace(self.separators, '\0').split('\0'),
	        vPattern = /^[djmn]/g,
	        len,
	        vFormatParts = vFormat.match(self.validParts),
	        vDate = new Date(),
	        vDigit = 0,
	        vYear,
	        i,
	        n,
	        iPart,
	        iSec;

	    if (!vPattern.test(vFormatParts[0])) {
	      return vDateStr;
	    }

	    for (i = 0; i < vParts.length; i++) {
	      vDigit = 2;
	      iPart = vParts[i];
	      iSec = $h.getInt(iPart.substr(0, 2));

	      if (isNaN(iSec)) {
	        return null;
	      }

	      switch (i) {
	        case 0:
	          if (vFormatParts[0] === 'm' || vFormatParts[0] === 'n') {
	            vDate.setMonth(iSec - 1);
	          } else {
	            vDate.setDate(iSec);
	          }

	          break;

	        case 1:
	          if (vFormatParts[0] === 'm' || vFormatParts[0] === 'n') {
	            vDate.setDate(iSec);
	          } else {
	            vDate.setMonth(iSec - 1);
	          }

	          break;

	        case 2:
	          vYear = vDate.getFullYear();
	          len = iPart.length;
	          vDigit = len < 4 ? len : 4;
	          vYear = $h.getInt(len < 4 ? vYear.toString().substr(0, 4 - len) + iPart : iPart.substr(0, 4));

	          if (!vYear) {
	            return null;
	          }

	          vDate.setFullYear(vYear);
	          break;

	        case 3:
	          vDate.setHours(iSec);
	          break;

	        case 4:
	          vDate.setMinutes(iSec);
	          break;

	        case 5:
	          vDate.setSeconds(iSec);
	          break;
	      }

	      n = iPart.substr(vDigit);

	      if (n.length > 0) {
	        vParts.splice(i + 1, 0, n);
	      }
	    }

	    return vDate;
	  },
	  parseFormat: function parseFormat(vChar, vDate) {
	    var self = this,
	        vSettings = self.dateSettings,
	        fmt,
	        backslash = /\\?(.?)/gi,
	        doFormat = function doFormat(t, s) {
	      return fmt[t] ? fmt[t]() : s;
	    };

	    fmt = {
	      /////////
	      // DAY //
	      /////////
	      d: function d() {
	        return $h.lpad(fmt.j(), 2);
	      },
	      D: function D() {
	        return vSettings.daysShort[fmt.w()];
	      },
	      j: function j() {
	        return vDate.getDate();
	      },
	      l: function l() {
	        return vSettings.days[fmt.w()];
	      },
	      N: function N() {
	        return fmt.w() || 7;
	      },
	      w: function w() {
	        return vDate.getDay();
	      },
	      z: function z() {
	        var a = new Date(fmt.Y(), fmt.n() - 1, fmt.j()),
	            b = new Date(fmt.Y(), 0, 1);
	        return Math.round((a - b) / $h.DAY);
	      },
	      //////////
	      // WEEK //
	      //////////
	      W: function W() {
	        var a = new Date(fmt.Y(), fmt.n() - 1, fmt.j() - fmt.N() + 3),
	            b = new Date(a.getFullYear(), 0, 4);
	        return $h.lpad(1 + Math.round((a - b) / $h.DAY / 7), 2);
	      },
	      ///////////
	      // MONTH //
	      ///////////
	      F: function F() {
	        return vSettings.months[vDate.getMonth()];
	      },

	      /**
	       * Month w/leading 0: `01..12`
	       * @return {string}
	       */
	      m: function m() {
	        return $h.lpad(fmt.n(), 2);
	      },

	      /**
	       * Shorthand month name; `Jan...Dec`
	       * @return {string}
	       */
	      M: function M() {
	        return vSettings.monthsShort[vDate.getMonth()];
	      },

	      /**
	       * Month: `1...12`
	       * @return {number}
	       */
	      n: function n() {
	        return vDate.getMonth() + 1;
	      },

	      /**
	       * Days in month: `28...31`
	       * @return {number}
	       */
	      t: function t() {
	        return new Date(fmt.Y(), fmt.n(), 0).getDate();
	      },
	      //////////
	      // YEAR //
	      //////////

	      /**
	       * Is leap year? `0 or 1`
	       * @return {number}
	       */
	      L: function L() {
	        var Y = fmt.Y();
	        return Y % 4 === 0 && Y % 100 !== 0 || Y % 400 === 0 ? 1 : 0;
	      },

	      /**
	       * ISO-8601 year
	       * @return {number}
	       */
	      o: function o() {
	        var n = fmt.n(),
	            W = fmt.W(),
	            Y = fmt.Y();
	        return Y + (n === 12 && W < 9 ? 1 : n === 1 && W > 9 ? -1 : 0);
	      },

	      /**
	       * Full year: `e.g. 1980...2010`
	       * @return {number}
	       */
	      Y: function Y() {
	        return vDate.getFullYear();
	      },

	      /**
	       * Last two digits of year: `00...99`
	       * @return {string}
	       */
	      y: function y() {
	        return fmt.Y().toString().slice(-2);
	      },
	      //////////
	      // TIME //
	      //////////

	      /**
	       * Meridian lower: `am or pm`
	       * @return {string}
	       */
	      a: function a() {
	        return fmt.A().toLowerCase();
	      },

	      /**
	       * Meridian upper: `AM or PM`
	       * @return {string}
	       */
	      A: function A() {
	        var n = fmt.G() < 12 ? 0 : 1;
	        return vSettings.meridiem[n];
	      },

	      /**
	       * Swatch Internet time: `000..999`
	       * @return {string}
	       */
	      B: function B() {
	        var H = vDate.getUTCHours() * $h.HOUR,
	            i = vDate.getUTCMinutes() * 60,
	            s = vDate.getUTCSeconds();
	        return $h.lpad(Math.floor((H + i + s + $h.HOUR) / 86.4) % 1000, 3);
	      },

	      /**
	       * 12-Hours: `1..12`
	       * @return {number}
	       */
	      g: function g() {
	        return fmt.G() % 12 || 12;
	      },

	      /**
	       * 24-Hours: `0..23`
	       * @return {number}
	       */
	      G: function G() {
	        return vDate.getHours();
	      },

	      /**
	       * 12-Hours with leading 0: `01..12`
	       * @return {string}
	       */
	      h: function h() {
	        return $h.lpad(fmt.g(), 2);
	      },

	      /**
	       * 24-Hours w/leading 0: `00..23`
	       * @return {string}
	       */
	      H: function H() {
	        return $h.lpad(fmt.G(), 2);
	      },

	      /**
	       * Minutes w/leading 0: `00..59`
	       * @return {string}
	       */
	      i: function i() {
	        return $h.lpad(vDate.getMinutes(), 2);
	      },

	      /**
	       * Seconds w/leading 0: `00..59`
	       * @return {string}
	       */
	      s: function s() {
	        return $h.lpad(vDate.getSeconds(), 2);
	      },

	      /**
	       * Microseconds: `000000-999000`
	       * @return {string}
	       */
	      u: function u() {
	        return $h.lpad(vDate.getMilliseconds() * 1000, 6);
	      },
	      //////////////
	      // TIMEZONE //
	      //////////////

	      /**
	       * Timezone identifier: `e.g. Atlantic/Azores, ...`
	       * @return {string}
	       */
	      e: function e() {
	        var str = /\((.*)\)/.exec(String(vDate))[1];
	        return str || 'Coordinated Universal Time';
	      },

	      /**
	       * DST observed? `0 or 1`
	       * @return {number}
	       */
	      I: function I() {
	        var a = new Date(fmt.Y(), 0),
	            c = Date.UTC(fmt.Y(), 0),
	            b = new Date(fmt.Y(), 6),
	            d = Date.UTC(fmt.Y(), 6);
	        return a - c !== b - d ? 1 : 0;
	      },

	      /**
	       * Difference to GMT in hour format: `e.g. +0200`
	       * @return {string}
	       */
	      O: function O() {
	        var tzo = vDate.getTimezoneOffset(),
	            a = Math.abs(tzo);
	        return (tzo > 0 ? '-' : '+') + $h.lpad(Math.floor(a / 60) * 100 + a % 60, 4);
	      },

	      /**
	       * Difference to GMT with colon: `e.g. +02:00`
	       * @return {string}
	       */
	      P: function P() {
	        var O = fmt.O();
	        return O.substr(0, 3) + ':' + O.substr(3, 2);
	      },

	      /**
	       * Timezone abbreviation: `e.g. EST, MDT, ...`
	       * @return {string}
	       */
	      T: function T() {
	        var str = (String(vDate).match(self.tzParts) || ['']).pop().replace(self.tzClip, '');
	        return str || 'UTC';
	      },

	      /**
	       * Timezone offset in seconds: `-43200...50400`
	       * @return {number}
	       */
	      Z: function Z() {
	        return -vDate.getTimezoneOffset() * 60;
	      },
	      ////////////////////
	      // FULL DATE TIME //
	      ////////////////////

	      /**
	       * ISO-8601 date
	       * @return {string}
	       */
	      c: function c() {
	        return 'Y-m-d\\TH:i:sP'.replace(backslash, doFormat);
	      },

	      /**
	       * RFC 2822 date
	       * @return {string}
	       */
	      r: function r() {
	        return 'D, d M Y H:i:s O'.replace(backslash, doFormat);
	      },

	      /**
	       * Seconds since UNIX epoch
	       * @return {number}
	       */
	      U: function U() {
	        return vDate.getTime() / 1000 || 0;
	      }
	    };
	    return doFormat(vChar, vChar);
	  },
	  formatDate: function formatDate(vDate, vFormat) {
	    var self = this,
	        i,
	        n,
	        len,
	        str,
	        vChar,
	        vDateStr = '',
	        BACKSLASH = '\\';

	    if (typeof vDate === 'string') {
	      vDate = self.parseDate(vDate, vFormat);

	      if (!vDate) {
	        return null;
	      }
	    }

	    if (vDate instanceof Date) {
	      len = vFormat.length;

	      for (i = 0; i < len; i++) {
	        vChar = vFormat.charAt(i);

	        if (vChar === 'S' || vChar === BACKSLASH) {
	          continue;
	        }

	        if (i > 0 && vFormat.charAt(i - 1) === BACKSLASH) {
	          vDateStr += vChar;
	          continue;
	        }

	        str = self.parseFormat(vChar, vDate);

	        if (i !== len - 1 && self.intParts.test(vChar) && vFormat.charAt(i + 1) === 'S') {
	          n = $h.getInt(str) || 0;
	          str += self.dateSettings.ordinal(n);
	        }

	        vDateStr += str;
	      }

	      return vDateStr;
	    }

	    return '';
	  }
	};
	var DateFormatter$1 = DateFormatter;

	var appValidation = {
	  implicitRules: ['Required', 'Confirmed'],

	  /**
	   * Initialize app validations.
	   *
	   * @return {void}
	   */
	  init: function init() {
	    if ($.validator) {
	      // Disable class rules and attribute rules
	      $.validator.classRuleSettings = {};

	      $.validator.attributeRules = function () {
	        this.rules = {};
	      };

	      $.validator.dataRules = this.arrayRules;
	      $.validator.prototype.arrayRulesCache = {}; // Register validations methods

	      this.setupValidations();
	    }
	  },
	  arrayRules: function arrayRules(element) {
	    var rules = {},
	        validator = $.data(element.form, 'validator'),
	        cache = validator.arrayRulesCache; // Is not an Array

	    if (element.name.indexOf('[') === -1) {
	      return rules;
	    }

	    if (!(element.name in cache)) {
	      cache[element.name] = {};
	    }

	    $.each(validator.settings.rules, function (name, tmpRules) {
	      if (name in cache[element.name]) {
	        $.extend(rules, cache[element.name][name]);
	      } else {
	        cache[element.name][name] = {};
	        var nameRegExp = appValidation.helpers.regexFromWildcard(name);

	        if (element.name.match(nameRegExp)) {
	          var newRules = $.validator.normalizeRule(tmpRules) || {};
	          cache[element.name][name] = newRules;
	          $.extend(rules, newRules);
	        }
	      }
	    });
	    return rules;
	  },
	  setupValidations: function setupValidations() {
	    /**
	     * Create JQueryValidation check to validate Laravel rules.
	     */
	    $.validator.addMethod('appValidation', function (value, element, params) {
	      var validator = this;
	      var validated = true;
	      var previous = this.previousValue(element); // put Implicit rules in front

	      var rules = [];
	      $.each(params, function (i, param) {
	        if (param[3] || appValidation.implicitRules.indexOf(param[0]) !== -1) {
	          rules.unshift(param);
	        } else {
	          rules.push(param);
	        }
	      });
	      $.each(rules, function (i, param) {
	        var implicit = param[3] || appValidation.implicitRules.indexOf(param[0]) !== -1;
	        var rule = param[0];
	        var message = param[2];

	        if (!implicit && validator.optional(element)) {
	          validated = 'dependency-mismatch';
	          return false;
	        }

	        if (appValidation.methods[rule] !== undefined) {
	          validated = appValidation.methods[rule].call(validator, value, element, param[1], function (valid) {
	            validator.settings.messages[element.name].appValidationRemote = previous.originalMessage;

	            if (valid) {
	              var submitted = validator.formSubmitted;
	              validator.prepareElement(element);
	              validator.formSubmitted = submitted;
	              validator.successList.push(element);
	              delete validator.invalid[element.name];
	              validator.showErrors();
	            } else {
	              var errors = {};
	              errors[element.name] = previous.message = $.isFunction(message) ? message(value) : message;
	              validator.invalid[element.name] = true;
	              validator.showErrors(errors);
	            }

	            validator.showErrors(validator.errorMap);
	            previous.valid = valid;
	          });
	        } else {
	          validated = false;
	        }

	        if (validated !== true) {
	          if (!validator.settings.messages[element.name]) {
	            validator.settings.messages[element.name] = {};
	          }

	          validator.settings.messages[element.name].appValidation = message;
	          return false;
	        }
	      });
	      return validated;
	    }, '');
	    /**
	     * Create JQueryValidation check to validate Remote Laravel rules.
	     */

	    $.validator.addMethod('appValidationRemote', function (value, element, params) {
	      var implicit = false,
	          check = params[0][1],
	          attribute = element.name,
	          token = check[1],
	          validateAll = check[2];
	      $.each(params, function (i, parameters) {
	        implicit = implicit || parameters[3];
	      });

	      if (!implicit && this.optional(element)) {
	        return 'dependency-mismatch';
	      }

	      var previous = this.previousValue(element),
	          validator,
	          data;

	      if (!this.settings.messages[element.name]) {
	        this.settings.messages[element.name] = {};
	      }

	      previous.originalMessage = this.settings.messages[element.name].appValidationRemote;
	      this.settings.messages[element.name].appValidationRemote = previous.message;
	      var param = typeof params === 'string' && {
	        url: params
	      } || params;

	      if (appValidation.helpers.arrayEquals(previous.old, value) || previous.old === value) {
	        return previous.valid;
	      }

	      previous.old = value;
	      validator = this;
	      this.startRequest(element);
	      data = $(validator.currentForm).serializeArray();
	      data.push({
	        name: '_jsvalidation',
	        value: attribute
	      });
	      data.push({
	        name: '_jsvalidation_validate_all',
	        value: validateAll
	      });
	      var formMethod = $(validator.currentForm).attr('method');

	      if ($(validator.currentForm).find('input[name="_method"]').length) {
	        formMethod = $(validator.currentForm).find('input[name="_method"]').val();
	      }

	      $.ajax($.extend(true, {
	        mode: 'abort',
	        port: 'validate' + element.name,
	        dataType: 'json',
	        data: data,
	        context: validator.currentForm,
	        url: $(validator.currentForm).attr('remote-validation-url'),
	        type: formMethod,
	        beforeSend: function beforeSend(xhr) {
	          if ($(validator.currentForm).attr('method').toLowerCase() !== 'get' && token) {
	            return xhr.setRequestHeader('X-XSRF-TOKEN', token);
	          }
	        }
	      }, param)).always(function (response, textStatus) {
	        var errors, message, submitted, valid;

	        if (textStatus === 'error') {
	          valid = false;
	          response = appValidation.helpers.parseErrorResponse(response);
	        } else if (textStatus === 'success') {
	          valid = response === true || response === 'true';
	        } else {
	          return;
	        }

	        validator.settings.messages[element.name].appValidationRemote = previous.originalMessage;

	        if (valid) {
	          submitted = validator.formSubmitted;
	          validator.prepareElement(element);
	          validator.formSubmitted = submitted;
	          validator.successList.push(element);
	          delete validator.invalid[element.name];
	          validator.showErrors();
	        } else {
	          errors = {};
	          message = response || validator.defaultMessage(element, 'remote');
	          errors[element.name] = previous.message = $.isFunction(message) ? message(value) : message[0];
	          validator.invalid[element.name] = true;
	          validator.showErrors(errors);
	        }

	        validator.showErrors(validator.errorMap);
	        previous.valid = valid;
	        validator.stopRequest(element, valid);
	      });
	      return 'pending';
	    }, '');
	  }
	};
	/*!
	 * CApp Javascript Validation
	 * Reference https://github.com/proengsoft/laravel-jsvalidation
	 * Helper functions used by validators
	 *
	 */

	/*!
	 * references from https://github.com/proengsoft/laravel-jsvalidation
	 * Timezone Helper functions used by validators
	 *
	 */

	var initValidation = function initValidation() {
	  $.extend(true, appValidation, {
	    helpers: {
	      /**
	       * Numeric rules
	       */
	      numericRules: ['Integer', 'Numeric'],
	      fileinfo: function fileinfo(fieldObj, index) {
	        var FileName = fieldObj.value;
	        index = typeof index !== 'undefined' ? index : 0;

	        if (fieldObj.files !== null) {
	          if (typeof fieldObj.files[index] !== 'undefined') {
	            return {
	              file: FileName,
	              extension: FileName.substr(FileName.lastIndexOf('.') + 1),
	              size: fieldObj.files[index].size / 1024,
	              type: fieldObj.files[index].type
	            };
	          }
	        }

	        return false;
	      },
	      selector: function selector(names) {
	        var selector = [];

	        if (!$.isArray(names)) {
	          names = [names];
	        }

	        for (var i = 0; i < names.length; i++) {
	          selector.push('[name=\'' + names[i] + '\']');
	        }

	        return selector.join();
	      },
	      hasNumericRules: function hasNumericRules(element) {
	        return this.hasRules(element, this.numericRules);
	      },
	      hasRules: function hasRules(element, rules) {
	        var found = false;

	        if (typeof rules === 'string') {
	          rules = [rules];
	        }

	        var validator = $.data(element.form, 'validator');
	        var listRules = [];
	        var cache = validator.arrayRulesCache;

	        if (element.name in cache) {
	          $.each(cache[element.name], function (index, arrayRule) {
	            listRules.push(arrayRule);
	          });
	        }

	        if (element.name in validator.settings.rules) {
	          listRules.push(validator.settings.rules[element.name]);
	        }

	        $.each(listRules, function (index, objRules) {
	          if ('appValidation' in objRules) {
	            var valRules = objRules.appValidation;

	            for (var i = 0; i < valRules.length; i++) {
	              if ($.inArray(valRules[i][0], rules) !== -1) {
	                found = true;
	                return false;
	              }
	            }
	          }
	        });
	        return found;
	      },
	      strlen: function strlen$1(string) {
	        return strlen(string);
	      },
	      getSize: function getSize(obj, element, value) {
	        if (this.hasNumericRules(element) && this.is_numeric(value)) {
	          return parseFloat(value);
	        } else if ($.isArray(value)) {
	          return parseFloat(value.length);
	        } else if (element.type === 'file') {
	          return parseFloat(Math.floor(this.fileinfo(element).size));
	        }

	        return parseFloat(this.strlen(value));
	      },
	      getAppValidation: function getAppValidation(rule, element) {
	        var found;
	        $.each($.validator.staticRules(element), function (key, rules) {
	          if (key === 'appValidation') {
	            $.each(rules, function (i, value) {
	              if (value[0] === rule) {
	                found = value;
	              }
	            });
	          }
	        });
	        return found;
	      },
	      parseTime: function parseTime(value, format) {
	        var timeValue = false;
	        var fmt = new DateFormatter$1();

	        if ($.type(format) === 'object') {
	          var dateRule = this.getAppValidation('DateFormat', format);

	          if (dateRule !== undefined) {
	            format = dateRule[1][0];
	          } else {
	            format = null;
	          }
	        }

	        if (format == null) {
	          timeValue = this.strtotime(value);
	        } else {
	          timeValue = fmt.parseDate(value, format);

	          if (timeValue) {
	            timeValue = Math.round(timeValue.getTime() / 1000);
	          }
	        }

	        return timeValue;
	      },
	      guessDate: function guessDate(value, format) {
	        var fmt = new DateFormatter$1();
	        return fmt.guessDate(value, format);
	      },
	      strtotime: function strtotime$1(text, now) {
	        return strtotime(text, now);
	      },
	      is_numeric: function is_numeric$1(mixed_var) {
	        return is_numeric(mixed_var);
	      },
	      arrayDiff: function arrayDiff(arr1, arr2) {
	        return array_diff(arr1, arr2);
	      },
	      arrayEquals: function arrayEquals(arr1, arr2) {
	        if (!$.isArray(arr1) || !$.isArray(arr2)) {
	          return false;
	        }

	        if (arr1.length !== arr2.length) {
	          return false;
	        }

	        return $.isEmptyObject(this.arrayDiff(arr1, arr2));
	      },
	      dependentElement: function dependentElement(validator, element, name) {
	        var el = validator.findByName(name);

	        if (el[0] !== undefined && validator.settings.onfocusout) {
	          var event = 'blur';

	          if (el[0].tagName === 'SELECT' || el[0].tagName === 'OPTION' || el[0].type === 'checkbox' || el[0].type === 'radio') {
	            event = 'click';
	          }

	          var ruleName = '.validate-appValidation';
	          el.off(ruleName).off(event + ruleName + '-' + element.name).on(event + ruleName + '-' + element.name, function () {
	            $(element).valid();
	          });
	        }

	        return el[0];
	      },
	      parseErrorResponse: function parseErrorResponse(response) {
	        var newResponse = ['Whoops, looks like something went wrong.'];

	        if ('responseText' in response) {
	          var errorMsg = response.responseText.match(/<h1\s*>(.*)<\/h1\s*>/i);

	          if ($.isArray(errorMsg)) {
	            newResponse = [errorMsg[1]];
	          }
	        }

	        return newResponse;
	      },
	      escapeRegExp: function escapeRegExp(str) {
	        return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, '\\$&');
	      },
	      regexFromWildcard: function regexFromWildcard(name) {
	        var nameParts = name.split('[*]');

	        if (nameParts.length === 1) {
	          nameParts.push('');
	        }

	        var regexpParts = nameParts.map(function (currentValue, index) {
	          if (index % 2 === 0) {
	            currentValue = currentValue + '[';
	          } else {
	            currentValue = ']' + currentValue;
	          }

	          return appValidation.helpers.escapeRegExp(currentValue);
	        });
	        return new RegExp('^' + regexpParts.join('.*') + '$');
	      }
	    }
	  });
	  $.extend(true, appValidation, {
	    helpers: {
	      isTimezone: function isTimezone(value) {
	        var timezones = {
	          africa: ['abidjan', 'accra', 'addis_ababa', 'algiers', 'asmara', 'bamako', 'bangui', 'banjul', 'bissau', 'blantyre', 'brazzaville', 'bujumbura', 'cairo', 'casablanca', 'ceuta', 'conakry', 'dakar', 'dar_es_salaam', 'djibouti', 'douala', 'el_aaiun', 'freetown', 'gaborone', 'harare', 'johannesburg', 'juba', 'kampala', 'khartoum', 'kigali', 'kinshasa', 'lagos', 'libreville', 'lome', 'luanda', 'lubumbashi', 'lusaka', 'malabo', 'maputo', 'maseru', 'mbabane', 'mogadishu', 'monrovia', 'nairobi', 'ndjamena', 'niamey', 'nouakchott', 'ouagadougou', 'porto-novo', 'sao_tome', 'tripoli', 'tunis', 'windhoek'],
	          america: ['adak', 'anchorage', 'anguilla', 'antigua', 'araguaina', 'argentina\/buenos_aires', 'argentina\/catamarca', 'argentina\/cordoba', 'argentina\/jujuy', 'argentina\/la_rioja', 'argentina\/mendoza', 'argentina\/rio_gallegos', 'argentina\/salta', 'argentina\/san_juan', 'argentina\/san_luis', 'argentina\/tucuman', 'argentina\/ushuaia', 'aruba', 'asuncion', 'atikokan', 'bahia', 'bahia_banderas', 'barbados', 'belem', 'belize', 'blanc-sablon', 'boa_vista', 'bogota', 'boise', 'cambridge_bay', 'campo_grande', 'cancun', 'caracas', 'cayenne', 'cayman', 'chicago', 'chihuahua', 'costa_rica', 'creston', 'cuiaba', 'curacao', 'danmarkshavn', 'dawson', 'dawson_creek', 'denver', 'detroit', 'dominica', 'edmonton', 'eirunepe', 'el_salvador', 'fortaleza', 'glace_bay', 'godthab', 'goose_bay', 'grand_turk', 'grenada', 'guadeloupe', 'guatemala', 'guayaquil', 'guyana', 'halifax', 'havana', 'hermosillo', 'indiana\/indianapolis', 'indiana\/knox', 'indiana\/marengo', 'indiana\/petersburg', 'indiana\/tell_city', 'indiana\/vevay', 'indiana\/vincennes', 'indiana\/winamac', 'inuvik', 'iqaluit', 'jamaica', 'juneau', 'kentucky\/louisville', 'kentucky\/monticello', 'kralendijk', 'la_paz', 'lima', 'los_angeles', 'lower_princes', 'maceio', 'managua', 'manaus', 'marigot', 'martinique', 'matamoros', 'mazatlan', 'menominee', 'merida', 'metlakatla', 'mexico_city', 'miquelon', 'moncton', 'monterrey', 'montevideo', 'montreal', 'montserrat', 'nassau', 'new_york', 'nipigon', 'nome', 'noronha', 'north_dakota\/beulah', 'north_dakota\/center', 'north_dakota\/new_salem', 'ojinaga', 'panama', 'pangnirtung', 'paramaribo', 'phoenix', 'port-au-prince', 'port_of_spain', 'porto_velho', 'puerto_rico', 'rainy_river', 'rankin_inlet', 'recife', 'regina', 'resolute', 'rio_branco', 'santa_isabel', 'santarem', 'santiago', 'santo_domingo', 'sao_paulo', 'scoresbysund', 'shiprock', 'sitka', 'st_barthelemy', 'st_johns', 'st_kitts', 'st_lucia', 'st_thomas', 'st_vincent', 'swift_current', 'tegucigalpa', 'thule', 'thunder_bay', 'tijuana', 'toronto', 'tortola', 'vancouver', 'whitehorse', 'winnipeg', 'yakutat', 'yellowknife'],
	          antarctica: ['casey', 'davis', 'dumontdurville', 'macquarie', 'mawson', 'mcmurdo', 'palmer', 'rothera', 'south_pole', 'syowa', 'vostok'],
	          arctic: ['longyearbyen'],
	          asia: ['aden', 'almaty', 'amman', 'anadyr', 'aqtau', 'aqtobe', 'ashgabat', 'baghdad', 'bahrain', 'baku', 'bangkok', 'beirut', 'bishkek', 'brunei', 'choibalsan', 'chongqing', 'colombo', 'damascus', 'dhaka', 'dili', 'dubai', 'dushanbe', 'gaza', 'harbin', 'hebron', 'ho_chi_minh', 'hong_kong', 'hovd', 'irkutsk', 'jakarta', 'jayapura', 'jerusalem', 'kabul', 'kamchatka', 'karachi', 'kashgar', 'kathmandu', 'khandyga', 'kolkata', 'krasnoyarsk', 'kuala_lumpur', 'kuching', 'kuwait', 'macau', 'magadan', 'makassar', 'manila', 'muscat', 'nicosia', 'novokuznetsk', 'novosibirsk', 'omsk', 'oral', 'phnom_penh', 'pontianak', 'pyongyang', 'qatar', 'qyzylorda', 'rangoon', 'riyadh', 'sakhalin', 'samarkand', 'seoul', 'shanghai', 'singapore', 'taipei', 'tashkent', 'tbilisi', 'tehran', 'thimphu', 'tokyo', 'ulaanbaatar', 'urumqi', 'ust-nera', 'vientiane', 'vladivostok', 'yakutsk', 'yekaterinburg', 'yerevan'],
	          atlantic: ['azores', 'bermuda', 'canary', 'cape_verde', 'faroe', 'madeira', 'reykjavik', 'south_georgia', 'st_helena', 'stanley'],
	          australia: ['adelaide', 'brisbane', 'broken_hill', 'currie', 'darwin', 'eucla', 'hobart', 'lindeman', 'lord_howe', 'melbourne', 'perth', 'sydney'],
	          europe: ['amsterdam', 'andorra', 'athens', 'belgrade', 'berlin', 'bratislava', 'brussels', 'bucharest', 'budapest', 'busingen', 'chisinau', 'copenhagen', 'dublin', 'gibraltar', 'guernsey', 'helsinki', 'isle_of_man', 'istanbul', 'jersey', 'kaliningrad', 'kiev', 'lisbon', 'ljubljana', 'london', 'luxembourg', 'madrid', 'malta', 'mariehamn', 'minsk', 'monaco', 'moscow', 'oslo', 'paris', 'podgorica', 'prague', 'riga', 'rome', 'samara', 'san_marino', 'sarajevo', 'simferopol', 'skopje', 'sofia', 'stockholm', 'tallinn', 'tirane', 'uzhgorod', 'vaduz', 'vatican', 'vienna', 'vilnius', 'volgograd', 'warsaw', 'zagreb', 'zaporozhye', 'zurich'],
	          indian: ['antananarivo', 'chagos', 'christmas', 'cocos', 'comoro', 'kerguelen', 'mahe', 'maldives', 'mauritius', 'mayotte', 'reunion'],
	          pacific: ['apia', 'auckland', 'chatham', 'chuuk', 'easter', 'efate', 'enderbury', 'fakaofo', 'fiji', 'funafuti', 'galapagos', 'gambier', 'guadalcanal', 'guam', 'honolulu', 'johnston', 'kiritimati', 'kosrae', 'kwajalein', 'majuro', 'marquesas', 'midway', 'nauru', 'niue', 'norfolk', 'noumea', 'pago_pago', 'palau', 'pitcairn', 'pohnpei', 'port_moresby', 'rarotonga', 'saipan', 'tahiti', 'tarawa', 'tongatapu', 'wake', 'wallis'],
	          utc: ['']
	        };
	        var tzparts = value.split('/', 2);
	        var continent = tzparts[0].toLowerCase();
	        var city = '';

	        if (tzparts[1]) {
	          city = tzparts[1].toLowerCase();
	        }

	        return continent in timezones && (timezones[continent].length === 0 || timezones[continent].indexOf(city) !== -1);
	      }
	    }
	  });
	  /*!
	   * Methods that implement CApp Validations
	   */

	  $.extend(true, appValidation, {
	    methods: {
	      helpers: appValidation.helpers,
	      jsRemoteTimer: 0,
	      Sometimes: function Sometimes() {
	        return true;
	      },
	      Bail: function Bail() {
	        return true;
	      },
	      Nullable: function Nullable() {
	        return true;
	      },
	      Filled: function Filled(value, element) {
	        return $.validator.methods.required.call(this, value, element, true);
	      },
	      Required: function Required(value, element) {
	        return $.validator.methods.required.call(this, value, element);
	      },
	      RequiredWith: function RequiredWith(value, element, params) {
	        var validator = this,
	            required = false;
	        var currentObject = this;
	        $.each(params, function (i, param) {
	          var target = appValidation.helpers.dependentElement(currentObject, element, param);
	          required = required || target !== undefined && $.validator.methods.required.call(validator, currentObject.elementValue(target), target, true);
	        });

	        if (required) {
	          return $.validator.methods.required.call(this, value, element, true);
	        }

	        return true;
	      },
	      RequiredWithAll: function RequiredWithAll(value, element, params) {
	        var validator = this,
	            required = true;
	        var currentObject = this;
	        $.each(params, function (i, param) {
	          var target = appValidation.helpers.dependentElement(currentObject, element, param);
	          required = required && target !== undefined && $.validator.methods.required.call(validator, currentObject.elementValue(target), target, true);
	        });

	        if (required) {
	          return $.validator.methods.required.call(this, value, element, true);
	        }

	        return true;
	      },
	      RequiredWithout: function RequiredWithout(value, element, params) {
	        var validator = this,
	            required = false;
	        var currentObject = this;
	        $.each(params, function (i, param) {
	          var target = appValidation.helpers.dependentElement(currentObject, element, param);
	          required = required || target === undefined || !$.validator.methods.required.call(validator, currentObject.elementValue(target), target, true);
	        });

	        if (required) {
	          return $.validator.methods.required.call(this, value, element, true);
	        }

	        return true;
	      },
	      RequiredWithoutAll: function RequiredWithoutAll(value, element, params) {
	        var validator = this,
	            required = true,
	            currentObject = this;
	        $.each(params, function (i, param) {
	          var target = appValidation.helpers.dependentElement(currentObject, element, param);
	          required = required && (target === undefined || !$.validator.methods.required.call(validator, currentObject.elementValue(target), target, true));
	        });

	        if (required) {
	          return $.validator.methods.required.call(this, value, element, true);
	        }

	        return true;
	      },
	      RequiredIf: function RequiredIf(value, element, params) {
	        var target = appValidation.helpers.dependentElement(this, element, params[0]);

	        if (target !== undefined) {
	          var val = String(this.elementValue(target));

	          if (typeof val !== 'undefined') {
	            var data = params.slice(1);

	            if ($.inArray(val, data) !== -1) {
	              return $.validator.methods.required.call(this, value, element, true);
	            }
	          }
	        }

	        return true;
	      },
	      RequiredUnless: function RequiredUnless(value, element, params) {
	        var target = appValidation.helpers.dependentElement(this, element, params[0]);

	        if (target !== undefined) {
	          var val = String(this.elementValue(target));

	          if (typeof val !== 'undefined') {
	            var data = params.slice(1);

	            if ($.inArray(val, data) !== -1) {
	              return true;
	            }
	          }
	        }

	        return $.validator.methods.required.call(this, value, element, true);
	      },
	      Confirmed: function Confirmed(value, element, params) {
	        return appValidation.methods.Same.call(this, value, element, params);
	      },
	      Same: function Same(value, element, params) {
	        var target = appValidation.helpers.dependentElement(this, element, params[0]);

	        if (target !== undefined) {
	          return String(value) === String(this.elementValue(target));
	        }

	        return false;
	      },
	      InArray: function InArray(value, element, params) {
	        if (typeof params[0] === 'undefined') {
	          return false;
	        }

	        var elements = this.elements();
	        var found = false;
	        var nameRegExp = appValidation.helpers.regexFromWildcard(params[0]);

	        for (var i = 0; i < elements.length; i++) {
	          var targetName = elements[i].name;

	          if (targetName.match(nameRegExp)) {
	            var equals = appValidation.methods.Same.call(this, value, element, [targetName]);
	            found = found || equals;
	          }
	        }

	        return found;
	      },
	      Distinct: function Distinct(value, element, params) {
	        if (typeof params[0] === 'undefined') {
	          return false;
	        }

	        var elements = this.elements();
	        var found = false;
	        var nameRegExp = appValidation.helpers.regexFromWildcard(params[0]);

	        for (var i = 0; i < elements.length; i++) {
	          var targetName = elements[i].name;

	          if (targetName !== element.name && targetName.match(nameRegExp)) {
	            var equals = appValidation.methods.Same.call(this, value, element, [targetName]);
	            found = found || equals;
	          }
	        }

	        return !found;
	      },
	      Different: function Different(value, element, params) {
	        return !appValidation.methods.Same.call(this, value, element, params);
	      },
	      Accepted: function Accepted(value) {
	        var regex = new RegExp('^(?:(yes|on|1|true))$', 'i');
	        return regex.test(value);
	      },
	      Array: function Array(value, element) {
	        if (element.name.indexOf('[') !== -1 && element.name.indexOf(']') !== -1) {
	          return true;
	        }

	        return $.isArray(value);
	      },
	      Boolean: function Boolean(value) {
	        var regex = new RegExp('^(?:(true|false|1|0))$', 'i');
	        return regex.test(value);
	      },
	      Integer: function Integer(value) {
	        var regex = new RegExp('^(?:-?\\d+)$', 'i');
	        return regex.test(value);
	      },
	      Numeric: function Numeric(value, element) {
	        return $.validator.methods.number.call(this, value, element, true);
	      },
	      String: function String(value) {
	        return typeof value === 'string';
	      },
	      Digits: function Digits(value, element, params) {
	        return $.validator.methods.number.call(this, value, element, true) && value.length === parseInt(params, 10);
	      },
	      DigitsBetween: function DigitsBetween(value, element, params) {
	        return $.validator.methods.number.call(this, value, element, true) && value.length >= parseFloat(params[0]) && value.length <= parseFloat(params[1]);
	      },
	      Size: function Size(value, element, params) {
	        return appValidation.helpers.getSize(this, element, value) === parseFloat(params[0]);
	      },
	      Between: function Between(value, element, params) {
	        return appValidation.helpers.getSize(this, element, value) >= parseFloat(params[0]) && appValidation.helpers.getSize(this, element, value) <= parseFloat(params[1]);
	      },
	      Min: function Min(value, element, params) {
	        return appValidation.helpers.getSize(this, element, value) >= parseFloat(params[0]);
	      },
	      Max: function Max(value, element, params) {
	        return appValidation.helpers.getSize(this, element, value) <= parseFloat(params[0]);
	      },
	      In: function In(value, element, params) {
	        if ($.isArray(value) && appValidation.helpers.hasRules(element, 'Array')) {
	          var diff = appValidation.helpers.arrayDiff(value, params);
	          return Object.keys(diff).length === 0;
	        }

	        return params.indexOf(value.toString()) !== -1;
	      },
	      NotIn: function NotIn(value, element, params) {
	        return params.indexOf(value.toString()) === -1;
	      },
	      Ip: function Ip(value) {
	        return /^(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)$/i.test(value) || /^((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))$/i.test(value);
	      },
	      Email: function Email(value, element) {
	        return $.validator.methods.email.call(this, value, element, true);
	      },
	      Url: function Url(value, element) {
	        return $.validator.methods.url.call(this, value, element, true);
	      },
	      File: function File(value, element) {
	        if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
	          return true;
	        }

	        if ('files' in element) {
	          return element.files.length > 0;
	        }

	        return false;
	      },
	      Mimes: function Mimes(value, element, params) {
	        if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
	          return true;
	        }

	        var lowerParams = $.map(params, function (item) {
	          return item.toLowerCase();
	        });
	        var fileinfo = appValidation.helpers.fileinfo(element);
	        return fileinfo !== false && lowerParams.indexOf(fileinfo.extension.toLowerCase()) !== -1;
	      },
	      Mimetypes: function Mimetypes(value, element, params) {
	        if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
	          return true;
	        }

	        var lowerParams = $.map(params, function (item) {
	          return item.toLowerCase();
	        });
	        var fileinfo = appValidation.helpers.fileinfo(element);

	        if (fileinfo === false) {
	          return false;
	        }

	        return lowerParams.indexOf(fileinfo.type.toLowerCase()) !== -1;
	      },
	      Image: function Image(value, element) {
	        return appValidation.methods.Mimes.call(this, value, element, ['jpg', 'png', 'gif', 'bmp', 'svg', 'jpeg']);
	      },
	      Dimensions: function Dimensions(value, element, params, callback) {
	        if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
	          return true;
	        }

	        if (element.files === null || typeof element.files[0] === 'undefined') {
	          return false;
	        }

	        var fr = new FileReader();

	        fr.onload = function () {
	          var img = new Image();

	          img.onload = function () {
	            var height = parseFloat(img.naturalHeight);
	            var width = parseFloat(img.naturalWidth);
	            var ratio = width / height;
	            var notValid = params.width && parseFloat(params.width !== width) || params.min_width && parseFloat(params.min_width) > width || params.max_width && parseFloat(params.max_width) < width || params.height && parseFloat(params.height) !== height || params.min_height && parseFloat(params.min_height) > height || params.max_height && parseFloat(params.max_height) < height || params.ratio && ratio !== parseFloat(eval(params.ratio));
	            callback(!notValid);
	          };

	          img.onerror = function () {
	            callback(false);
	          };

	          img.src = fr.result;
	        };

	        fr.readAsDataURL(element.files[0]);
	        return 'pending';
	      },
	      Alpha: function Alpha(value) {
	        if (typeof value !== 'string') {
	          return false;
	        }

	        var regex = new RegExp("^(?:^[a-z\xE0-\xFC]+$)$", 'i');
	        return regex.test(value);
	      },
	      AlphaNum: function AlphaNum(value) {
	        if (typeof value !== 'string') {
	          return false;
	        }

	        var regex = new RegExp("^(?:^[a-z0-9\xE0-\xFC]+$)$", 'i');
	        return regex.test(value);
	      },
	      AlphaDash: function AlphaDash(value) {
	        if (typeof value !== 'string') {
	          return false;
	        }

	        var regex = new RegExp("^(?:^[a-z0-9\xE0-\xFC_-]+$)$", 'i');
	        return regex.test(value);
	      },
	      Regex: function Regex(value, element, params) {
	        var invalidModifiers = ['x', 's', 'u', 'X', 'U', 'A']; // Converting php regular expression

	        var phpReg = new RegExp('^(?:\/)(.*\\\/?[^\/]*|[^\/]*)(?:\/)([gmixXsuUAJ]*)?$');
	        var matches = params[0].match(phpReg);

	        if (matches === null) {
	          return false;
	        } // checking modifiers


	        var php_modifiers = [];

	        if (matches[2] !== undefined) {
	          php_modifiers = matches[2].split('');

	          for (var i = 0; i < php_modifiers.length < i; i++) {
	            if (invalidModifiers.indexOf(php_modifiers[i]) !== -1) {
	              return true;
	            }
	          }
	        }

	        var regex = new RegExp('^(?:' + matches[1] + ')$', php_modifiers.join());
	        return regex.test(value);
	      },
	      Date: function Date(value) {
	        return appValidation.helpers.strtotime(value) !== false;
	      },
	      DateFormat: function DateFormat(value, element, params) {
	        return appValidation.helpers.parseTime(value, params[0]) !== false;
	      },
	      Before: function Before(value, element, params) {
	        var timeCompare = parseFloat(params);

	        if (isNaN(timeCompare)) {
	          var target = appValidation.helpers.dependentElement(this, element, params);

	          if (target === undefined) {
	            return false;
	          }

	          timeCompare = appValidation.helpers.parseTime(this.elementValue(target), target);
	        }

	        var timeValue = appValidation.helpers.parseTime(value, element);
	        return timeValue !== false && timeValue < timeCompare;
	      },
	      After: function After(value, element, params) {
	        var timeCompare = parseFloat(params);

	        if (isNaN(timeCompare)) {
	          var target = appValidation.helpers.dependentElement(this, element, params);

	          if (target === undefined) {
	            return false;
	          }

	          timeCompare = appValidation.helpers.parseTime(this.elementValue(target), target);
	        }

	        var timeValue = appValidation.helpers.parseTime(value, element);
	        return timeValue !== false && timeValue > timeCompare;
	      },
	      Timezone: function Timezone(value) {
	        return appValidation.helpers.isTimezone(value);
	      },
	      Json: function Json(value) {
	        var result = true;

	        try {
	          JSON.parse(value);
	        } catch (e) {
	          result = false;
	        }

	        return result;
	      }
	    }
	  });
	  appValidation.init();
	};

	var global$1 = (typeof global$1 !== "undefined" ? global$1 :
	  typeof self !== "undefined" ? self :
	  typeof window !== "undefined" ? window : {});

	var __create = Object.create;
	var __defProp = Object.defineProperty;
	var __getProtoOf = Object.getPrototypeOf;
	var __hasOwnProp = Object.prototype.hasOwnProperty;
	var __getOwnPropNames = Object.getOwnPropertyNames;
	var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
	var __markAsModule = (target) => __defProp(target, "__esModule", {value: true});
	var __commonJS = (callback, module) => () => {
	  if (!module) {
	    module = {exports: {}};
	    callback(module.exports, module);
	  }
	  return module.exports;
	};
	var __exportStar = (target, module, desc) => {
	  if (module && typeof module === "object" || typeof module === "function") {
	    for (let key of __getOwnPropNames(module))
	      if (!__hasOwnProp.call(target, key) && key !== "default")
	        __defProp(target, key, {get: () => module[key], enumerable: !(desc = __getOwnPropDesc(module, key)) || desc.enumerable});
	  }
	  return target;
	};
	var __toModule = (module) => {
	  return __exportStar(__markAsModule(__defProp(module != null ? __create(__getProtoOf(module)) : {}, "default", module && module.__esModule && "default" in module ? {get: () => module.default, enumerable: true} : {value: module, enumerable: true})), module);
	};

	// node_modules/@vue/shared/dist/shared.cjs.js
	var require_shared_cjs = __commonJS((exports) => {
	  Object.defineProperty(exports, "__esModule", {value: true});
	  function makeMap(str, expectsLowerCase) {
	    const map = Object.create(null);
	    const list = str.split(",");
	    for (let i = 0; i < list.length; i++) {
	      map[list[i]] = true;
	    }
	    return expectsLowerCase ? (val) => !!map[val.toLowerCase()] : (val) => !!map[val];
	  }
	  var PatchFlagNames = {
	    [1]: `TEXT`,
	    [2]: `CLASS`,
	    [4]: `STYLE`,
	    [8]: `PROPS`,
	    [16]: `FULL_PROPS`,
	    [32]: `HYDRATE_EVENTS`,
	    [64]: `STABLE_FRAGMENT`,
	    [128]: `KEYED_FRAGMENT`,
	    [256]: `UNKEYED_FRAGMENT`,
	    [512]: `NEED_PATCH`,
	    [1024]: `DYNAMIC_SLOTS`,
	    [2048]: `DEV_ROOT_FRAGMENT`,
	    [-1]: `HOISTED`,
	    [-2]: `BAIL`
	  };
	  var slotFlagsText = {
	    [1]: "STABLE",
	    [2]: "DYNAMIC",
	    [3]: "FORWARDED"
	  };
	  var GLOBALS_WHITE_LISTED = "Infinity,undefined,NaN,isFinite,isNaN,parseFloat,parseInt,decodeURI,decodeURIComponent,encodeURI,encodeURIComponent,Math,Number,Date,Array,Object,Boolean,String,RegExp,Map,Set,JSON,Intl,BigInt";
	  var isGloballyWhitelisted = /* @__PURE__ */ makeMap(GLOBALS_WHITE_LISTED);
	  var range = 2;
	  function generateCodeFrame(source, start2 = 0, end = source.length) {
	    const lines = source.split(/\r?\n/);
	    let count = 0;
	    const res = [];
	    for (let i = 0; i < lines.length; i++) {
	      count += lines[i].length + 1;
	      if (count >= start2) {
	        for (let j = i - range; j <= i + range || end > count; j++) {
	          if (j < 0 || j >= lines.length)
	            continue;
	          const line = j + 1;
	          res.push(`${line}${" ".repeat(Math.max(3 - String(line).length, 0))}|  ${lines[j]}`);
	          const lineLength = lines[j].length;
	          if (j === i) {
	            const pad = start2 - (count - lineLength) + 1;
	            const length = Math.max(1, end > count ? lineLength - pad : end - start2);
	            res.push(`   |  ` + " ".repeat(pad) + "^".repeat(length));
	          } else if (j > i) {
	            if (end > count) {
	              const length = Math.max(Math.min(end - count, lineLength), 1);
	              res.push(`   |  ` + "^".repeat(length));
	            }
	            count += lineLength + 1;
	          }
	        }
	        break;
	      }
	    }
	    return res.join("\n");
	  }
	  var specialBooleanAttrs = `itemscope,allowfullscreen,formnovalidate,ismap,nomodule,novalidate,readonly`;
	  var isSpecialBooleanAttr = /* @__PURE__ */ makeMap(specialBooleanAttrs);
	  var isBooleanAttr2 = /* @__PURE__ */ makeMap(specialBooleanAttrs + `,async,autofocus,autoplay,controls,default,defer,disabled,hidden,loop,open,required,reversed,scoped,seamless,checked,muted,multiple,selected`);
	  var unsafeAttrCharRE = /[>/="'\u0009\u000a\u000c\u0020]/;
	  var attrValidationCache = {};
	  function isSSRSafeAttrName(name) {
	    if (attrValidationCache.hasOwnProperty(name)) {
	      return attrValidationCache[name];
	    }
	    const isUnsafe = unsafeAttrCharRE.test(name);
	    if (isUnsafe) {
	      console.error(`unsafe attribute name: ${name}`);
	    }
	    return attrValidationCache[name] = !isUnsafe;
	  }
	  var propsToAttrMap = {
	    acceptCharset: "accept-charset",
	    className: "class",
	    htmlFor: "for",
	    httpEquiv: "http-equiv"
	  };
	  var isNoUnitNumericStyleProp = /* @__PURE__ */ makeMap(`animation-iteration-count,border-image-outset,border-image-slice,border-image-width,box-flex,box-flex-group,box-ordinal-group,column-count,columns,flex,flex-grow,flex-positive,flex-shrink,flex-negative,flex-order,grid-row,grid-row-end,grid-row-span,grid-row-start,grid-column,grid-column-end,grid-column-span,grid-column-start,font-weight,line-clamp,line-height,opacity,order,orphans,tab-size,widows,z-index,zoom,fill-opacity,flood-opacity,stop-opacity,stroke-dasharray,stroke-dashoffset,stroke-miterlimit,stroke-opacity,stroke-width`);
	  var isKnownAttr = /* @__PURE__ */ makeMap(`accept,accept-charset,accesskey,action,align,allow,alt,async,autocapitalize,autocomplete,autofocus,autoplay,background,bgcolor,border,buffered,capture,challenge,charset,checked,cite,class,code,codebase,color,cols,colspan,content,contenteditable,contextmenu,controls,coords,crossorigin,csp,data,datetime,decoding,default,defer,dir,dirname,disabled,download,draggable,dropzone,enctype,enterkeyhint,for,form,formaction,formenctype,formmethod,formnovalidate,formtarget,headers,height,hidden,high,href,hreflang,http-equiv,icon,id,importance,integrity,ismap,itemprop,keytype,kind,label,lang,language,loading,list,loop,low,manifest,max,maxlength,minlength,media,min,multiple,muted,name,novalidate,open,optimum,pattern,ping,placeholder,poster,preload,radiogroup,readonly,referrerpolicy,rel,required,reversed,rows,rowspan,sandbox,scope,scoped,selected,shape,size,sizes,slot,span,spellcheck,src,srcdoc,srclang,srcset,start,step,style,summary,tabindex,target,title,translate,type,usemap,value,width,wrap`);
	  function normalizeStyle(value) {
	    if (isArray(value)) {
	      const res = {};
	      for (let i = 0; i < value.length; i++) {
	        const item = value[i];
	        const normalized = normalizeStyle(isString(item) ? parseStringStyle(item) : item);
	        if (normalized) {
	          for (const key in normalized) {
	            res[key] = normalized[key];
	          }
	        }
	      }
	      return res;
	    } else if (isObject(value)) {
	      return value;
	    }
	  }
	  var listDelimiterRE = /;(?![^(]*\))/g;
	  var propertyDelimiterRE = /:(.+)/;
	  function parseStringStyle(cssText) {
	    const ret = {};
	    cssText.split(listDelimiterRE).forEach((item) => {
	      if (item) {
	        const tmp = item.split(propertyDelimiterRE);
	        tmp.length > 1 && (ret[tmp[0].trim()] = tmp[1].trim());
	      }
	    });
	    return ret;
	  }
	  function stringifyStyle(styles) {
	    let ret = "";
	    if (!styles) {
	      return ret;
	    }
	    for (const key in styles) {
	      const value = styles[key];
	      const normalizedKey = key.startsWith(`--`) ? key : hyphenate(key);
	      if (isString(value) || typeof value === "number" && isNoUnitNumericStyleProp(normalizedKey)) {
	        ret += `${normalizedKey}:${value};`;
	      }
	    }
	    return ret;
	  }
	  function normalizeClass(value) {
	    let res = "";
	    if (isString(value)) {
	      res = value;
	    } else if (isArray(value)) {
	      for (let i = 0; i < value.length; i++) {
	        const normalized = normalizeClass(value[i]);
	        if (normalized) {
	          res += normalized + " ";
	        }
	      }
	    } else if (isObject(value)) {
	      for (const name in value) {
	        if (value[name]) {
	          res += name + " ";
	        }
	      }
	    }
	    return res.trim();
	  }
	  var HTML_TAGS = "html,body,base,head,link,meta,style,title,address,article,aside,footer,header,h1,h2,h3,h4,h5,h6,hgroup,nav,section,div,dd,dl,dt,figcaption,figure,picture,hr,img,li,main,ol,p,pre,ul,a,b,abbr,bdi,bdo,br,cite,code,data,dfn,em,i,kbd,mark,q,rp,rt,rtc,ruby,s,samp,small,span,strong,sub,sup,time,u,var,wbr,area,audio,map,track,video,embed,object,param,source,canvas,script,noscript,del,ins,caption,col,colgroup,table,thead,tbody,td,th,tr,button,datalist,fieldset,form,input,label,legend,meter,optgroup,option,output,progress,select,textarea,details,dialog,menu,summary,template,blockquote,iframe,tfoot";
	  var SVG_TAGS = "svg,animate,animateMotion,animateTransform,circle,clipPath,color-profile,defs,desc,discard,ellipse,feBlend,feColorMatrix,feComponentTransfer,feComposite,feConvolveMatrix,feDiffuseLighting,feDisplacementMap,feDistanceLight,feDropShadow,feFlood,feFuncA,feFuncB,feFuncG,feFuncR,feGaussianBlur,feImage,feMerge,feMergeNode,feMorphology,feOffset,fePointLight,feSpecularLighting,feSpotLight,feTile,feTurbulence,filter,foreignObject,g,hatch,hatchpath,image,line,linearGradient,marker,mask,mesh,meshgradient,meshpatch,meshrow,metadata,mpath,path,pattern,polygon,polyline,radialGradient,rect,set,solidcolor,stop,switch,symbol,text,textPath,title,tspan,unknown,use,view";
	  var VOID_TAGS = "area,base,br,col,embed,hr,img,input,link,meta,param,source,track,wbr";
	  var isHTMLTag = /* @__PURE__ */ makeMap(HTML_TAGS);
	  var isSVGTag = /* @__PURE__ */ makeMap(SVG_TAGS);
	  var isVoidTag = /* @__PURE__ */ makeMap(VOID_TAGS);
	  var escapeRE = /["'&<>]/;
	  function escapeHtml(string) {
	    const str = "" + string;
	    const match = escapeRE.exec(str);
	    if (!match) {
	      return str;
	    }
	    let html = "";
	    let escaped;
	    let index;
	    let lastIndex = 0;
	    for (index = match.index; index < str.length; index++) {
	      switch (str.charCodeAt(index)) {
	        case 34:
	          escaped = "&quot;";
	          break;
	        case 38:
	          escaped = "&amp;";
	          break;
	        case 39:
	          escaped = "&#39;";
	          break;
	        case 60:
	          escaped = "&lt;";
	          break;
	        case 62:
	          escaped = "&gt;";
	          break;
	        default:
	          continue;
	      }
	      if (lastIndex !== index) {
	        html += str.substring(lastIndex, index);
	      }
	      lastIndex = index + 1;
	      html += escaped;
	    }
	    return lastIndex !== index ? html + str.substring(lastIndex, index) : html;
	  }
	  var commentStripRE = /^-?>|<!--|-->|--!>|<!-$/g;
	  function escapeHtmlComment(src) {
	    return src.replace(commentStripRE, "");
	  }
	  function looseCompareArrays(a, b) {
	    if (a.length !== b.length)
	      return false;
	    let equal = true;
	    for (let i = 0; equal && i < a.length; i++) {
	      equal = looseEqual(a[i], b[i]);
	    }
	    return equal;
	  }
	  function looseEqual(a, b) {
	    if (a === b)
	      return true;
	    let aValidType = isDate(a);
	    let bValidType = isDate(b);
	    if (aValidType || bValidType) {
	      return aValidType && bValidType ? a.getTime() === b.getTime() : false;
	    }
	    aValidType = isArray(a);
	    bValidType = isArray(b);
	    if (aValidType || bValidType) {
	      return aValidType && bValidType ? looseCompareArrays(a, b) : false;
	    }
	    aValidType = isObject(a);
	    bValidType = isObject(b);
	    if (aValidType || bValidType) {
	      if (!aValidType || !bValidType) {
	        return false;
	      }
	      const aKeysCount = Object.keys(a).length;
	      const bKeysCount = Object.keys(b).length;
	      if (aKeysCount !== bKeysCount) {
	        return false;
	      }
	      for (const key in a) {
	        const aHasKey = a.hasOwnProperty(key);
	        const bHasKey = b.hasOwnProperty(key);
	        if (aHasKey && !bHasKey || !aHasKey && bHasKey || !looseEqual(a[key], b[key])) {
	          return false;
	        }
	      }
	    }
	    return String(a) === String(b);
	  }
	  function looseIndexOf(arr, val) {
	    return arr.findIndex((item) => looseEqual(item, val));
	  }
	  var toDisplayString = (val) => {
	    return val == null ? "" : isObject(val) ? JSON.stringify(val, replacer, 2) : String(val);
	  };
	  var replacer = (_key, val) => {
	    if (isMap(val)) {
	      return {
	        [`Map(${val.size})`]: [...val.entries()].reduce((entries, [key, val2]) => {
	          entries[`${key} =>`] = val2;
	          return entries;
	        }, {})
	      };
	    } else if (isSet(val)) {
	      return {
	        [`Set(${val.size})`]: [...val.values()]
	      };
	    } else if (isObject(val) && !isArray(val) && !isPlainObject(val)) {
	      return String(val);
	    }
	    return val;
	  };
	  var babelParserDefaultPlugins = [
	    "bigInt",
	    "optionalChaining",
	    "nullishCoalescingOperator"
	  ];
	  var EMPTY_OBJ = Object.freeze({});
	  var EMPTY_ARR = Object.freeze([]);
	  var NOOP = () => {
	  };
	  var NO = () => false;
	  var onRE = /^on[^a-z]/;
	  var isOn = (key) => onRE.test(key);
	  var isModelListener = (key) => key.startsWith("onUpdate:");
	  var extend = Object.assign;
	  var remove = (arr, el) => {
	    const i = arr.indexOf(el);
	    if (i > -1) {
	      arr.splice(i, 1);
	    }
	  };
	  var hasOwnProperty = Object.prototype.hasOwnProperty;
	  var hasOwn = (val, key) => hasOwnProperty.call(val, key);
	  var isArray = Array.isArray;
	  var isMap = (val) => toTypeString(val) === "[object Map]";
	  var isSet = (val) => toTypeString(val) === "[object Set]";
	  var isDate = (val) => val instanceof Date;
	  var isFunction = (val) => typeof val === "function";
	  var isString = (val) => typeof val === "string";
	  var isSymbol = (val) => typeof val === "symbol";
	  var isObject = (val) => val !== null && typeof val === "object";
	  var isPromise = (val) => {
	    return isObject(val) && isFunction(val.then) && isFunction(val.catch);
	  };
	  var objectToString = Object.prototype.toString;
	  var toTypeString = (value) => objectToString.call(value);
	  var toRawType = (value) => {
	    return toTypeString(value).slice(8, -1);
	  };
	  var isPlainObject = (val) => toTypeString(val) === "[object Object]";
	  var isIntegerKey = (key) => isString(key) && key !== "NaN" && key[0] !== "-" && "" + parseInt(key, 10) === key;
	  var isReservedProp = /* @__PURE__ */ makeMap(",key,ref,onVnodeBeforeMount,onVnodeMounted,onVnodeBeforeUpdate,onVnodeUpdated,onVnodeBeforeUnmount,onVnodeUnmounted");
	  var cacheStringFunction = (fn) => {
	    const cache = Object.create(null);
	    return (str) => {
	      const hit = cache[str];
	      return hit || (cache[str] = fn(str));
	    };
	  };
	  var camelizeRE = /-(\w)/g;
	  var camelize = cacheStringFunction((str) => {
	    return str.replace(camelizeRE, (_, c) => c ? c.toUpperCase() : "");
	  });
	  var hyphenateRE = /\B([A-Z])/g;
	  var hyphenate = cacheStringFunction((str) => str.replace(hyphenateRE, "-$1").toLowerCase());
	  var capitalize = cacheStringFunction((str) => str.charAt(0).toUpperCase() + str.slice(1));
	  var toHandlerKey = cacheStringFunction((str) => str ? `on${capitalize(str)}` : ``);
	  var hasChanged = (value, oldValue) => value !== oldValue && (value === value || oldValue === oldValue);
	  var invokeArrayFns = (fns, arg) => {
	    for (let i = 0; i < fns.length; i++) {
	      fns[i](arg);
	    }
	  };
	  var def = (obj, key, value) => {
	    Object.defineProperty(obj, key, {
	      configurable: true,
	      enumerable: false,
	      value
	    });
	  };
	  var toNumber = (val) => {
	    const n = parseFloat(val);
	    return isNaN(n) ? val : n;
	  };
	  var _globalThis;
	  var getGlobalThis = () => {
	    return _globalThis || (_globalThis = typeof globalThis !== "undefined" ? globalThis : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : typeof global$1 !== "undefined" ? global$1 : {});
	  };
	  exports.EMPTY_ARR = EMPTY_ARR;
	  exports.EMPTY_OBJ = EMPTY_OBJ;
	  exports.NO = NO;
	  exports.NOOP = NOOP;
	  exports.PatchFlagNames = PatchFlagNames;
	  exports.babelParserDefaultPlugins = babelParserDefaultPlugins;
	  exports.camelize = camelize;
	  exports.capitalize = capitalize;
	  exports.def = def;
	  exports.escapeHtml = escapeHtml;
	  exports.escapeHtmlComment = escapeHtmlComment;
	  exports.extend = extend;
	  exports.generateCodeFrame = generateCodeFrame;
	  exports.getGlobalThis = getGlobalThis;
	  exports.hasChanged = hasChanged;
	  exports.hasOwn = hasOwn;
	  exports.hyphenate = hyphenate;
	  exports.invokeArrayFns = invokeArrayFns;
	  exports.isArray = isArray;
	  exports.isBooleanAttr = isBooleanAttr2;
	  exports.isDate = isDate;
	  exports.isFunction = isFunction;
	  exports.isGloballyWhitelisted = isGloballyWhitelisted;
	  exports.isHTMLTag = isHTMLTag;
	  exports.isIntegerKey = isIntegerKey;
	  exports.isKnownAttr = isKnownAttr;
	  exports.isMap = isMap;
	  exports.isModelListener = isModelListener;
	  exports.isNoUnitNumericStyleProp = isNoUnitNumericStyleProp;
	  exports.isObject = isObject;
	  exports.isOn = isOn;
	  exports.isPlainObject = isPlainObject;
	  exports.isPromise = isPromise;
	  exports.isReservedProp = isReservedProp;
	  exports.isSSRSafeAttrName = isSSRSafeAttrName;
	  exports.isSVGTag = isSVGTag;
	  exports.isSet = isSet;
	  exports.isSpecialBooleanAttr = isSpecialBooleanAttr;
	  exports.isString = isString;
	  exports.isSymbol = isSymbol;
	  exports.isVoidTag = isVoidTag;
	  exports.looseEqual = looseEqual;
	  exports.looseIndexOf = looseIndexOf;
	  exports.makeMap = makeMap;
	  exports.normalizeClass = normalizeClass;
	  exports.normalizeStyle = normalizeStyle;
	  exports.objectToString = objectToString;
	  exports.parseStringStyle = parseStringStyle;
	  exports.propsToAttrMap = propsToAttrMap;
	  exports.remove = remove;
	  exports.slotFlagsText = slotFlagsText;
	  exports.stringifyStyle = stringifyStyle;
	  exports.toDisplayString = toDisplayString;
	  exports.toHandlerKey = toHandlerKey;
	  exports.toNumber = toNumber;
	  exports.toRawType = toRawType;
	  exports.toTypeString = toTypeString;
	});

	// node_modules/@vue/shared/index.js
	var require_shared = __commonJS((exports, module) => {
	  {
	    module.exports = require_shared_cjs();
	  }
	});

	// node_modules/@vue/reactivity/dist/reactivity.cjs.js
	var require_reactivity_cjs = __commonJS((exports) => {
	  Object.defineProperty(exports, "__esModule", {value: true});
	  var shared = require_shared();
	  var targetMap = new WeakMap();
	  var effectStack = [];
	  var activeEffect;
	  var ITERATE_KEY = Symbol("iterate");
	  var MAP_KEY_ITERATE_KEY = Symbol("Map key iterate");
	  function isEffect(fn) {
	    return fn && fn._isEffect === true;
	  }
	  function effect3(fn, options = shared.EMPTY_OBJ) {
	    if (isEffect(fn)) {
	      fn = fn.raw;
	    }
	    const effect4 = createReactiveEffect(fn, options);
	    if (!options.lazy) {
	      effect4();
	    }
	    return effect4;
	  }
	  function stop2(effect4) {
	    if (effect4.active) {
	      cleanup(effect4);
	      if (effect4.options.onStop) {
	        effect4.options.onStop();
	      }
	      effect4.active = false;
	    }
	  }
	  var uid = 0;
	  function createReactiveEffect(fn, options) {
	    const effect4 = function reactiveEffect() {
	      if (!effect4.active) {
	        return fn();
	      }
	      if (!effectStack.includes(effect4)) {
	        cleanup(effect4);
	        try {
	          enableTracking();
	          effectStack.push(effect4);
	          activeEffect = effect4;
	          return fn();
	        } finally {
	          effectStack.pop();
	          resetTracking();
	          activeEffect = effectStack[effectStack.length - 1];
	        }
	      }
	    };
	    effect4.id = uid++;
	    effect4.allowRecurse = !!options.allowRecurse;
	    effect4._isEffect = true;
	    effect4.active = true;
	    effect4.raw = fn;
	    effect4.deps = [];
	    effect4.options = options;
	    return effect4;
	  }
	  function cleanup(effect4) {
	    const {deps} = effect4;
	    if (deps.length) {
	      for (let i = 0; i < deps.length; i++) {
	        deps[i].delete(effect4);
	      }
	      deps.length = 0;
	    }
	  }
	  var shouldTrack = true;
	  var trackStack = [];
	  function pauseTracking() {
	    trackStack.push(shouldTrack);
	    shouldTrack = false;
	  }
	  function enableTracking() {
	    trackStack.push(shouldTrack);
	    shouldTrack = true;
	  }
	  function resetTracking() {
	    const last = trackStack.pop();
	    shouldTrack = last === void 0 ? true : last;
	  }
	  function track(target, type, key) {
	    if (!shouldTrack || activeEffect === void 0) {
	      return;
	    }
	    let depsMap = targetMap.get(target);
	    if (!depsMap) {
	      targetMap.set(target, depsMap = new Map());
	    }
	    let dep = depsMap.get(key);
	    if (!dep) {
	      depsMap.set(key, dep = new Set());
	    }
	    if (!dep.has(activeEffect)) {
	      dep.add(activeEffect);
	      activeEffect.deps.push(dep);
	      if (activeEffect.options.onTrack) {
	        activeEffect.options.onTrack({
	          effect: activeEffect,
	          target,
	          type,
	          key
	        });
	      }
	    }
	  }
	  function trigger(target, type, key, newValue, oldValue, oldTarget) {
	    const depsMap = targetMap.get(target);
	    if (!depsMap) {
	      return;
	    }
	    const effects = new Set();
	    const add2 = (effectsToAdd) => {
	      if (effectsToAdd) {
	        effectsToAdd.forEach((effect4) => {
	          if (effect4 !== activeEffect || effect4.allowRecurse) {
	            effects.add(effect4);
	          }
	        });
	      }
	    };
	    if (type === "clear") {
	      depsMap.forEach(add2);
	    } else if (key === "length" && shared.isArray(target)) {
	      depsMap.forEach((dep, key2) => {
	        if (key2 === "length" || key2 >= newValue) {
	          add2(dep);
	        }
	      });
	    } else {
	      if (key !== void 0) {
	        add2(depsMap.get(key));
	      }
	      switch (type) {
	        case "add":
	          if (!shared.isArray(target)) {
	            add2(depsMap.get(ITERATE_KEY));
	            if (shared.isMap(target)) {
	              add2(depsMap.get(MAP_KEY_ITERATE_KEY));
	            }
	          } else if (shared.isIntegerKey(key)) {
	            add2(depsMap.get("length"));
	          }
	          break;
	        case "delete":
	          if (!shared.isArray(target)) {
	            add2(depsMap.get(ITERATE_KEY));
	            if (shared.isMap(target)) {
	              add2(depsMap.get(MAP_KEY_ITERATE_KEY));
	            }
	          }
	          break;
	        case "set":
	          if (shared.isMap(target)) {
	            add2(depsMap.get(ITERATE_KEY));
	          }
	          break;
	      }
	    }
	    const run = (effect4) => {
	      if (effect4.options.onTrigger) {
	        effect4.options.onTrigger({
	          effect: effect4,
	          target,
	          key,
	          type,
	          newValue,
	          oldValue,
	          oldTarget
	        });
	      }
	      if (effect4.options.scheduler) {
	        effect4.options.scheduler(effect4);
	      } else {
	        effect4();
	      }
	    };
	    effects.forEach(run);
	  }
	  var isNonTrackableKeys = /* @__PURE__ */ shared.makeMap(`__proto__,__v_isRef,__isVue`);
	  var builtInSymbols = new Set(Object.getOwnPropertyNames(Symbol).map((key) => Symbol[key]).filter(shared.isSymbol));
	  var get2 = /* @__PURE__ */ createGetter();
	  var shallowGet = /* @__PURE__ */ createGetter(false, true);
	  var readonlyGet = /* @__PURE__ */ createGetter(true);
	  var shallowReadonlyGet = /* @__PURE__ */ createGetter(true, true);
	  var arrayInstrumentations = {};
	  ["includes", "indexOf", "lastIndexOf"].forEach((key) => {
	    const method = Array.prototype[key];
	    arrayInstrumentations[key] = function(...args) {
	      const arr = toRaw2(this);
	      for (let i = 0, l = this.length; i < l; i++) {
	        track(arr, "get", i + "");
	      }
	      const res = method.apply(arr, args);
	      if (res === -1 || res === false) {
	        return method.apply(arr, args.map(toRaw2));
	      } else {
	        return res;
	      }
	    };
	  });
	  ["push", "pop", "shift", "unshift", "splice"].forEach((key) => {
	    const method = Array.prototype[key];
	    arrayInstrumentations[key] = function(...args) {
	      pauseTracking();
	      const res = method.apply(this, args);
	      resetTracking();
	      return res;
	    };
	  });
	  function createGetter(isReadonly2 = false, shallow = false) {
	    return function get3(target, key, receiver) {
	      if (key === "__v_isReactive") {
	        return !isReadonly2;
	      } else if (key === "__v_isReadonly") {
	        return isReadonly2;
	      } else if (key === "__v_raw" && receiver === (isReadonly2 ? shallow ? shallowReadonlyMap : readonlyMap : shallow ? shallowReactiveMap : reactiveMap).get(target)) {
	        return target;
	      }
	      const targetIsArray = shared.isArray(target);
	      if (!isReadonly2 && targetIsArray && shared.hasOwn(arrayInstrumentations, key)) {
	        return Reflect.get(arrayInstrumentations, key, receiver);
	      }
	      const res = Reflect.get(target, key, receiver);
	      if (shared.isSymbol(key) ? builtInSymbols.has(key) : isNonTrackableKeys(key)) {
	        return res;
	      }
	      if (!isReadonly2) {
	        track(target, "get", key);
	      }
	      if (shallow) {
	        return res;
	      }
	      if (isRef(res)) {
	        const shouldUnwrap = !targetIsArray || !shared.isIntegerKey(key);
	        return shouldUnwrap ? res.value : res;
	      }
	      if (shared.isObject(res)) {
	        return isReadonly2 ? readonly(res) : reactive3(res);
	      }
	      return res;
	    };
	  }
	  var set2 = /* @__PURE__ */ createSetter();
	  var shallowSet = /* @__PURE__ */ createSetter(true);
	  function createSetter(shallow = false) {
	    return function set3(target, key, value, receiver) {
	      let oldValue = target[key];
	      if (!shallow) {
	        value = toRaw2(value);
	        oldValue = toRaw2(oldValue);
	        if (!shared.isArray(target) && isRef(oldValue) && !isRef(value)) {
	          oldValue.value = value;
	          return true;
	        }
	      }
	      const hadKey = shared.isArray(target) && shared.isIntegerKey(key) ? Number(key) < target.length : shared.hasOwn(target, key);
	      const result = Reflect.set(target, key, value, receiver);
	      if (target === toRaw2(receiver)) {
	        if (!hadKey) {
	          trigger(target, "add", key, value);
	        } else if (shared.hasChanged(value, oldValue)) {
	          trigger(target, "set", key, value, oldValue);
	        }
	      }
	      return result;
	    };
	  }
	  function deleteProperty(target, key) {
	    const hadKey = shared.hasOwn(target, key);
	    const oldValue = target[key];
	    const result = Reflect.deleteProperty(target, key);
	    if (result && hadKey) {
	      trigger(target, "delete", key, void 0, oldValue);
	    }
	    return result;
	  }
	  function has(target, key) {
	    const result = Reflect.has(target, key);
	    if (!shared.isSymbol(key) || !builtInSymbols.has(key)) {
	      track(target, "has", key);
	    }
	    return result;
	  }
	  function ownKeys(target) {
	    track(target, "iterate", shared.isArray(target) ? "length" : ITERATE_KEY);
	    return Reflect.ownKeys(target);
	  }
	  var mutableHandlers = {
	    get: get2,
	    set: set2,
	    deleteProperty,
	    has,
	    ownKeys
	  };
	  var readonlyHandlers = {
	    get: readonlyGet,
	    set(target, key) {
	      {
	        console.warn(`Set operation on key "${String(key)}" failed: target is readonly.`, target);
	      }
	      return true;
	    },
	    deleteProperty(target, key) {
	      {
	        console.warn(`Delete operation on key "${String(key)}" failed: target is readonly.`, target);
	      }
	      return true;
	    }
	  };
	  var shallowReactiveHandlers = shared.extend({}, mutableHandlers, {
	    get: shallowGet,
	    set: shallowSet
	  });
	  var shallowReadonlyHandlers = shared.extend({}, readonlyHandlers, {
	    get: shallowReadonlyGet
	  });
	  var toReactive = (value) => shared.isObject(value) ? reactive3(value) : value;
	  var toReadonly = (value) => shared.isObject(value) ? readonly(value) : value;
	  var toShallow = (value) => value;
	  var getProto = (v) => Reflect.getPrototypeOf(v);
	  function get$1(target, key, isReadonly2 = false, isShallow = false) {
	    target = target["__v_raw"];
	    const rawTarget = toRaw2(target);
	    const rawKey = toRaw2(key);
	    if (key !== rawKey) {
	      !isReadonly2 && track(rawTarget, "get", key);
	    }
	    !isReadonly2 && track(rawTarget, "get", rawKey);
	    const {has: has2} = getProto(rawTarget);
	    const wrap = isShallow ? toShallow : isReadonly2 ? toReadonly : toReactive;
	    if (has2.call(rawTarget, key)) {
	      return wrap(target.get(key));
	    } else if (has2.call(rawTarget, rawKey)) {
	      return wrap(target.get(rawKey));
	    } else if (target !== rawTarget) {
	      target.get(key);
	    }
	  }
	  function has$1(key, isReadonly2 = false) {
	    const target = this["__v_raw"];
	    const rawTarget = toRaw2(target);
	    const rawKey = toRaw2(key);
	    if (key !== rawKey) {
	      !isReadonly2 && track(rawTarget, "has", key);
	    }
	    !isReadonly2 && track(rawTarget, "has", rawKey);
	    return key === rawKey ? target.has(key) : target.has(key) || target.has(rawKey);
	  }
	  function size(target, isReadonly2 = false) {
	    target = target["__v_raw"];
	    !isReadonly2 && track(toRaw2(target), "iterate", ITERATE_KEY);
	    return Reflect.get(target, "size", target);
	  }
	  function add(value) {
	    value = toRaw2(value);
	    const target = toRaw2(this);
	    const proto = getProto(target);
	    const hadKey = proto.has.call(target, value);
	    if (!hadKey) {
	      target.add(value);
	      trigger(target, "add", value, value);
	    }
	    return this;
	  }
	  function set$1(key, value) {
	    value = toRaw2(value);
	    const target = toRaw2(this);
	    const {has: has2, get: get3} = getProto(target);
	    let hadKey = has2.call(target, key);
	    if (!hadKey) {
	      key = toRaw2(key);
	      hadKey = has2.call(target, key);
	    } else {
	      checkIdentityKeys(target, has2, key);
	    }
	    const oldValue = get3.call(target, key);
	    target.set(key, value);
	    if (!hadKey) {
	      trigger(target, "add", key, value);
	    } else if (shared.hasChanged(value, oldValue)) {
	      trigger(target, "set", key, value, oldValue);
	    }
	    return this;
	  }
	  function deleteEntry(key) {
	    const target = toRaw2(this);
	    const {has: has2, get: get3} = getProto(target);
	    let hadKey = has2.call(target, key);
	    if (!hadKey) {
	      key = toRaw2(key);
	      hadKey = has2.call(target, key);
	    } else {
	      checkIdentityKeys(target, has2, key);
	    }
	    const oldValue = get3 ? get3.call(target, key) : void 0;
	    const result = target.delete(key);
	    if (hadKey) {
	      trigger(target, "delete", key, void 0, oldValue);
	    }
	    return result;
	  }
	  function clear() {
	    const target = toRaw2(this);
	    const hadItems = target.size !== 0;
	    const oldTarget = shared.isMap(target) ? new Map(target) : new Set(target);
	    const result = target.clear();
	    if (hadItems) {
	      trigger(target, "clear", void 0, void 0, oldTarget);
	    }
	    return result;
	  }
	  function createForEach(isReadonly2, isShallow) {
	    return function forEach(callback, thisArg) {
	      const observed = this;
	      const target = observed["__v_raw"];
	      const rawTarget = toRaw2(target);
	      const wrap = isShallow ? toShallow : isReadonly2 ? toReadonly : toReactive;
	      !isReadonly2 && track(rawTarget, "iterate", ITERATE_KEY);
	      return target.forEach((value, key) => {
	        return callback.call(thisArg, wrap(value), wrap(key), observed);
	      });
	    };
	  }
	  function createIterableMethod(method, isReadonly2, isShallow) {
	    return function(...args) {
	      const target = this["__v_raw"];
	      const rawTarget = toRaw2(target);
	      const targetIsMap = shared.isMap(rawTarget);
	      const isPair = method === "entries" || method === Symbol.iterator && targetIsMap;
	      const isKeyOnly = method === "keys" && targetIsMap;
	      const innerIterator = target[method](...args);
	      const wrap = isShallow ? toShallow : isReadonly2 ? toReadonly : toReactive;
	      !isReadonly2 && track(rawTarget, "iterate", isKeyOnly ? MAP_KEY_ITERATE_KEY : ITERATE_KEY);
	      return {
	        next() {
	          const {value, done} = innerIterator.next();
	          return done ? {value, done} : {
	            value: isPair ? [wrap(value[0]), wrap(value[1])] : wrap(value),
	            done
	          };
	        },
	        [Symbol.iterator]() {
	          return this;
	        }
	      };
	    };
	  }
	  function createReadonlyMethod(type) {
	    return function(...args) {
	      {
	        const key = args[0] ? `on key "${args[0]}" ` : ``;
	        console.warn(`${shared.capitalize(type)} operation ${key}failed: target is readonly.`, toRaw2(this));
	      }
	      return type === "delete" ? false : this;
	    };
	  }
	  var mutableInstrumentations = {
	    get(key) {
	      return get$1(this, key);
	    },
	    get size() {
	      return size(this);
	    },
	    has: has$1,
	    add,
	    set: set$1,
	    delete: deleteEntry,
	    clear,
	    forEach: createForEach(false, false)
	  };
	  var shallowInstrumentations = {
	    get(key) {
	      return get$1(this, key, false, true);
	    },
	    get size() {
	      return size(this);
	    },
	    has: has$1,
	    add,
	    set: set$1,
	    delete: deleteEntry,
	    clear,
	    forEach: createForEach(false, true)
	  };
	  var readonlyInstrumentations = {
	    get(key) {
	      return get$1(this, key, true);
	    },
	    get size() {
	      return size(this, true);
	    },
	    has(key) {
	      return has$1.call(this, key, true);
	    },
	    add: createReadonlyMethod("add"),
	    set: createReadonlyMethod("set"),
	    delete: createReadonlyMethod("delete"),
	    clear: createReadonlyMethod("clear"),
	    forEach: createForEach(true, false)
	  };
	  var shallowReadonlyInstrumentations = {
	    get(key) {
	      return get$1(this, key, true, true);
	    },
	    get size() {
	      return size(this, true);
	    },
	    has(key) {
	      return has$1.call(this, key, true);
	    },
	    add: createReadonlyMethod("add"),
	    set: createReadonlyMethod("set"),
	    delete: createReadonlyMethod("delete"),
	    clear: createReadonlyMethod("clear"),
	    forEach: createForEach(true, true)
	  };
	  var iteratorMethods = ["keys", "values", "entries", Symbol.iterator];
	  iteratorMethods.forEach((method) => {
	    mutableInstrumentations[method] = createIterableMethod(method, false, false);
	    readonlyInstrumentations[method] = createIterableMethod(method, true, false);
	    shallowInstrumentations[method] = createIterableMethod(method, false, true);
	    shallowReadonlyInstrumentations[method] = createIterableMethod(method, true, true);
	  });
	  function createInstrumentationGetter(isReadonly2, shallow) {
	    const instrumentations = shallow ? isReadonly2 ? shallowReadonlyInstrumentations : shallowInstrumentations : isReadonly2 ? readonlyInstrumentations : mutableInstrumentations;
	    return (target, key, receiver) => {
	      if (key === "__v_isReactive") {
	        return !isReadonly2;
	      } else if (key === "__v_isReadonly") {
	        return isReadonly2;
	      } else if (key === "__v_raw") {
	        return target;
	      }
	      return Reflect.get(shared.hasOwn(instrumentations, key) && key in target ? instrumentations : target, key, receiver);
	    };
	  }
	  var mutableCollectionHandlers = {
	    get: createInstrumentationGetter(false, false)
	  };
	  var shallowCollectionHandlers = {
	    get: createInstrumentationGetter(false, true)
	  };
	  var readonlyCollectionHandlers = {
	    get: createInstrumentationGetter(true, false)
	  };
	  var shallowReadonlyCollectionHandlers = {
	    get: createInstrumentationGetter(true, true)
	  };
	  function checkIdentityKeys(target, has2, key) {
	    const rawKey = toRaw2(key);
	    if (rawKey !== key && has2.call(target, rawKey)) {
	      const type = shared.toRawType(target);
	      console.warn(`Reactive ${type} contains both the raw and reactive versions of the same object${type === `Map` ? ` as keys` : ``}, which can lead to inconsistencies. Avoid differentiating between the raw and reactive versions of an object and only use the reactive version if possible.`);
	    }
	  }
	  var reactiveMap = new WeakMap();
	  var shallowReactiveMap = new WeakMap();
	  var readonlyMap = new WeakMap();
	  var shallowReadonlyMap = new WeakMap();
	  function targetTypeMap(rawType) {
	    switch (rawType) {
	      case "Object":
	      case "Array":
	        return 1;
	      case "Map":
	      case "Set":
	      case "WeakMap":
	      case "WeakSet":
	        return 2;
	      default:
	        return 0;
	    }
	  }
	  function getTargetType(value) {
	    return value["__v_skip"] || !Object.isExtensible(value) ? 0 : targetTypeMap(shared.toRawType(value));
	  }
	  function reactive3(target) {
	    if (target && target["__v_isReadonly"]) {
	      return target;
	    }
	    return createReactiveObject(target, false, mutableHandlers, mutableCollectionHandlers, reactiveMap);
	  }
	  function shallowReactive(target) {
	    return createReactiveObject(target, false, shallowReactiveHandlers, shallowCollectionHandlers, shallowReactiveMap);
	  }
	  function readonly(target) {
	    return createReactiveObject(target, true, readonlyHandlers, readonlyCollectionHandlers, readonlyMap);
	  }
	  function shallowReadonly(target) {
	    return createReactiveObject(target, true, shallowReadonlyHandlers, shallowReadonlyCollectionHandlers, shallowReadonlyMap);
	  }
	  function createReactiveObject(target, isReadonly2, baseHandlers, collectionHandlers, proxyMap) {
	    if (!shared.isObject(target)) {
	      {
	        console.warn(`value cannot be made reactive: ${String(target)}`);
	      }
	      return target;
	    }
	    if (target["__v_raw"] && !(isReadonly2 && target["__v_isReactive"])) {
	      return target;
	    }
	    const existingProxy = proxyMap.get(target);
	    if (existingProxy) {
	      return existingProxy;
	    }
	    const targetType = getTargetType(target);
	    if (targetType === 0) {
	      return target;
	    }
	    const proxy = new Proxy(target, targetType === 2 ? collectionHandlers : baseHandlers);
	    proxyMap.set(target, proxy);
	    return proxy;
	  }
	  function isReactive2(value) {
	    if (isReadonly(value)) {
	      return isReactive2(value["__v_raw"]);
	    }
	    return !!(value && value["__v_isReactive"]);
	  }
	  function isReadonly(value) {
	    return !!(value && value["__v_isReadonly"]);
	  }
	  function isProxy(value) {
	    return isReactive2(value) || isReadonly(value);
	  }
	  function toRaw2(observed) {
	    return observed && toRaw2(observed["__v_raw"]) || observed;
	  }
	  function markRaw(value) {
	    shared.def(value, "__v_skip", true);
	    return value;
	  }
	  var convert = (val) => shared.isObject(val) ? reactive3(val) : val;
	  function isRef(r) {
	    return Boolean(r && r.__v_isRef === true);
	  }
	  function ref(value) {
	    return createRef(value);
	  }
	  function shallowRef(value) {
	    return createRef(value, true);
	  }
	  var RefImpl = class {
	    constructor(_rawValue, _shallow = false) {
	      this._rawValue = _rawValue;
	      this._shallow = _shallow;
	      this.__v_isRef = true;
	      this._value = _shallow ? _rawValue : convert(_rawValue);
	    }
	    get value() {
	      track(toRaw2(this), "get", "value");
	      return this._value;
	    }
	    set value(newVal) {
	      if (shared.hasChanged(toRaw2(newVal), this._rawValue)) {
	        this._rawValue = newVal;
	        this._value = this._shallow ? newVal : convert(newVal);
	        trigger(toRaw2(this), "set", "value", newVal);
	      }
	    }
	  };
	  function createRef(rawValue, shallow = false) {
	    if (isRef(rawValue)) {
	      return rawValue;
	    }
	    return new RefImpl(rawValue, shallow);
	  }
	  function triggerRef(ref2) {
	    trigger(toRaw2(ref2), "set", "value", ref2.value);
	  }
	  function unref(ref2) {
	    return isRef(ref2) ? ref2.value : ref2;
	  }
	  var shallowUnwrapHandlers = {
	    get: (target, key, receiver) => unref(Reflect.get(target, key, receiver)),
	    set: (target, key, value, receiver) => {
	      const oldValue = target[key];
	      if (isRef(oldValue) && !isRef(value)) {
	        oldValue.value = value;
	        return true;
	      } else {
	        return Reflect.set(target, key, value, receiver);
	      }
	    }
	  };
	  function proxyRefs(objectWithRefs) {
	    return isReactive2(objectWithRefs) ? objectWithRefs : new Proxy(objectWithRefs, shallowUnwrapHandlers);
	  }
	  var CustomRefImpl = class {
	    constructor(factory) {
	      this.__v_isRef = true;
	      const {get: get3, set: set3} = factory(() => track(this, "get", "value"), () => trigger(this, "set", "value"));
	      this._get = get3;
	      this._set = set3;
	    }
	    get value() {
	      return this._get();
	    }
	    set value(newVal) {
	      this._set(newVal);
	    }
	  };
	  function customRef(factory) {
	    return new CustomRefImpl(factory);
	  }
	  function toRefs(object) {
	    if (!isProxy(object)) {
	      console.warn(`toRefs() expects a reactive object but received a plain one.`);
	    }
	    const ret = shared.isArray(object) ? new Array(object.length) : {};
	    for (const key in object) {
	      ret[key] = toRef(object, key);
	    }
	    return ret;
	  }
	  var ObjectRefImpl = class {
	    constructor(_object, _key) {
	      this._object = _object;
	      this._key = _key;
	      this.__v_isRef = true;
	    }
	    get value() {
	      return this._object[this._key];
	    }
	    set value(newVal) {
	      this._object[this._key] = newVal;
	    }
	  };
	  function toRef(object, key) {
	    return isRef(object[key]) ? object[key] : new ObjectRefImpl(object, key);
	  }
	  var ComputedRefImpl = class {
	    constructor(getter, _setter, isReadonly2) {
	      this._setter = _setter;
	      this._dirty = true;
	      this.__v_isRef = true;
	      this.effect = effect3(getter, {
	        lazy: true,
	        scheduler: () => {
	          if (!this._dirty) {
	            this._dirty = true;
	            trigger(toRaw2(this), "set", "value");
	          }
	        }
	      });
	      this["__v_isReadonly"] = isReadonly2;
	    }
	    get value() {
	      const self2 = toRaw2(this);
	      if (self2._dirty) {
	        self2._value = this.effect();
	        self2._dirty = false;
	      }
	      track(self2, "get", "value");
	      return self2._value;
	    }
	    set value(newValue) {
	      this._setter(newValue);
	    }
	  };
	  function computed(getterOrOptions) {
	    let getter;
	    let setter;
	    if (shared.isFunction(getterOrOptions)) {
	      getter = getterOrOptions;
	      setter = () => {
	        console.warn("Write operation failed: computed value is readonly");
	      };
	    } else {
	      getter = getterOrOptions.get;
	      setter = getterOrOptions.set;
	    }
	    return new ComputedRefImpl(getter, setter, shared.isFunction(getterOrOptions) || !getterOrOptions.set);
	  }
	  exports.ITERATE_KEY = ITERATE_KEY;
	  exports.computed = computed;
	  exports.customRef = customRef;
	  exports.effect = effect3;
	  exports.enableTracking = enableTracking;
	  exports.isProxy = isProxy;
	  exports.isReactive = isReactive2;
	  exports.isReadonly = isReadonly;
	  exports.isRef = isRef;
	  exports.markRaw = markRaw;
	  exports.pauseTracking = pauseTracking;
	  exports.proxyRefs = proxyRefs;
	  exports.reactive = reactive3;
	  exports.readonly = readonly;
	  exports.ref = ref;
	  exports.resetTracking = resetTracking;
	  exports.shallowReactive = shallowReactive;
	  exports.shallowReadonly = shallowReadonly;
	  exports.shallowRef = shallowRef;
	  exports.stop = stop2;
	  exports.toRaw = toRaw2;
	  exports.toRef = toRef;
	  exports.toRefs = toRefs;
	  exports.track = track;
	  exports.trigger = trigger;
	  exports.triggerRef = triggerRef;
	  exports.unref = unref;
	});

	// node_modules/@vue/reactivity/index.js
	var require_reactivity = __commonJS((exports, module) => {
	  {
	    module.exports = require_reactivity_cjs();
	  }
	});

	// packages/alpinejs/src/scheduler.js
	var flushPending = false;
	var flushing = false;
	var queue = [];
	function scheduler(callback) {
	  queueJob(callback);
	}
	function queueJob(job) {
	  if (!queue.includes(job))
	    queue.push(job);
	  queueFlush();
	}
	function queueFlush() {
	  if (!flushing && !flushPending) {
	    flushPending = true;
	    queueMicrotask(flushJobs);
	  }
	}
	function flushJobs() {
	  flushPending = false;
	  flushing = true;
	  for (let i = 0; i < queue.length; i++) {
	    queue[i]();
	  }
	  queue.length = 0;
	  flushing = false;
	}

	// packages/alpinejs/src/reactivity.js
	var reactive;
	var effect;
	var release;
	var raw;
	var shouldSchedule = true;
	function disableEffectScheduling(callback) {
	  shouldSchedule = false;
	  callback();
	  shouldSchedule = true;
	}
	function setReactivityEngine(engine) {
	  reactive = engine.reactive;
	  release = engine.release;
	  effect = (callback) => engine.effect(callback, {scheduler: (task) => {
	    if (shouldSchedule) {
	      scheduler(task);
	    } else {
	      task();
	    }
	  }});
	  raw = engine.raw;
	}
	function overrideEffect(override) {
	  effect = override;
	}
	function elementBoundEffect(el) {
	  let cleanup = () => {
	  };
	  let wrappedEffect = (callback) => {
	    let effectReference = effect(callback);
	    if (!el._x_effects) {
	      el._x_effects = new Set();
	      el._x_runEffects = () => {
	        el._x_effects.forEach((i) => i());
	      };
	    }
	    el._x_effects.add(effectReference);
	    cleanup = () => {
	      if (effectReference === void 0)
	        return;
	      el._x_effects.delete(effectReference);
	      release(effectReference);
	    };
	  };
	  return [wrappedEffect, () => {
	    cleanup();
	  }];
	}

	// packages/alpinejs/src/mutation.js
	var onAttributeAddeds = [];
	var onElRemoveds = [];
	var onElAddeds = [];
	function onElAdded(callback) {
	  onElAddeds.push(callback);
	}
	function onElRemoved(callback) {
	  onElRemoveds.push(callback);
	}
	function onAttributesAdded(callback) {
	  onAttributeAddeds.push(callback);
	}
	function onAttributeRemoved(el, name, callback) {
	  if (!el._x_attributeCleanups)
	    el._x_attributeCleanups = {};
	  if (!el._x_attributeCleanups[name])
	    el._x_attributeCleanups[name] = [];
	  el._x_attributeCleanups[name].push(callback);
	}
	function cleanupAttributes(el, names) {
	  if (!el._x_attributeCleanups)
	    return;
	  Object.entries(el._x_attributeCleanups).forEach(([name, value]) => {
	    if (names === void 0 || names.includes(name)) {
	      value.forEach((i) => i());
	      delete el._x_attributeCleanups[name];
	    }
	  });
	}
	var observer = new MutationObserver(onMutate);
	var currentlyObserving = false;
	function startObservingMutations() {
	  observer.observe(document, {subtree: true, childList: true, attributes: true, attributeOldValue: true});
	  currentlyObserving = true;
	}
	function stopObservingMutations() {
	  flushObserver();
	  observer.disconnect();
	  currentlyObserving = false;
	}
	var recordQueue = [];
	var willProcessRecordQueue = false;
	function flushObserver() {
	  recordQueue = recordQueue.concat(observer.takeRecords());
	  if (recordQueue.length && !willProcessRecordQueue) {
	    willProcessRecordQueue = true;
	    queueMicrotask(() => {
	      processRecordQueue();
	      willProcessRecordQueue = false;
	    });
	  }
	}
	function processRecordQueue() {
	  onMutate(recordQueue);
	  recordQueue.length = 0;
	}
	function mutateDom(callback) {
	  if (!currentlyObserving)
	    return callback();
	  stopObservingMutations();
	  let result = callback();
	  startObservingMutations();
	  return result;
	}
	var isCollecting = false;
	var deferredMutations = [];
	function deferMutations() {
	  isCollecting = true;
	}
	function flushAndStopDeferringMutations() {
	  isCollecting = false;
	  onMutate(deferredMutations);
	  deferredMutations = [];
	}
	function onMutate(mutations) {
	  if (isCollecting) {
	    deferredMutations = deferredMutations.concat(mutations);
	    return;
	  }
	  let addedNodes = [];
	  let removedNodes = [];
	  let addedAttributes = new Map();
	  let removedAttributes = new Map();
	  for (let i = 0; i < mutations.length; i++) {
	    if (mutations[i].target._x_ignoreMutationObserver)
	      continue;
	    if (mutations[i].type === "childList") {
	      mutations[i].addedNodes.forEach((node) => node.nodeType === 1 && addedNodes.push(node));
	      mutations[i].removedNodes.forEach((node) => node.nodeType === 1 && removedNodes.push(node));
	    }
	    if (mutations[i].type === "attributes") {
	      let el = mutations[i].target;
	      let name = mutations[i].attributeName;
	      let oldValue = mutations[i].oldValue;
	      let add = () => {
	        if (!addedAttributes.has(el))
	          addedAttributes.set(el, []);
	        addedAttributes.get(el).push({name, value: el.getAttribute(name)});
	      };
	      let remove = () => {
	        if (!removedAttributes.has(el))
	          removedAttributes.set(el, []);
	        removedAttributes.get(el).push(name);
	      };
	      if (el.hasAttribute(name) && oldValue === null) {
	        add();
	      } else if (el.hasAttribute(name)) {
	        remove();
	        add();
	      } else {
	        remove();
	      }
	    }
	  }
	  removedAttributes.forEach((attrs, el) => {
	    cleanupAttributes(el, attrs);
	  });
	  addedAttributes.forEach((attrs, el) => {
	    onAttributeAddeds.forEach((i) => i(el, attrs));
	  });
	  for (let node of addedNodes) {
	    if (removedNodes.includes(node))
	      continue;
	    onElAddeds.forEach((i) => i(node));
	  }
	  for (let node of removedNodes) {
	    if (addedNodes.includes(node))
	      continue;
	    onElRemoveds.forEach((i) => i(node));
	  }
	  addedNodes = null;
	  removedNodes = null;
	  addedAttributes = null;
	  removedAttributes = null;
	}

	// packages/alpinejs/src/scope.js
	function addScopeToNode(node, data2, referenceNode) {
	  node._x_dataStack = [data2, ...closestDataStack(referenceNode || node)];
	  return () => {
	    node._x_dataStack = node._x_dataStack.filter((i) => i !== data2);
	  };
	}
	function refreshScope(element, scope) {
	  let existingScope = element._x_dataStack[0];
	  Object.entries(scope).forEach(([key, value]) => {
	    existingScope[key] = value;
	  });
	}
	function closestDataStack(node) {
	  if (node._x_dataStack)
	    return node._x_dataStack;
	  if (typeof ShadowRoot === "function" && node instanceof ShadowRoot) {
	    return closestDataStack(node.host);
	  }
	  if (!node.parentNode) {
	    return [];
	  }
	  return closestDataStack(node.parentNode);
	}
	function mergeProxies(objects) {
	  let thisProxy = new Proxy({}, {
	    ownKeys: () => {
	      return Array.from(new Set(objects.flatMap((i) => Object.keys(i))));
	    },
	    has: (target, name) => {
	      return objects.some((obj) => obj.hasOwnProperty(name));
	    },
	    get: (target, name) => {
	      return (objects.find((obj) => {
	        if (obj.hasOwnProperty(name)) {
	          let descriptor = Object.getOwnPropertyDescriptor(obj, name);
	          if (descriptor.get && descriptor.get._x_alreadyBound || descriptor.set && descriptor.set._x_alreadyBound) {
	            return true;
	          }
	          if ((descriptor.get || descriptor.set) && descriptor.enumerable) {
	            let getter = descriptor.get;
	            let setter = descriptor.set;
	            let property = descriptor;
	            getter = getter && getter.bind(thisProxy);
	            setter = setter && setter.bind(thisProxy);
	            if (getter)
	              getter._x_alreadyBound = true;
	            if (setter)
	              setter._x_alreadyBound = true;
	            Object.defineProperty(obj, name, {
	              ...property,
	              get: getter,
	              set: setter
	            });
	          }
	          return true;
	        }
	        return false;
	      }) || {})[name];
	    },
	    set: (target, name, value) => {
	      let closestObjectWithKey = objects.find((obj) => obj.hasOwnProperty(name));
	      if (closestObjectWithKey) {
	        closestObjectWithKey[name] = value;
	      } else {
	        objects[objects.length - 1][name] = value;
	      }
	      return true;
	    }
	  });
	  return thisProxy;
	}

	// packages/alpinejs/src/interceptor.js
	function initInterceptors(data2) {
	  let isObject = (val) => typeof val === "object" && !Array.isArray(val) && val !== null;
	  let recurse = (obj, basePath = "") => {
	    Object.entries(Object.getOwnPropertyDescriptors(obj)).forEach(([key, {value, enumerable}]) => {
	      if (enumerable === false || value === void 0)
	        return;
	      let path = basePath === "" ? key : `${basePath}.${key}`;
	      if (typeof value === "object" && value !== null && value._x_interceptor) {
	        obj[key] = value.initialize(data2, path, key);
	      } else {
	        if (isObject(value) && value !== obj && !(value instanceof Element)) {
	          recurse(value, path);
	        }
	      }
	    });
	  };
	  return recurse(data2);
	}
	function interceptor(callback, mutateObj = () => {
	}) {
	  let obj = {
	    initialValue: void 0,
	    _x_interceptor: true,
	    initialize(data2, path, key) {
	      return callback(this.initialValue, () => get(data2, path), (value) => set(data2, path, value), path, key);
	    }
	  };
	  mutateObj(obj);
	  return (initialValue) => {
	    if (typeof initialValue === "object" && initialValue !== null && initialValue._x_interceptor) {
	      let initialize = obj.initialize.bind(obj);
	      obj.initialize = (data2, path, key) => {
	        let innerValue = initialValue.initialize(data2, path, key);
	        obj.initialValue = innerValue;
	        return initialize(data2, path, key);
	      };
	    } else {
	      obj.initialValue = initialValue;
	    }
	    return obj;
	  };
	}
	function get(obj, path) {
	  return path.split(".").reduce((carry, segment) => carry[segment], obj);
	}
	function set(obj, path, value) {
	  if (typeof path === "string")
	    path = path.split(".");
	  if (path.length === 1)
	    obj[path[0]] = value;
	  else if (path.length === 0)
	    throw error;
	  else {
	    if (obj[path[0]])
	      return set(obj[path[0]], path.slice(1), value);
	    else {
	      obj[path[0]] = {};
	      return set(obj[path[0]], path.slice(1), value);
	    }
	  }
	}

	// packages/alpinejs/src/magics.js
	var magics = {};
	function magic(name, callback) {
	  magics[name] = callback;
	}
	function injectMagics(obj, el) {
	  Object.entries(magics).forEach(([name, callback]) => {
	    Object.defineProperty(obj, `$${name}`, {
	      get() {
	        return callback(el, {Alpine: alpine_default, interceptor});
	      },
	      enumerable: false
	    });
	  });
	  return obj;
	}

	// packages/alpinejs/src/utils/error.js
	function tryCatch(el, expression, callback, ...args) {
	  try {
	    return callback(...args);
	  } catch (e) {
	    handleError(e, el, expression);
	  }
	}
	function handleError(error2, el, expression = void 0) {
	  Object.assign(error2, {el, expression});
	  console.warn(`Alpine Expression Error: ${error2.message}

${expression ? 'Expression: "' + expression + '"\n\n' : ""}`, el);
	  setTimeout(() => {
	    throw error2;
	  }, 0);
	}

	// packages/alpinejs/src/evaluator.js
	function evaluate(el, expression, extras = {}) {
	  let result;
	  evaluateLater(el, expression)((value) => result = value, extras);
	  return result;
	}
	function evaluateLater(...args) {
	  return theEvaluatorFunction(...args);
	}
	var theEvaluatorFunction = normalEvaluator;
	function setEvaluator(newEvaluator) {
	  theEvaluatorFunction = newEvaluator;
	}
	function normalEvaluator(el, expression) {
	  let overriddenMagics = {};
	  injectMagics(overriddenMagics, el);
	  let dataStack = [overriddenMagics, ...closestDataStack(el)];
	  if (typeof expression === "function") {
	    return generateEvaluatorFromFunction(dataStack, expression);
	  }
	  let evaluator = generateEvaluatorFromString(dataStack, expression, el);
	  return tryCatch.bind(null, el, expression, evaluator);
	}
	function generateEvaluatorFromFunction(dataStack, func) {
	  return (receiver = () => {
	  }, {scope = {}, params = []} = {}) => {
	    let result = func.apply(mergeProxies([scope, ...dataStack]), params);
	    runIfTypeOfFunction(receiver, result);
	  };
	}
	var evaluatorMemo = {};
	function generateFunctionFromString(expression, el) {
	  if (evaluatorMemo[expression]) {
	    return evaluatorMemo[expression];
	  }
	  let AsyncFunction = Object.getPrototypeOf(async function() {
	  }).constructor;
	  let rightSideSafeExpression = /^[\n\s]*if.*\(.*\)/.test(expression) || /^(let|const)\s/.test(expression) ? `(() => { ${expression} })()` : expression;
	  const safeAsyncFunction = () => {
	    try {
	      return new AsyncFunction(["__self", "scope"], `with (scope) { __self.result = ${rightSideSafeExpression} }; __self.finished = true; return __self.result;`);
	    } catch (error2) {
	      handleError(error2, el, expression);
	      return Promise.resolve();
	    }
	  };
	  let func = safeAsyncFunction();
	  evaluatorMemo[expression] = func;
	  return func;
	}
	function generateEvaluatorFromString(dataStack, expression, el) {
	  let func = generateFunctionFromString(expression, el);
	  return (receiver = () => {
	  }, {scope = {}, params = []} = {}) => {
	    func.result = void 0;
	    func.finished = false;
	    let completeScope = mergeProxies([scope, ...dataStack]);
	    if (typeof func === "function") {
	      let promise = func(func, completeScope).catch((error2) => handleError(error2, el, expression));
	      if (func.finished) {
	        runIfTypeOfFunction(receiver, func.result, completeScope, params, el);
	      } else {
	        promise.then((result) => {
	          runIfTypeOfFunction(receiver, result, completeScope, params, el);
	        }).catch((error2) => handleError(error2, el, expression));
	      }
	    }
	  };
	}
	function runIfTypeOfFunction(receiver, value, scope, params, el) {
	  if (typeof value === "function") {
	    let result = value.apply(scope, params);
	    if (result instanceof Promise) {
	      result.then((i) => runIfTypeOfFunction(receiver, i, scope, params)).catch((error2) => handleError(error2, el, value));
	    } else {
	      receiver(result);
	    }
	  } else {
	    receiver(value);
	  }
	}

	// packages/alpinejs/src/directives.js
	var prefixAsString = "x-";
	function prefix(subject = "") {
	  return prefixAsString + subject;
	}
	function setPrefix(newPrefix) {
	  prefixAsString = newPrefix;
	}
	var directiveHandlers = {};
	function directive(name, callback) {
	  directiveHandlers[name] = callback;
	}
	function directives(el, attributes, originalAttributeOverride) {
	  let transformedAttributeMap = {};
	  let directives2 = Array.from(attributes).map(toTransformedAttributes((newName, oldName) => transformedAttributeMap[newName] = oldName)).filter(outNonAlpineAttributes).map(toParsedDirectives(transformedAttributeMap, originalAttributeOverride)).sort(byPriority);
	  return directives2.map((directive2) => {
	    return getDirectiveHandler(el, directive2);
	  });
	}
	function attributesOnly(attributes) {
	  return Array.from(attributes).map(toTransformedAttributes()).filter((attr) => !outNonAlpineAttributes(attr));
	}
	var isDeferringHandlers = false;
	var directiveHandlerStacks = new Map();
	var currentHandlerStackKey = Symbol();
	function deferHandlingDirectives(callback) {
	  isDeferringHandlers = true;
	  let key = Symbol();
	  currentHandlerStackKey = key;
	  directiveHandlerStacks.set(key, []);
	  let flushHandlers = () => {
	    while (directiveHandlerStacks.get(key).length)
	      directiveHandlerStacks.get(key).shift()();
	    directiveHandlerStacks.delete(key);
	  };
	  let stopDeferring = () => {
	    isDeferringHandlers = false;
	    flushHandlers();
	  };
	  callback(flushHandlers);
	  stopDeferring();
	}
	function getDirectiveHandler(el, directive2) {
	  let noop = () => {
	  };
	  let handler3 = directiveHandlers[directive2.type] || noop;
	  let cleanups = [];
	  let cleanup = (callback) => cleanups.push(callback);
	  let [effect3, cleanupEffect] = elementBoundEffect(el);
	  cleanups.push(cleanupEffect);
	  let utilities = {
	    Alpine: alpine_default,
	    effect: effect3,
	    cleanup,
	    evaluateLater: evaluateLater.bind(evaluateLater, el),
	    evaluate: evaluate.bind(evaluate, el)
	  };
	  let doCleanup = () => cleanups.forEach((i) => i());
	  onAttributeRemoved(el, directive2.original, doCleanup);
	  let fullHandler = () => {
	    if (el._x_ignore || el._x_ignoreSelf)
	      return;
	    handler3.inline && handler3.inline(el, directive2, utilities);
	    handler3 = handler3.bind(handler3, el, directive2, utilities);
	    isDeferringHandlers ? directiveHandlerStacks.get(currentHandlerStackKey).push(handler3) : handler3();
	  };
	  fullHandler.runCleanups = doCleanup;
	  return fullHandler;
	}
	var startingWith = (subject, replacement) => ({name, value}) => {
	  if (name.startsWith(subject))
	    name = name.replace(subject, replacement);
	  return {name, value};
	};
	var into = (i) => i;
	function toTransformedAttributes(callback = () => {
	}) {
	  return ({name, value}) => {
	    let {name: newName, value: newValue} = attributeTransformers.reduce((carry, transform) => {
	      return transform(carry);
	    }, {name, value});
	    if (newName !== name)
	      callback(newName, name);
	    return {name: newName, value: newValue};
	  };
	}
	var attributeTransformers = [];
	function mapAttributes(callback) {
	  attributeTransformers.push(callback);
	}
	function outNonAlpineAttributes({name}) {
	  return alpineAttributeRegex().test(name);
	}
	var alpineAttributeRegex = () => new RegExp(`^${prefixAsString}([^:^.]+)\\b`);
	function toParsedDirectives(transformedAttributeMap, originalAttributeOverride) {
	  return ({name, value}) => {
	    let typeMatch = name.match(alpineAttributeRegex());
	    let valueMatch = name.match(/:([a-zA-Z0-9\-:]+)/);
	    let modifiers = name.match(/\.[^.\]]+(?=[^\]]*$)/g) || [];
	    let original = originalAttributeOverride || transformedAttributeMap[name] || name;
	    return {
	      type: typeMatch ? typeMatch[1] : null,
	      value: valueMatch ? valueMatch[1] : null,
	      modifiers: modifiers.map((i) => i.replace(".", "")),
	      expression: value,
	      original
	    };
	  };
	}
	var DEFAULT = "DEFAULT";
	var directiveOrder = [
	  "ignore",
	  "ref",
	  "data",
	  "bind",
	  "init",
	  "for",
	  "model",
	  "transition",
	  "show",
	  "if",
	  DEFAULT,
	  "element"
	];
	function byPriority(a, b) {
	  let typeA = directiveOrder.indexOf(a.type) === -1 ? DEFAULT : a.type;
	  let typeB = directiveOrder.indexOf(b.type) === -1 ? DEFAULT : b.type;
	  return directiveOrder.indexOf(typeA) - directiveOrder.indexOf(typeB);
	}

	// packages/alpinejs/src/utils/dispatch.js
	function dispatch(el, name, detail = {}) {
	  el.dispatchEvent(new CustomEvent(name, {
	    detail,
	    bubbles: true,
	    composed: true,
	    cancelable: true
	  }));
	}

	// packages/alpinejs/src/nextTick.js
	var tickStack = [];
	var isHolding = false;
	function nextTick(callback) {
	  tickStack.push(callback);
	  queueMicrotask(() => {
	    isHolding || setTimeout(() => {
	      releaseNextTicks();
	    });
	  });
	}
	function releaseNextTicks() {
	  isHolding = false;
	  while (tickStack.length)
	    tickStack.shift()();
	}
	function holdNextTicks() {
	  isHolding = true;
	}

	// packages/alpinejs/src/utils/walk.js
	function walk(el, callback) {
	  if (typeof ShadowRoot === "function" && el instanceof ShadowRoot) {
	    Array.from(el.children).forEach((el2) => walk(el2, callback));
	    return;
	  }
	  let skip = false;
	  callback(el, () => skip = true);
	  if (skip)
	    return;
	  let node = el.firstElementChild;
	  while (node) {
	    walk(node, callback);
	    node = node.nextElementSibling;
	  }
	}

	// packages/alpinejs/src/utils/warn.js
	function warn(message, ...args) {
	  console.warn(`Alpine Warning: ${message}`, ...args);
	}

	// packages/alpinejs/src/lifecycle.js
	function start() {
	  if (!document.body)
	    warn("Unable to initialize. Trying to load Alpine before `<body>` is available. Did you forget to add `defer` in Alpine's `<script>` tag?");
	  dispatch(document, "alpine:init");
	  dispatch(document, "alpine:initializing");
	  startObservingMutations();
	  onElAdded((el) => initTree(el, walk));
	  onElRemoved((el) => nextTick(() => destroyTree(el)));
	  onAttributesAdded((el, attrs) => {
	    directives(el, attrs).forEach((handle) => handle());
	  });
	  let outNestedComponents = (el) => !closestRoot(el.parentElement, true);
	  Array.from(document.querySelectorAll(allSelectors())).filter(outNestedComponents).forEach((el) => {
	    initTree(el);
	  });
	  dispatch(document, "alpine:initialized");
	}
	var rootSelectorCallbacks = [];
	var initSelectorCallbacks = [];
	function rootSelectors() {
	  return rootSelectorCallbacks.map((fn) => fn());
	}
	function allSelectors() {
	  return rootSelectorCallbacks.concat(initSelectorCallbacks).map((fn) => fn());
	}
	function addRootSelector(selectorCallback) {
	  rootSelectorCallbacks.push(selectorCallback);
	}
	function addInitSelector(selectorCallback) {
	  initSelectorCallbacks.push(selectorCallback);
	}
	function closestRoot(el, includeInitSelectors = false) {
	  if (!el)
	    return;
	  const selectors = includeInitSelectors ? allSelectors() : rootSelectors();
	  if (selectors.some((selector) => el.matches(selector)))
	    return el;
	  if (!el.parentElement)
	    return;
	  return closestRoot(el.parentElement, includeInitSelectors);
	}
	function isRoot(el) {
	  return rootSelectors().some((selector) => el.matches(selector));
	}
	function initTree(el, walker = walk) {
	  deferHandlingDirectives(() => {
	    walker(el, (el2, skip) => {
	      directives(el2, el2.attributes).forEach((handle) => handle());
	      el2._x_ignore && skip();
	    });
	  });
	}
	function destroyTree(root) {
	  walk(root, (el) => cleanupAttributes(el));
	}

	// packages/alpinejs/src/utils/classes.js
	function setClasses(el, value) {
	  if (Array.isArray(value)) {
	    return setClassesFromString(el, value.join(" "));
	  } else if (typeof value === "object" && value !== null) {
	    return setClassesFromObject(el, value);
	  } else if (typeof value === "function") {
	    return setClasses(el, value());
	  }
	  return setClassesFromString(el, value);
	}
	function setClassesFromString(el, classString) {
	  let missingClasses = (classString2) => classString2.split(" ").filter((i) => !el.classList.contains(i)).filter(Boolean);
	  let addClassesAndReturnUndo = (classes) => {
	    el.classList.add(...classes);
	    return () => {
	      el.classList.remove(...classes);
	    };
	  };
	  classString = classString === true ? classString = "" : classString || "";
	  return addClassesAndReturnUndo(missingClasses(classString));
	}
	function setClassesFromObject(el, classObject) {
	  let split = (classString) => classString.split(" ").filter(Boolean);
	  let forAdd = Object.entries(classObject).flatMap(([classString, bool]) => bool ? split(classString) : false).filter(Boolean);
	  let forRemove = Object.entries(classObject).flatMap(([classString, bool]) => !bool ? split(classString) : false).filter(Boolean);
	  let added = [];
	  let removed = [];
	  forRemove.forEach((i) => {
	    if (el.classList.contains(i)) {
	      el.classList.remove(i);
	      removed.push(i);
	    }
	  });
	  forAdd.forEach((i) => {
	    if (!el.classList.contains(i)) {
	      el.classList.add(i);
	      added.push(i);
	    }
	  });
	  return () => {
	    removed.forEach((i) => el.classList.add(i));
	    added.forEach((i) => el.classList.remove(i));
	  };
	}

	// packages/alpinejs/src/utils/styles.js
	function setStyles(el, value) {
	  if (typeof value === "object" && value !== null) {
	    return setStylesFromObject(el, value);
	  }
	  return setStylesFromString(el, value);
	}
	function setStylesFromObject(el, value) {
	  let previousStyles = {};
	  Object.entries(value).forEach(([key, value2]) => {
	    previousStyles[key] = el.style[key];
	    el.style.setProperty(kebabCase(key), value2);
	  });
	  setTimeout(() => {
	    if (el.style.length === 0) {
	      el.removeAttribute("style");
	    }
	  });
	  return () => {
	    setStyles(el, previousStyles);
	  };
	}
	function setStylesFromString(el, value) {
	  let cache = el.getAttribute("style", value);
	  el.setAttribute("style", value);
	  return () => {
	    el.setAttribute("style", cache);
	  };
	}
	function kebabCase(subject) {
	  return subject.replace(/([a-z])([A-Z])/g, "$1-$2").toLowerCase();
	}

	// packages/alpinejs/src/utils/once.js
	function once(callback, fallback = () => {
	}) {
	  let called = false;
	  return function() {
	    if (!called) {
	      called = true;
	      callback.apply(this, arguments);
	    } else {
	      fallback.apply(this, arguments);
	    }
	  };
	}

	// packages/alpinejs/src/directives/x-transition.js
	directive("transition", (el, {value, modifiers, expression}, {evaluate: evaluate2}) => {
	  if (typeof expression === "function")
	    expression = evaluate2(expression);
	  if (!expression) {
	    registerTransitionsFromHelper(el, modifiers, value);
	  } else {
	    registerTransitionsFromClassString(el, expression, value);
	  }
	});
	function registerTransitionsFromClassString(el, classString, stage) {
	  registerTransitionObject(el, setClasses, "");
	  let directiveStorageMap = {
	    enter: (classes) => {
	      el._x_transition.enter.during = classes;
	    },
	    "enter-start": (classes) => {
	      el._x_transition.enter.start = classes;
	    },
	    "enter-end": (classes) => {
	      el._x_transition.enter.end = classes;
	    },
	    leave: (classes) => {
	      el._x_transition.leave.during = classes;
	    },
	    "leave-start": (classes) => {
	      el._x_transition.leave.start = classes;
	    },
	    "leave-end": (classes) => {
	      el._x_transition.leave.end = classes;
	    }
	  };
	  directiveStorageMap[stage](classString);
	}
	function registerTransitionsFromHelper(el, modifiers, stage) {
	  registerTransitionObject(el, setStyles);
	  let doesntSpecify = !modifiers.includes("in") && !modifiers.includes("out") && !stage;
	  let transitioningIn = doesntSpecify || modifiers.includes("in") || ["enter"].includes(stage);
	  let transitioningOut = doesntSpecify || modifiers.includes("out") || ["leave"].includes(stage);
	  if (modifiers.includes("in") && !doesntSpecify) {
	    modifiers = modifiers.filter((i, index) => index < modifiers.indexOf("out"));
	  }
	  if (modifiers.includes("out") && !doesntSpecify) {
	    modifiers = modifiers.filter((i, index) => index > modifiers.indexOf("out"));
	  }
	  let wantsAll = !modifiers.includes("opacity") && !modifiers.includes("scale");
	  let wantsOpacity = wantsAll || modifiers.includes("opacity");
	  let wantsScale = wantsAll || modifiers.includes("scale");
	  let opacityValue = wantsOpacity ? 0 : 1;
	  let scaleValue = wantsScale ? modifierValue(modifiers, "scale", 95) / 100 : 1;
	  let delay = modifierValue(modifiers, "delay", 0);
	  let origin = modifierValue(modifiers, "origin", "center");
	  let property = "opacity, transform";
	  let durationIn = modifierValue(modifiers, "duration", 150) / 1e3;
	  let durationOut = modifierValue(modifiers, "duration", 75) / 1e3;
	  let easing = `cubic-bezier(0.4, 0.0, 0.2, 1)`;
	  if (transitioningIn) {
	    el._x_transition.enter.during = {
	      transformOrigin: origin,
	      transitionDelay: delay,
	      transitionProperty: property,
	      transitionDuration: `${durationIn}s`,
	      transitionTimingFunction: easing
	    };
	    el._x_transition.enter.start = {
	      opacity: opacityValue,
	      transform: `scale(${scaleValue})`
	    };
	    el._x_transition.enter.end = {
	      opacity: 1,
	      transform: `scale(1)`
	    };
	  }
	  if (transitioningOut) {
	    el._x_transition.leave.during = {
	      transformOrigin: origin,
	      transitionDelay: delay,
	      transitionProperty: property,
	      transitionDuration: `${durationOut}s`,
	      transitionTimingFunction: easing
	    };
	    el._x_transition.leave.start = {
	      opacity: 1,
	      transform: `scale(1)`
	    };
	    el._x_transition.leave.end = {
	      opacity: opacityValue,
	      transform: `scale(${scaleValue})`
	    };
	  }
	}
	function registerTransitionObject(el, setFunction, defaultValue = {}) {
	  if (!el._x_transition)
	    el._x_transition = {
	      enter: {during: defaultValue, start: defaultValue, end: defaultValue},
	      leave: {during: defaultValue, start: defaultValue, end: defaultValue},
	      in(before = () => {
	      }, after = () => {
	      }) {
	        transition(el, setFunction, {
	          during: this.enter.during,
	          start: this.enter.start,
	          end: this.enter.end
	        }, before, after);
	      },
	      out(before = () => {
	      }, after = () => {
	      }) {
	        transition(el, setFunction, {
	          during: this.leave.during,
	          start: this.leave.start,
	          end: this.leave.end
	        }, before, after);
	      }
	    };
	}
	window.Element.prototype._x_toggleAndCascadeWithTransitions = function(el, value, show, hide) {
	  let clickAwayCompatibleShow = () => {
	    document.visibilityState === "visible" ? requestAnimationFrame(show) : setTimeout(show);
	  };
	  if (value) {
	    if (el._x_transition && (el._x_transition.enter || el._x_transition.leave)) {
	      el._x_transition.enter && (Object.entries(el._x_transition.enter.during).length || Object.entries(el._x_transition.enter.start).length || Object.entries(el._x_transition.enter.end).length) ? el._x_transition.in(show) : clickAwayCompatibleShow();
	    } else {
	      el._x_transition ? el._x_transition.in(show) : clickAwayCompatibleShow();
	    }
	    return;
	  }
	  el._x_hidePromise = el._x_transition ? new Promise((resolve, reject) => {
	    el._x_transition.out(() => {
	    }, () => resolve(hide));
	    el._x_transitioning.beforeCancel(() => reject({isFromCancelledTransition: true}));
	  }) : Promise.resolve(hide);
	  queueMicrotask(() => {
	    let closest = closestHide(el);
	    if (closest) {
	      if (!closest._x_hideChildren)
	        closest._x_hideChildren = [];
	      closest._x_hideChildren.push(el);
	    } else {
	      queueMicrotask(() => {
	        let hideAfterChildren = (el2) => {
	          let carry = Promise.all([
	            el2._x_hidePromise,
	            ...(el2._x_hideChildren || []).map(hideAfterChildren)
	          ]).then(([i]) => i());
	          delete el2._x_hidePromise;
	          delete el2._x_hideChildren;
	          return carry;
	        };
	        hideAfterChildren(el).catch((e) => {
	          if (!e.isFromCancelledTransition)
	            throw e;
	        });
	      });
	    }
	  });
	};
	function closestHide(el) {
	  let parent = el.parentNode;
	  if (!parent)
	    return;
	  return parent._x_hidePromise ? parent : closestHide(parent);
	}
	function transition(el, setFunction, {during, start: start2, end} = {}, before = () => {
	}, after = () => {
	}) {
	  if (el._x_transitioning)
	    el._x_transitioning.cancel();
	  if (Object.keys(during).length === 0 && Object.keys(start2).length === 0 && Object.keys(end).length === 0) {
	    before();
	    after();
	    return;
	  }
	  let undoStart, undoDuring, undoEnd;
	  performTransition(el, {
	    start() {
	      undoStart = setFunction(el, start2);
	    },
	    during() {
	      undoDuring = setFunction(el, during);
	    },
	    before,
	    end() {
	      undoStart();
	      undoEnd = setFunction(el, end);
	    },
	    after,
	    cleanup() {
	      undoDuring();
	      undoEnd();
	    }
	  });
	}
	function performTransition(el, stages) {
	  let interrupted, reachedBefore, reachedEnd;
	  let finish = once(() => {
	    mutateDom(() => {
	      interrupted = true;
	      if (!reachedBefore)
	        stages.before();
	      if (!reachedEnd) {
	        stages.end();
	        releaseNextTicks();
	      }
	      stages.after();
	      if (el.isConnected)
	        stages.cleanup();
	      delete el._x_transitioning;
	    });
	  });
	  el._x_transitioning = {
	    beforeCancels: [],
	    beforeCancel(callback) {
	      this.beforeCancels.push(callback);
	    },
	    cancel: once(function() {
	      while (this.beforeCancels.length) {
	        this.beforeCancels.shift()();
	      }
	      finish();
	    }),
	    finish
	  };
	  mutateDom(() => {
	    stages.start();
	    stages.during();
	  });
	  holdNextTicks();
	  requestAnimationFrame(() => {
	    if (interrupted)
	      return;
	    let duration = Number(getComputedStyle(el).transitionDuration.replace(/,.*/, "").replace("s", "")) * 1e3;
	    let delay = Number(getComputedStyle(el).transitionDelay.replace(/,.*/, "").replace("s", "")) * 1e3;
	    if (duration === 0)
	      duration = Number(getComputedStyle(el).animationDuration.replace("s", "")) * 1e3;
	    mutateDom(() => {
	      stages.before();
	    });
	    reachedBefore = true;
	    requestAnimationFrame(() => {
	      if (interrupted)
	        return;
	      mutateDom(() => {
	        stages.end();
	      });
	      releaseNextTicks();
	      setTimeout(el._x_transitioning.finish, duration + delay);
	      reachedEnd = true;
	    });
	  });
	}
	function modifierValue(modifiers, key, fallback) {
	  if (modifiers.indexOf(key) === -1)
	    return fallback;
	  const rawValue = modifiers[modifiers.indexOf(key) + 1];
	  if (!rawValue)
	    return fallback;
	  if (key === "scale") {
	    if (isNaN(rawValue))
	      return fallback;
	  }
	  if (key === "duration") {
	    let match = rawValue.match(/([0-9]+)ms/);
	    if (match)
	      return match[1];
	  }
	  if (key === "origin") {
	    if (["top", "right", "left", "center", "bottom"].includes(modifiers[modifiers.indexOf(key) + 2])) {
	      return [rawValue, modifiers[modifiers.indexOf(key) + 2]].join(" ");
	    }
	  }
	  return rawValue;
	}

	// packages/alpinejs/src/utils/debounce.js
	function debounce(func, wait) {
	  var timeout;
	  return function() {
	    var context = this, args = arguments;
	    var later = function() {
	      timeout = null;
	      func.apply(context, args);
	    };
	    clearTimeout(timeout);
	    timeout = setTimeout(later, wait);
	  };
	}

	// packages/alpinejs/src/utils/throttle.js
	function throttle(func, limit) {
	  let inThrottle;
	  return function() {
	    let context = this, args = arguments;
	    if (!inThrottle) {
	      func.apply(context, args);
	      inThrottle = true;
	      setTimeout(() => inThrottle = false, limit);
	    }
	  };
	}

	// packages/alpinejs/src/plugin.js
	function plugin(callback) {
	  callback(alpine_default);
	}

	// packages/alpinejs/src/store.js
	var stores = {};
	var isReactive = false;
	function store(name, value) {
	  if (!isReactive) {
	    stores = reactive(stores);
	    isReactive = true;
	  }
	  if (value === void 0) {
	    return stores[name];
	  }
	  stores[name] = value;
	  if (typeof value === "object" && value !== null && value.hasOwnProperty("init") && typeof value.init === "function") {
	    stores[name].init();
	  }
	  initInterceptors(stores[name]);
	}
	function getStores() {
	  return stores;
	}

	// packages/alpinejs/src/clone.js
	var isCloning = false;
	function skipDuringClone(callback, fallback = () => {
	}) {
	  return (...args) => isCloning ? fallback(...args) : callback(...args);
	}
	function clone(oldEl, newEl) {
	  newEl._x_dataStack = oldEl._x_dataStack;
	  isCloning = true;
	  dontRegisterReactiveSideEffects(() => {
	    cloneTree(newEl);
	  });
	  isCloning = false;
	}
	function cloneTree(el) {
	  let hasRunThroughFirstEl = false;
	  let shallowWalker = (el2, callback) => {
	    walk(el2, (el3, skip) => {
	      if (hasRunThroughFirstEl && isRoot(el3))
	        return skip();
	      hasRunThroughFirstEl = true;
	      callback(el3, skip);
	    });
	  };
	  initTree(el, shallowWalker);
	}
	function dontRegisterReactiveSideEffects(callback) {
	  let cache = effect;
	  overrideEffect((callback2, el) => {
	    let storedEffect = cache(callback2);
	    release(storedEffect);
	    return () => {
	    };
	  });
	  callback();
	  overrideEffect(cache);
	}

	// packages/alpinejs/src/datas.js
	var datas = {};
	function data(name, callback) {
	  datas[name] = callback;
	}
	function injectDataProviders(obj, context) {
	  Object.entries(datas).forEach(([name, callback]) => {
	    Object.defineProperty(obj, name, {
	      get() {
	        return (...args) => {
	          return callback.bind(context)(...args);
	        };
	      },
	      enumerable: false
	    });
	  });
	  return obj;
	}

	// packages/alpinejs/src/alpine.js
	var Alpine = {
	  get reactive() {
	    return reactive;
	  },
	  get release() {
	    return release;
	  },
	  get effect() {
	    return effect;
	  },
	  get raw() {
	    return raw;
	  },
	  version: "3.5.1",
	  flushAndStopDeferringMutations,
	  disableEffectScheduling,
	  setReactivityEngine,
	  skipDuringClone,
	  addRootSelector,
	  deferMutations,
	  mapAttributes,
	  evaluateLater,
	  setEvaluator,
	  mergeProxies,
	  closestRoot,
	  interceptor,
	  transition,
	  setStyles,
	  mutateDom,
	  directive,
	  throttle,
	  debounce,
	  evaluate,
	  initTree,
	  nextTick,
	  prefix: setPrefix,
	  plugin,
	  magic,
	  store,
	  start,
	  clone,
	  data
	};
	var alpine_default = Alpine;

	// packages/alpinejs/src/index.js
	var import_reactivity9 = __toModule(require_reactivity());

	// packages/alpinejs/src/magics/$nextTick.js
	magic("nextTick", () => nextTick);

	// packages/alpinejs/src/magics/$dispatch.js
	magic("dispatch", (el) => dispatch.bind(dispatch, el));

	// packages/alpinejs/src/magics/$watch.js
	magic("watch", (el) => (key, callback) => {
	  let evaluate2 = evaluateLater(el, key);
	  let firstTime = true;
	  let oldValue;
	  effect(() => evaluate2((value) => {
	    let div = document.createElement("div");
	    div.dataset.throwAway = value;
	    if (!firstTime) {
	      queueMicrotask(() => {
	        callback(value, oldValue);
	        oldValue = value;
	      });
	    } else {
	      oldValue = value;
	    }
	    firstTime = false;
	  }));
	});

	// packages/alpinejs/src/magics/$store.js
	magic("store", getStores);

	// packages/alpinejs/src/magics/$data.js
	magic("data", (el) => {
	  return mergeProxies(closestDataStack(el));
	});

	// packages/alpinejs/src/magics/$root.js
	magic("root", (el) => closestRoot(el));

	// packages/alpinejs/src/magics/$refs.js
	magic("refs", (el) => {
	  if (el._x_refs_proxy)
	    return el._x_refs_proxy;
	  el._x_refs_proxy = mergeProxies(getArrayOfRefObject(el));
	  return el._x_refs_proxy;
	});
	function getArrayOfRefObject(el) {
	  let refObjects = [];
	  let currentEl = el;
	  while (currentEl) {
	    if (currentEl._x_refs)
	      refObjects.push(currentEl._x_refs);
	    currentEl = currentEl.parentNode;
	  }
	  return refObjects;
	}

	// packages/alpinejs/src/magics/$el.js
	magic("el", (el) => el);

	// packages/alpinejs/src/directives/x-ignore.js
	var handler = () => {
	};
	handler.inline = (el, {modifiers}, {cleanup}) => {
	  modifiers.includes("self") ? el._x_ignoreSelf = true : el._x_ignore = true;
	  cleanup(() => {
	    modifiers.includes("self") ? delete el._x_ignoreSelf : delete el._x_ignore;
	  });
	};
	directive("ignore", handler);

	// packages/alpinejs/src/directives/x-effect.js
	directive("effect", (el, {expression}, {effect: effect3}) => effect3(evaluateLater(el, expression)));

	// packages/alpinejs/src/utils/bind.js
	function bind(el, name, value, modifiers = []) {
	  if (!el._x_bindings)
	    el._x_bindings = reactive({});
	  el._x_bindings[name] = value;
	  name = modifiers.includes("camel") ? camelCase(name) : name;
	  switch (name) {
	    case "value":
	      bindInputValue(el, value);
	      break;
	    case "style":
	      bindStyles(el, value);
	      break;
	    case "class":
	      bindClasses(el, value);
	      break;
	    default:
	      bindAttribute(el, name, value);
	      break;
	  }
	}
	function bindInputValue(el, value) {
	  if (el.type === "radio") {
	    if (el.attributes.value === void 0) {
	      el.value = value;
	    }
	    if (window.fromModel) {
	      el.checked = checkedAttrLooseCompare(el.value, value);
	    }
	  } else if (el.type === "checkbox") {
	    if (Number.isInteger(value)) {
	      el.value = value;
	    } else if (!Number.isInteger(value) && !Array.isArray(value) && typeof value !== "boolean" && ![null, void 0].includes(value)) {
	      el.value = String(value);
	    } else {
	      if (Array.isArray(value)) {
	        el.checked = value.some((val) => checkedAttrLooseCompare(val, el.value));
	      } else {
	        el.checked = !!value;
	      }
	    }
	  } else if (el.tagName === "SELECT") {
	    updateSelect(el, value);
	  } else {
	    if (el.value === value)
	      return;
	    el.value = value;
	  }
	}
	function bindClasses(el, value) {
	  if (el._x_undoAddedClasses)
	    el._x_undoAddedClasses();
	  el._x_undoAddedClasses = setClasses(el, value);
	}
	function bindStyles(el, value) {
	  if (el._x_undoAddedStyles)
	    el._x_undoAddedStyles();
	  el._x_undoAddedStyles = setStyles(el, value);
	}
	function bindAttribute(el, name, value) {
	  if ([null, void 0, false].includes(value) && attributeShouldntBePreservedIfFalsy(name)) {
	    el.removeAttribute(name);
	  } else {
	    if (isBooleanAttr(name))
	      value = name;
	    setIfChanged(el, name, value);
	  }
	}
	function setIfChanged(el, attrName, value) {
	  if (el.getAttribute(attrName) != value) {
	    el.setAttribute(attrName, value);
	  }
	}
	function updateSelect(el, value) {
	  const arrayWrappedValue = [].concat(value).map((value2) => {
	    return value2 + "";
	  });
	  Array.from(el.options).forEach((option) => {
	    option.selected = arrayWrappedValue.includes(option.value);
	  });
	}
	function camelCase(subject) {
	  return subject.toLowerCase().replace(/-(\w)/g, (match, char) => char.toUpperCase());
	}
	function checkedAttrLooseCompare(valueA, valueB) {
	  return valueA == valueB;
	}
	function isBooleanAttr(attrName) {
	  const booleanAttributes = [
	    "disabled",
	    "checked",
	    "required",
	    "readonly",
	    "hidden",
	    "open",
	    "selected",
	    "autofocus",
	    "itemscope",
	    "multiple",
	    "novalidate",
	    "allowfullscreen",
	    "allowpaymentrequest",
	    "formnovalidate",
	    "autoplay",
	    "controls",
	    "loop",
	    "muted",
	    "playsinline",
	    "default",
	    "ismap",
	    "reversed",
	    "async",
	    "defer",
	    "nomodule"
	  ];
	  return booleanAttributes.includes(attrName);
	}
	function attributeShouldntBePreservedIfFalsy(name) {
	  return !["aria-pressed", "aria-checked", "aria-expanded"].includes(name);
	}

	// packages/alpinejs/src/utils/on.js
	function on(el, event, modifiers, callback) {
	  let listenerTarget = el;
	  let handler3 = (e) => callback(e);
	  let options = {};
	  let wrapHandler = (callback2, wrapper) => (e) => wrapper(callback2, e);
	  if (modifiers.includes("dot"))
	    event = dotSyntax(event);
	  if (modifiers.includes("camel"))
	    event = camelCase2(event);
	  if (modifiers.includes("passive"))
	    options.passive = true;
	  if (modifiers.includes("capture"))
	    options.capture = true;
	  if (modifiers.includes("window"))
	    listenerTarget = window;
	  if (modifiers.includes("document"))
	    listenerTarget = document;
	  if (modifiers.includes("prevent"))
	    handler3 = wrapHandler(handler3, (next, e) => {
	      e.preventDefault();
	      next(e);
	    });
	  if (modifiers.includes("stop"))
	    handler3 = wrapHandler(handler3, (next, e) => {
	      e.stopPropagation();
	      next(e);
	    });
	  if (modifiers.includes("self"))
	    handler3 = wrapHandler(handler3, (next, e) => {
	      e.target === el && next(e);
	    });
	  if (modifiers.includes("away") || modifiers.includes("outside")) {
	    listenerTarget = document;
	    handler3 = wrapHandler(handler3, (next, e) => {
	      if (el.contains(e.target))
	        return;
	      if (el.offsetWidth < 1 && el.offsetHeight < 1)
	        return;
	      if (el._x_isShown === false)
	        return;
	      next(e);
	    });
	  }
	  handler3 = wrapHandler(handler3, (next, e) => {
	    if (isKeyEvent(event)) {
	      if (isListeningForASpecificKeyThatHasntBeenPressed(e, modifiers)) {
	        return;
	      }
	    }
	    next(e);
	  });
	  if (modifiers.includes("debounce")) {
	    let nextModifier = modifiers[modifiers.indexOf("debounce") + 1] || "invalid-wait";
	    let wait = isNumeric(nextModifier.split("ms")[0]) ? Number(nextModifier.split("ms")[0]) : 250;
	    handler3 = debounce(handler3, wait);
	  }
	  if (modifiers.includes("throttle")) {
	    let nextModifier = modifiers[modifiers.indexOf("throttle") + 1] || "invalid-wait";
	    let wait = isNumeric(nextModifier.split("ms")[0]) ? Number(nextModifier.split("ms")[0]) : 250;
	    handler3 = throttle(handler3, wait);
	  }
	  if (modifiers.includes("once")) {
	    handler3 = wrapHandler(handler3, (next, e) => {
	      next(e);
	      listenerTarget.removeEventListener(event, handler3, options);
	    });
	  }
	  listenerTarget.addEventListener(event, handler3, options);
	  return () => {
	    listenerTarget.removeEventListener(event, handler3, options);
	  };
	}
	function dotSyntax(subject) {
	  return subject.replace(/-/g, ".");
	}
	function camelCase2(subject) {
	  return subject.toLowerCase().replace(/-(\w)/g, (match, char) => char.toUpperCase());
	}
	function isNumeric(subject) {
	  return !Array.isArray(subject) && !isNaN(subject);
	}
	function kebabCase2(subject) {
	  return subject.replace(/([a-z])([A-Z])/g, "$1-$2").replace(/[_\s]/, "-").toLowerCase();
	}
	function isKeyEvent(event) {
	  return ["keydown", "keyup"].includes(event);
	}
	function isListeningForASpecificKeyThatHasntBeenPressed(e, modifiers) {
	  let keyModifiers = modifiers.filter((i) => {
	    return !["window", "document", "prevent", "stop", "once"].includes(i);
	  });
	  if (keyModifiers.includes("debounce")) {
	    let debounceIndex = keyModifiers.indexOf("debounce");
	    keyModifiers.splice(debounceIndex, isNumeric((keyModifiers[debounceIndex + 1] || "invalid-wait").split("ms")[0]) ? 2 : 1);
	  }
	  if (keyModifiers.length === 0)
	    return false;
	  if (keyModifiers.length === 1 && keyToModifiers(e.key).includes(keyModifiers[0]))
	    return false;
	  const systemKeyModifiers = ["ctrl", "shift", "alt", "meta", "cmd", "super"];
	  const selectedSystemKeyModifiers = systemKeyModifiers.filter((modifier) => keyModifiers.includes(modifier));
	  keyModifiers = keyModifiers.filter((i) => !selectedSystemKeyModifiers.includes(i));
	  if (selectedSystemKeyModifiers.length > 0) {
	    const activelyPressedKeyModifiers = selectedSystemKeyModifiers.filter((modifier) => {
	      if (modifier === "cmd" || modifier === "super")
	        modifier = "meta";
	      return e[`${modifier}Key`];
	    });
	    if (activelyPressedKeyModifiers.length === selectedSystemKeyModifiers.length) {
	      if (keyToModifiers(e.key).includes(keyModifiers[0]))
	        return false;
	    }
	  }
	  return true;
	}
	function keyToModifiers(key) {
	  if (!key)
	    return [];
	  key = kebabCase2(key);
	  let modifierToKeyMap = {
	    ctrl: "control",
	    slash: "/",
	    space: "-",
	    spacebar: "-",
	    cmd: "meta",
	    esc: "escape",
	    up: "arrow-up",
	    down: "arrow-down",
	    left: "arrow-left",
	    right: "arrow-right",
	    period: ".",
	    equal: "="
	  };
	  modifierToKeyMap[key] = key;
	  return Object.keys(modifierToKeyMap).map((modifier) => {
	    if (modifierToKeyMap[modifier] === key)
	      return modifier;
	  }).filter((modifier) => modifier);
	}

	// packages/alpinejs/src/directives/x-model.js
	directive("model", (el, {modifiers, expression}, {effect: effect3, cleanup}) => {
	  let evaluate2 = evaluateLater(el, expression);
	  let assignmentExpression = `${expression} = rightSideOfExpression($event, ${expression})`;
	  let evaluateAssignment = evaluateLater(el, assignmentExpression);
	  var event = el.tagName.toLowerCase() === "select" || ["checkbox", "radio"].includes(el.type) || modifiers.includes("lazy") ? "change" : "input";
	  let assigmentFunction = generateAssignmentFunction(el, modifiers, expression);
	  let removeListener = on(el, event, modifiers, (e) => {
	    evaluateAssignment(() => {
	    }, {scope: {
	      $event: e,
	      rightSideOfExpression: assigmentFunction
	    }});
	  });
	  cleanup(() => removeListener());
	  let evaluateSetModel = evaluateLater(el, `${expression} = __placeholder`);
	  el._x_model = {
	    get() {
	      let result;
	      evaluate2((value) => result = value);
	      return result;
	    },
	    set(value) {
	      evaluateSetModel(() => {
	      }, {scope: {__placeholder: value}});
	    }
	  };
	  el._x_forceModelUpdate = () => {
	    evaluate2((value) => {
	      if (value === void 0 && expression.match(/\./))
	        value = "";
	      window.fromModel = true;
	      mutateDom(() => bind(el, "value", value));
	      delete window.fromModel;
	    });
	  };
	  effect3(() => {
	    if (modifiers.includes("unintrusive") && document.activeElement.isSameNode(el))
	      return;
	    el._x_forceModelUpdate();
	  });
	});
	function generateAssignmentFunction(el, modifiers, expression) {
	  if (el.type === "radio") {
	    mutateDom(() => {
	      if (!el.hasAttribute("name"))
	        el.setAttribute("name", expression);
	    });
	  }
	  return (event, currentValue) => {
	    return mutateDom(() => {
	      if (event instanceof CustomEvent && event.detail !== void 0) {
	        return event.detail || event.target.value;
	      } else if (el.type === "checkbox") {
	        if (Array.isArray(currentValue)) {
	          let newValue = modifiers.includes("number") ? safeParseNumber(event.target.value) : event.target.value;
	          return event.target.checked ? currentValue.concat([newValue]) : currentValue.filter((el2) => !checkedAttrLooseCompare2(el2, newValue));
	        } else {
	          return event.target.checked;
	        }
	      } else if (el.tagName.toLowerCase() === "select" && el.multiple) {
	        return modifiers.includes("number") ? Array.from(event.target.selectedOptions).map((option) => {
	          let rawValue = option.value || option.text;
	          return safeParseNumber(rawValue);
	        }) : Array.from(event.target.selectedOptions).map((option) => {
	          return option.value || option.text;
	        });
	      } else {
	        let rawValue = event.target.value;
	        return modifiers.includes("number") ? safeParseNumber(rawValue) : modifiers.includes("trim") ? rawValue.trim() : rawValue;
	      }
	    });
	  };
	}
	function safeParseNumber(rawValue) {
	  let number = rawValue ? parseFloat(rawValue) : null;
	  return isNumeric2(number) ? number : rawValue;
	}
	function checkedAttrLooseCompare2(valueA, valueB) {
	  return valueA == valueB;
	}
	function isNumeric2(subject) {
	  return !Array.isArray(subject) && !isNaN(subject);
	}

	// packages/alpinejs/src/directives/x-cloak.js
	directive("cloak", (el) => queueMicrotask(() => mutateDom(() => el.removeAttribute(prefix("cloak")))));

	// packages/alpinejs/src/directives/x-init.js
	addInitSelector(() => `[${prefix("init")}]`);
	directive("init", skipDuringClone((el, {expression}) => {
	  if (typeof expression === "string") {
	    return !!expression.trim() && evaluate(el, expression, {});
	  }
	  return evaluate(el, expression, {});
	}));

	// packages/alpinejs/src/directives/x-text.js
	directive("text", (el, {expression}, {effect: effect3, evaluateLater: evaluateLater2}) => {
	  let evaluate2 = evaluateLater2(expression);
	  effect3(() => {
	    evaluate2((value) => {
	      mutateDom(() => {
	        el.textContent = value;
	      });
	    });
	  });
	});

	// packages/alpinejs/src/directives/x-html.js
	directive("html", (el, {expression}, {effect: effect3, evaluateLater: evaluateLater2}) => {
	  let evaluate2 = evaluateLater2(expression);
	  effect3(() => {
	    evaluate2((value) => {
	      el.innerHTML = value;
	    });
	  });
	});

	// packages/alpinejs/src/directives/x-bind.js
	mapAttributes(startingWith(":", into(prefix("bind:"))));
	directive("bind", (el, {value, modifiers, expression, original}, {effect: effect3}) => {
	  if (!value)
	    return applyBindingsObject(el, expression, original, effect3);
	  if (value === "key")
	    return storeKeyForXFor(el, expression);
	  let evaluate2 = evaluateLater(el, expression);
	  effect3(() => evaluate2((result) => {
	    if (result === void 0 && expression.match(/\./))
	      result = "";
	    mutateDom(() => bind(el, value, result, modifiers));
	  }));
	});
	function applyBindingsObject(el, expression, original, effect3) {
	  let getBindings = evaluateLater(el, expression);
	  let cleanupRunners = [];
	  effect3(() => {
	    while (cleanupRunners.length)
	      cleanupRunners.pop()();
	    getBindings((bindings) => {
	      let attributes = Object.entries(bindings).map(([name, value]) => ({name, value}));
	      attributes = attributes.filter((attr) => {
	        return !(typeof attr.value === "object" && !Array.isArray(attr.value) && attr.value !== null);
	      });
	      let staticAttributes = attributesOnly(attributes);
	      attributes = attributes.map((attribute) => {
	        if (staticAttributes.find((attr) => attr.name === attribute.name)) {
	          return {
	            name: `x-bind:${attribute.name}`,
	            value: `"${attribute.value}"`
	          };
	        }
	        return attribute;
	      });
	      directives(el, attributes, original).map((handle) => {
	        cleanupRunners.push(handle.runCleanups);
	        handle();
	      });
	    });
	  });
	}
	function storeKeyForXFor(el, expression) {
	  el._x_keyExpression = expression;
	}

	// packages/alpinejs/src/directives/x-data.js
	addRootSelector(() => `[${prefix("data")}]`);
	directive("data", skipDuringClone((el, {expression}, {cleanup}) => {
	  expression = expression === "" ? "{}" : expression;
	  let magicContext = {};
	  injectMagics(magicContext, el);
	  let dataProviderContext = {};
	  injectDataProviders(dataProviderContext, magicContext);
	  let data2 = evaluate(el, expression, {scope: dataProviderContext});
	  if (data2 === void 0)
	    data2 = {};
	  injectMagics(data2, el);
	  let reactiveData = reactive(data2);
	  initInterceptors(reactiveData);
	  let undo = addScopeToNode(el, reactiveData);
	  reactiveData["init"] && evaluate(el, reactiveData["init"]);
	  cleanup(() => {
	    undo();
	    reactiveData["destroy"] && evaluate(el, reactiveData["destroy"]);
	  });
	}));

	// packages/alpinejs/src/directives/x-show.js
	directive("show", (el, {modifiers, expression}, {effect: effect3}) => {
	  let evaluate2 = evaluateLater(el, expression);
	  let hide = () => mutateDom(() => {
	    el.style.display = "none";
	    el._x_isShown = false;
	  });
	  let show = () => mutateDom(() => {
	    if (el.style.length === 1 && el.style.display === "none") {
	      el.removeAttribute("style");
	    } else {
	      el.style.removeProperty("display");
	    }
	    el._x_isShown = true;
	  });
	  let clickAwayCompatibleShow = () => setTimeout(show);
	  let toggle = once((value) => value ? show() : hide(), (value) => {
	    if (typeof el._x_toggleAndCascadeWithTransitions === "function") {
	      el._x_toggleAndCascadeWithTransitions(el, value, show, hide);
	    } else {
	      value ? clickAwayCompatibleShow() : hide();
	    }
	  });
	  let oldValue;
	  let firstTime = true;
	  effect3(() => evaluate2((value) => {
	    if (!firstTime && value === oldValue)
	      return;
	    if (modifiers.includes("immediate"))
	      value ? clickAwayCompatibleShow() : hide();
	    toggle(value);
	    oldValue = value;
	    firstTime = false;
	  }));
	});

	// packages/alpinejs/src/directives/x-for.js
	directive("for", (el, {expression}, {effect: effect3, cleanup}) => {
	  let iteratorNames = parseForExpression(expression);
	  let evaluateItems = evaluateLater(el, iteratorNames.items);
	  let evaluateKey = evaluateLater(el, el._x_keyExpression || "index");
	  el._x_prevKeys = [];
	  el._x_lookup = {};
	  effect3(() => loop(el, iteratorNames, evaluateItems, evaluateKey));
	  cleanup(() => {
	    Object.values(el._x_lookup).forEach((el2) => el2.remove());
	    delete el._x_prevKeys;
	    delete el._x_lookup;
	  });
	});
	function loop(el, iteratorNames, evaluateItems, evaluateKey) {
	  let isObject = (i) => typeof i === "object" && !Array.isArray(i);
	  let templateEl = el;
	  evaluateItems((items) => {
	    if (isNumeric3(items) && items >= 0) {
	      items = Array.from(Array(items).keys(), (i) => i + 1);
	    }
	    if (items === void 0)
	      items = [];
	    let lookup = el._x_lookup;
	    let prevKeys = el._x_prevKeys;
	    let scopes = [];
	    let keys = [];
	    if (isObject(items)) {
	      items = Object.entries(items).map(([key, value]) => {
	        let scope = getIterationScopeVariables(iteratorNames, value, key, items);
	        evaluateKey((value2) => keys.push(value2), {scope: {index: key, ...scope}});
	        scopes.push(scope);
	      });
	    } else {
	      for (let i = 0; i < items.length; i++) {
	        let scope = getIterationScopeVariables(iteratorNames, items[i], i, items);
	        evaluateKey((value) => keys.push(value), {scope: {index: i, ...scope}});
	        scopes.push(scope);
	      }
	    }
	    let adds = [];
	    let moves = [];
	    let removes = [];
	    let sames = [];
	    for (let i = 0; i < prevKeys.length; i++) {
	      let key = prevKeys[i];
	      if (keys.indexOf(key) === -1)
	        removes.push(key);
	    }
	    prevKeys = prevKeys.filter((key) => !removes.includes(key));
	    let lastKey = "template";
	    for (let i = 0; i < keys.length; i++) {
	      let key = keys[i];
	      let prevIndex = prevKeys.indexOf(key);
	      if (prevIndex === -1) {
	        prevKeys.splice(i, 0, key);
	        adds.push([lastKey, i]);
	      } else if (prevIndex !== i) {
	        let keyInSpot = prevKeys.splice(i, 1)[0];
	        let keyForSpot = prevKeys.splice(prevIndex - 1, 1)[0];
	        prevKeys.splice(i, 0, keyForSpot);
	        prevKeys.splice(prevIndex, 0, keyInSpot);
	        moves.push([keyInSpot, keyForSpot]);
	      } else {
	        sames.push(key);
	      }
	      lastKey = key;
	    }
	    for (let i = 0; i < removes.length; i++) {
	      let key = removes[i];
	      lookup[key].remove();
	      lookup[key] = null;
	      delete lookup[key];
	    }
	    for (let i = 0; i < moves.length; i++) {
	      let [keyInSpot, keyForSpot] = moves[i];
	      let elInSpot = lookup[keyInSpot];
	      let elForSpot = lookup[keyForSpot];
	      let marker = document.createElement("div");
	      mutateDom(() => {
	        elForSpot.after(marker);
	        elInSpot.after(elForSpot);
	        marker.before(elInSpot);
	        marker.remove();
	      });
	      refreshScope(elForSpot, scopes[keys.indexOf(keyForSpot)]);
	    }
	    for (let i = 0; i < adds.length; i++) {
	      let [lastKey2, index] = adds[i];
	      let lastEl = lastKey2 === "template" ? templateEl : lookup[lastKey2];
	      let scope = scopes[index];
	      let key = keys[index];
	      let clone2 = document.importNode(templateEl.content, true).firstElementChild;
	      addScopeToNode(clone2, reactive(scope), templateEl);
	      mutateDom(() => {
	        lastEl.after(clone2);
	        initTree(clone2);
	      });
	      if (typeof key === "object") {
	        warn("x-for key cannot be an object, it must be a string or an integer", templateEl);
	      }
	      lookup[key] = clone2;
	    }
	    for (let i = 0; i < sames.length; i++) {
	      refreshScope(lookup[sames[i]], scopes[keys.indexOf(sames[i])]);
	    }
	    templateEl._x_prevKeys = keys;
	  });
	}
	function parseForExpression(expression) {
	  let forIteratorRE = /,([^,\}\]]*)(?:,([^,\}\]]*))?$/;
	  let stripParensRE = /^\s*\(|\)\s*$/g;
	  let forAliasRE = /([\s\S]*?)\s+(?:in|of)\s+([\s\S]*)/;
	  let inMatch = expression.match(forAliasRE);
	  if (!inMatch)
	    return;
	  let res = {};
	  res.items = inMatch[2].trim();
	  let item = inMatch[1].replace(stripParensRE, "").trim();
	  let iteratorMatch = item.match(forIteratorRE);
	  if (iteratorMatch) {
	    res.item = item.replace(forIteratorRE, "").trim();
	    res.index = iteratorMatch[1].trim();
	    if (iteratorMatch[2]) {
	      res.collection = iteratorMatch[2].trim();
	    }
	  } else {
	    res.item = item;
	  }
	  return res;
	}
	function getIterationScopeVariables(iteratorNames, item, index, items) {
	  let scopeVariables = {};
	  if (/^\[.*\]$/.test(iteratorNames.item) && Array.isArray(item)) {
	    let names = iteratorNames.item.replace("[", "").replace("]", "").split(",").map((i) => i.trim());
	    names.forEach((name, i) => {
	      scopeVariables[name] = item[i];
	    });
	  } else if (/^\{.*\}$/.test(iteratorNames.item) && !Array.isArray(item) && typeof item === "object") {
	    let names = iteratorNames.item.replace("{", "").replace("}", "").split(",").map((i) => i.trim());
	    names.forEach((name) => {
	      scopeVariables[name] = item[name];
	    });
	  } else {
	    scopeVariables[iteratorNames.item] = item;
	  }
	  if (iteratorNames.index)
	    scopeVariables[iteratorNames.index] = index;
	  if (iteratorNames.collection)
	    scopeVariables[iteratorNames.collection] = items;
	  return scopeVariables;
	}
	function isNumeric3(subject) {
	  return !Array.isArray(subject) && !isNaN(subject);
	}

	// packages/alpinejs/src/directives/x-ref.js
	function handler2() {
	}
	handler2.inline = (el, {expression}, {cleanup}) => {
	  let root = closestRoot(el);
	  if (!root._x_refs)
	    root._x_refs = {};
	  root._x_refs[expression] = el;
	  cleanup(() => delete root._x_refs[expression]);
	};
	directive("ref", handler2);

	// packages/alpinejs/src/directives/x-if.js
	directive("if", (el, {expression}, {effect: effect3, cleanup}) => {
	  let evaluate2 = evaluateLater(el, expression);
	  let show = () => {
	    if (el._x_currentIfEl)
	      return el._x_currentIfEl;
	    let clone2 = el.content.cloneNode(true).firstElementChild;
	    addScopeToNode(clone2, {}, el);
	    mutateDom(() => {
	      el.after(clone2);
	      initTree(clone2);
	    });
	    el._x_currentIfEl = clone2;
	    el._x_undoIf = () => {
	      clone2.remove();
	      delete el._x_currentIfEl;
	    };
	    return clone2;
	  };
	  let hide = () => {
	    if (!el._x_undoIf)
	      return;
	    el._x_undoIf();
	    delete el._x_undoIf;
	  };
	  effect3(() => evaluate2((value) => {
	    value ? show() : hide();
	  }));
	  cleanup(() => el._x_undoIf && el._x_undoIf());
	});

	// packages/alpinejs/src/directives/x-on.js
	mapAttributes(startingWith("@", into(prefix("on:"))));
	directive("on", skipDuringClone((el, {value, modifiers, expression}, {cleanup}) => {
	  let evaluate2 = expression ? evaluateLater(el, expression) : () => {
	  };
	  let removeListener = on(el, value, modifiers, (e) => {
	    evaluate2(() => {
	    }, {scope: {$event: e}, params: [e]});
	  });
	  cleanup(() => removeListener());
	}));

	// packages/alpinejs/src/index.js
	alpine_default.setEvaluator(normalEvaluator);
	alpine_default.setReactivityEngine({reactive: import_reactivity9.reactive, effect: import_reactivity9.effect, release: import_reactivity9.stop, raw: import_reactivity9.toRaw});
	var src_default = alpine_default;

	// packages/alpinejs/builds/module.js
	var module_default = src_default;

	var CresReact = /*#__PURE__*/function () {
	  function CresReact() {
	    _classCallCheck(this, CresReact);
	  }

	  _createClass(CresReact, [{
	    key: "createTestComponent",
	    value: function createTestComponent(container) {
	      window.ReactDOM.render( /*#__PURE__*/React.createElement("div", null, "halo"), container);
	    }
	  }]);

	  return CresReact;
	}();

	var cresReact = new CresReact();

	var Connector = /*#__PURE__*/function () {
	  /**
	   * Create a new class instance.
	   */
	  function Connector(options) {
	    _classCallCheck(this, Connector);

	    this.options = null;
	    /**
	     * Default connector options.
	     */

	    this.defaultOptions = {
	      auth: {
	        headers: {}
	      },
	      authEndpoint: window.capp.baseUrl + 'cresenity/broadcast/auth',
	      broadcaster: 'pusher',
	      csrfToken: null,
	      host: null,
	      key: null,
	      namespace: 'App.Events'
	    };
	    this.setOptions(options);
	    this.connect();
	  }
	  /**
	   * Merge the custom options with the defaults.
	   */


	  _createClass(Connector, [{
	    key: "setOptions",
	    value: function setOptions(options) {
	      this.options = Object.assign(this.defaultOptions, options);

	      if (this.csrfToken()) {
	        this.options.auth.headers['X-CSRF-TOKEN'] = this.csrfToken();
	      }

	      return options;
	    }
	    /**
	     * Extract the CSRF token from the page.
	     */

	  }, {
	    key: "csrfToken",
	    value: function csrfToken() {
	      var selector;

	      if (typeof window !== 'undefined' && window.capp && window.capp.csrfToken) {
	        return window.capp.csrfToken;
	      } else if (this.options.csrfToken) {
	        return this.options.csrfToken;
	      } else if (typeof document !== 'undefined' && typeof document.querySelector === 'function' && (selector = document.querySelector('meta[name="csrf-token"]'))) {
	        return selector.getAttribute('content');
	      }

	      return null;
	    }
	  }]);

	  return Connector;
	}();

	/**
	 * This class represents a basic channel.
	 */
	var Channel = /*#__PURE__*/function () {
	  function Channel() {
	    _classCallCheck(this, Channel);

	    this.options = null;
	  }
	  /**
	   * Listen for an event on the channel instance.
	   */


	  _createClass(Channel, [{
	    key: "listen",
	    value: function listen(event, callback) {
	      return this;
	    }
	    /**
	     * Listen for a whisper event on the channel instance.
	     */

	  }, {
	    key: "listenForWhisper",
	    value: function listenForWhisper(event, callback) {
	      return this.listen('.client-' + event, callback);
	    }
	    /**
	     * Listen for an event on the channel instance.
	     */

	  }, {
	    key: "notification",
	    value: function notification(callback) {
	      return this.listen('.CNotification_Event_BroadcastNotificationCreated', callback);
	    }
	    /**
	     * Stop listening to an event on the channel instance.
	     */

	  }, {
	    key: "stopListening",
	    value: function stopListening(event, callback) {
	      return this;
	    }
	    /**
	     * Stop listening for a whisper event on the channel instance.
	     */

	  }, {
	    key: "stopListeningForWhisper",
	    value: function stopListeningForWhisper(event, callback) {
	      return this.stopListening('.client-' + event, callback);
	    }
	    /**
	     * Register a callback to be called anytime a subscription succeeds.
	     */

	  }, {
	    key: "subscribed",
	    value: function subscribed(callback) {
	      return this;
	    }
	    /**
	     * Register a callback to be called anytime an error occurs.
	     */

	  }, {
	    key: "error",
	    value: function error(callback) {
	      return this;
	    }
	  }]);

	  return Channel;
	}();

	/**
	 * Event name formatter
	 */
	var EventFormatter = /*#__PURE__*/function () {
	  /**
	   * Create a new class instance.
	   */
	  function EventFormatter(namespace) {
	    _classCallCheck(this, EventFormatter);

	    this.setNamespace(namespace);
	  }
	  /**
	   * Format the given event name.
	   */


	  _createClass(EventFormatter, [{
	    key: "format",
	    value: function format(event) {
	      if (event.charAt(0) === '.' || event.charAt(0) === '\\') {
	        return event.substr(1);
	      } else if (this.namespace) {
	        event = this.namespace + '.' + event;
	      }

	      return event.replace(/\./g, '\\');
	    }
	    /**
	     * Set the event namespace.
	     */

	  }, {
	    key: "setNamespace",
	    value: function setNamespace(value) {
	      this.namespace = value;
	    }
	  }]);

	  return EventFormatter;
	}();

	/**
	 * This class represents a Pusher channel.
	 */

	var PusherChannel = /*#__PURE__*/function (_Channel) {
	  _inherits(PusherChannel, _Channel);

	  var _super = _createSuper(PusherChannel);

	  /**
	   * Create a new class instance.
	   */
	  function PusherChannel(pusher, name, options) {
	    var _this;

	    _classCallCheck(this, PusherChannel);

	    _this = _super.call(this);
	    _this.name = name;
	    _this.pusher = pusher;
	    _this.options = options;
	    _this.eventFormatter = new EventFormatter(_this.options.namespace);
	    _this.subscription = null;

	    _this.subscribe();

	    return _this;
	  }
	  /**
	   * Subscribe to a Pusher channel.
	   */


	  _createClass(PusherChannel, [{
	    key: "subscribe",
	    value: function subscribe() {
	      this.subscription = this.pusher.subscribe(this.name);
	    }
	    /**
	     * Unsubscribe from a Pusher channel.
	     */

	  }, {
	    key: "unsubscribe",
	    value: function unsubscribe() {
	      this.pusher.unsubscribe(this.name);
	    }
	    /**
	     * Listen for an event on the channel instance.
	     */

	  }, {
	    key: "listen",
	    value: function listen(event, callback) {
	      this.on(this.eventFormatter.format(event), callback);
	      return this;
	    }
	    /**
	     * Listen for all events on the channel instance.
	     */

	  }, {
	    key: "listenToAll",
	    value: function listenToAll(callback) {
	      var _this2 = this;

	      this.subscription.bind_global(function (event, data) {
	        if (event.startsWith('pusher:')) {
	          return;
	        }

	        var namespace = _this2.options.namespace.replace(/\./g, '\\');

	        var formattedEvent = event.startsWith(namespace) ? event.substring(namespace.length + 1) : '.' + event;
	        callback(formattedEvent, data);
	      });
	      return this;
	    }
	    /**
	     * Stop listening for an event on the channel instance.
	     */

	  }, {
	    key: "stopListening",
	    value: function stopListening(event, callback) {
	      if (callback) {
	        this.subscription.unbind(this.eventFormatter.format(event), callback);
	      } else {
	        this.subscription.unbind(this.eventFormatter.format(event));
	      }

	      return this;
	    }
	    /**
	     * Stop listening for all events on the channel instance.
	     */

	  }, {
	    key: "stopListeningToAll",
	    value: function stopListeningToAll(callback) {
	      if (callback) {
	        this.subscription.unbind_global(callback);
	      } else {
	        this.subscription.unbind_global();
	      }

	      return this;
	    }
	    /**
	     * Register a callback to be called anytime a subscription succeeds.
	     */

	  }, {
	    key: "subscribed",
	    value: function subscribed(callback) {
	      this.on('pusher:subscription_succeeded', function () {
	        callback();
	      });
	      return this;
	    }
	    /**
	     * Register a callback to be called anytime a subscription error occurs.
	     */

	  }, {
	    key: "error",
	    value: function error(callback) {
	      this.on('pusher:subscription_error', function (status) {
	        callback(status);
	      });
	      return this;
	    }
	    /**
	     * Bind a channel to an event.
	     */

	  }, {
	    key: "on",
	    value: function on(event, callback) {
	      this.subscription.bind(event, callback);
	      return this;
	    }
	  }]);

	  return PusherChannel;
	}(Channel);

	/**
	 * This class represents a Pusher private channel.
	 */

	var PusherEncryptedPrivateChannel = /*#__PURE__*/function (_PusherChannel) {
	  _inherits(PusherEncryptedPrivateChannel, _PusherChannel);

	  var _super = _createSuper(PusherEncryptedPrivateChannel);

	  function PusherEncryptedPrivateChannel() {
	    _classCallCheck(this, PusherEncryptedPrivateChannel);

	    return _super.apply(this, arguments);
	  }

	  _createClass(PusherEncryptedPrivateChannel, [{
	    key: "whisper",
	    value:
	    /**
	     * Trigger client event on the channel.
	     */
	    function whisper(eventName, data) {
	      this.pusher.channels.channels[this.name].trigger("client-".concat(eventName), data);
	      return this;
	    }
	  }]);

	  return PusherEncryptedPrivateChannel;
	}(PusherChannel);

	/**
	 * This class represents a Pusher presence channel.
	 */

	var PusherPresenceChannel = /*#__PURE__*/function (_PusherChannel) {
	  _inherits(PusherPresenceChannel, _PusherChannel);

	  var _super = _createSuper(PusherPresenceChannel);

	  function PusherPresenceChannel() {
	    _classCallCheck(this, PusherPresenceChannel);

	    return _super.apply(this, arguments);
	  }

	  _createClass(PusherPresenceChannel, [{
	    key: "here",
	    value:
	    /**
	     * Register a callback to be called anytime the member list changes.
	     */
	    function here(callback) {
	      this.on('pusher:subscription_succeeded', function (data) {
	        callback(Object.keys(data.members).map(function (k) {
	          return data.members[k];
	        }));
	      });
	      return this;
	    }
	    /**
	     * Listen for someone joining the channel.
	     */

	  }, {
	    key: "joining",
	    value: function joining(callback) {
	      this.on('pusher:member_added', function (member) {
	        callback(member.info);
	      });
	      return this;
	    }
	    /**
	     * Listen for someone leaving the channel.
	     */

	  }, {
	    key: "leaving",
	    value: function leaving(callback) {
	      this.on('pusher:member_removed', function (member) {
	        callback(member.info);
	      });
	      return this;
	    }
	    /**
	     * Trigger client event on the channel.
	     */

	  }, {
	    key: "whisper",
	    value: function whisper(eventName, data) {
	      this.pusher.channels.channels[this.name].trigger("client-".concat(eventName), data);
	      return this;
	    }
	  }]);

	  return PusherPresenceChannel;
	}(PusherChannel);

	/**
	 * This class represents a Pusher private channel.
	 */

	var PusherPrivateChannel = /*#__PURE__*/function (_PusherChannel) {
	  _inherits(PusherPrivateChannel, _PusherChannel);

	  var _super = _createSuper(PusherPrivateChannel);

	  function PusherPrivateChannel() {
	    _classCallCheck(this, PusherPrivateChannel);

	    return _super.apply(this, arguments);
	  }

	  _createClass(PusherPrivateChannel, [{
	    key: "whisper",
	    value:
	    /**
	     * Trigger client event on the channel.
	     */
	    function whisper(eventName, data) {
	      this.pusher.channels.channels[this.name].trigger("client-".concat(eventName), data);
	      return this;
	    }
	  }]);

	  return PusherPrivateChannel;
	}(PusherChannel);

	/**
	 * This class represents a null channel.
	 */

	var NullChannel = /*#__PURE__*/function (_Channel) {
	  _inherits(NullChannel, _Channel);

	  var _super = _createSuper(NullChannel);

	  function NullChannel() {
	    _classCallCheck(this, NullChannel);

	    return _super.apply(this, arguments);
	  }

	  _createClass(NullChannel, [{
	    key: "subscribe",
	    value:
	    /**
	     * Subscribe to a channel.
	     */
	    function subscribe() {//
	    }
	    /**
	     * Unsubscribe from a channel.
	     */

	  }, {
	    key: "unsubscribe",
	    value: function unsubscribe() {//
	    }
	    /**
	     * Listen for an event on the channel instance.
	     */

	  }, {
	    key: "listen",
	    value: function listen(event, callback) {
	      return this;
	    }
	    /**
	     * Stop listening for an event on the channel instance.
	     */

	  }, {
	    key: "stopListening",
	    value: function stopListening(event, callback) {
	      return this;
	    }
	    /**
	     * Register a callback to be called anytime a subscription succeeds.
	     */

	  }, {
	    key: "subscribed",
	    value: function subscribed(callback) {
	      return this;
	    }
	    /**
	     * Register a callback to be called anytime an error occurs.
	     */

	  }, {
	    key: "error",
	    value: function error(callback) {
	      return this;
	    }
	    /**
	     * Bind a channel to an event.
	     */

	  }, {
	    key: "on",
	    value: function on(event, callback) {
	      return this;
	    }
	  }]);

	  return NullChannel;
	}(Channel);

	/**
	 * This class represents a null presence channel.
	 */

	var NullPresenceChannel = /*#__PURE__*/function (_NullChannel) {
	  _inherits(NullPresenceChannel, _NullChannel);

	  var _super = _createSuper(NullPresenceChannel);

	  function NullPresenceChannel() {
	    _classCallCheck(this, NullPresenceChannel);

	    return _super.apply(this, arguments);
	  }

	  _createClass(NullPresenceChannel, [{
	    key: "here",
	    value:
	    /**
	     * Register a callback to be called anytime the member list changes.
	     */
	    function here(callback) {
	      return this;
	    }
	    /**
	     * Listen for someone joining the channel.
	     */

	  }, {
	    key: "joining",
	    value: function joining(callback) {
	      return this;
	    }
	    /**
	     * Listen for someone leaving the channel.
	     */

	  }, {
	    key: "leaving",
	    value: function leaving(callback) {
	      return this;
	    }
	    /**
	     * Trigger client event on the channel.
	     */

	  }, {
	    key: "whisper",
	    value: function whisper(eventName, data) {
	      return this;
	    }
	  }]);

	  return NullPresenceChannel;
	}(NullChannel);

	/**
	 * This class represents a null private channel.
	 */

	var NullPrivateChannel = /*#__PURE__*/function (_NullChannel) {
	  _inherits(NullPrivateChannel, _NullChannel);

	  var _super = _createSuper(NullPrivateChannel);

	  function NullPrivateChannel() {
	    _classCallCheck(this, NullPrivateChannel);

	    return _super.apply(this, arguments);
	  }

	  _createClass(NullPrivateChannel, [{
	    key: "whisper",
	    value:
	    /**
	     * Trigger client event on the channel.
	     */
	    function whisper(eventName, data) {
	      return this;
	    }
	  }]);

	  return NullPrivateChannel;
	}(NullChannel);

	/* global Pusher */

	/**
	 * This class creates a connector to Pusher.
	 */

	var PusherConnector = /*#__PURE__*/function (_Connector) {
	  _inherits(PusherConnector, _Connector);

	  var _super = _createSuper(PusherConnector);

	  function PusherConnector(options) {
	    _classCallCheck(this, PusherConnector);

	    return _super.call(this, options);
	  }
	  /**
	   * Create a fresh Pusher connection.
	   */


	  _createClass(PusherConnector, [{
	    key: "connect",
	    value: function connect() {
	      this.channels = {};

	      if (typeof this.options.client !== 'undefined') {
	        this.pusher = this.options.client;
	      } else {
	        if (typeof Pusher === 'undefined') {
	          throw new Error('Pusher is not defined');
	        }

	        this.pusher = new Pusher(this.options.key, this.options);
	      }
	    }
	    /**
	     * Listen for an event on a channel instance.
	     */

	  }, {
	    key: "listen",
	    value: function listen(name, event, callback) {
	      return this.channel(name).listen(event, callback);
	    }
	    /**
	     * Get a channel instance by name.
	     */

	  }, {
	    key: "channel",
	    value: function channel(name) {
	      if (!this.channels[name]) {
	        this.channels[name] = new PusherChannel(this.pusher, name, this.options);
	      }

	      return this.channels[name];
	    }
	    /**
	     * Get a private channel instance by name.
	     */

	  }, {
	    key: "privateChannel",
	    value: function privateChannel(name) {
	      if (!this.channels['private-' + name]) {
	        this.channels['private-' + name] = new PusherPrivateChannel(this.pusher, 'private-' + name, this.options);
	      }

	      return this.channels['private-' + name];
	    }
	    /**
	     * Get a private encrypted channel instance by name.
	     */

	  }, {
	    key: "encryptedPrivateChannel",
	    value: function encryptedPrivateChannel(name) {
	      if (!this.channels['private-encrypted-' + name]) {
	        this.channels['private-encrypted-' + name] = new PusherEncryptedPrivateChannel(this.pusher, 'private-encrypted-' + name, this.options);
	      }

	      return this.channels['private-encrypted-' + name];
	    }
	    /**
	     * Get a presence channel instance by name.
	     */

	  }, {
	    key: "presenceChannel",
	    value: function presenceChannel(name) {
	      if (!this.channels['presence-' + name]) {
	        this.channels['presence-' + name] = new PusherPresenceChannel(this.pusher, 'presence-' + name, this.options);
	      }

	      return this.channels['presence-' + name];
	    }
	    /**
	     * Leave the given channel, as well as its private and presence variants.
	     */

	  }, {
	    key: "leave",
	    value: function leave(name) {
	      var _this = this;

	      var channels = [name, 'private-' + name, 'presence-' + name];
	      channels.forEach(function (name, index) {
	        _this.leaveChannel(name);
	      });
	    }
	    /**
	     * Leave the given channel.
	     */

	  }, {
	    key: "leaveChannel",
	    value: function leaveChannel(name) {
	      if (this.channels[name]) {
	        this.channels[name].unsubscribe();
	        delete this.channels[name];
	      }
	    }
	    /**
	     * Get the socket ID for the connection.
	     */

	  }, {
	    key: "socketId",
	    value: function socketId() {
	      if (this.pusher && this.pusher.connection) {
	        return this.pusher.connection.socket_id;
	      }

	      return null;
	    }
	    /**
	     * Disconnect Pusher connection.
	     */

	  }, {
	    key: "disconnect",
	    value: function disconnect() {
	      this.pusher.disconnect();
	    }
	  }]);

	  return PusherConnector;
	}(Connector);

	/**
	 * This class creates a null connector.
	 */

	var NullConnector = /*#__PURE__*/function (_Connector) {
	  _inherits(NullConnector, _Connector);

	  var _super = _createSuper(NullConnector);

	  function NullConnector(options) {
	    var _this;

	    _classCallCheck(this, NullConnector);

	    _this = _super.call(this, options);
	    _this.channels = {};
	    return _this;
	  }
	  /**
	   * Create a fresh connection.
	   */


	  _createClass(NullConnector, [{
	    key: "connect",
	    value: function connect() {//
	    }
	    /**
	     * Listen for an event on a channel instance.
	     */

	  }, {
	    key: "listen",
	    value: function listen(name, event, callback) {
	      return new NullChannel();
	    }
	    /**
	     * Get a channel instance by name.
	     */

	  }, {
	    key: "channel",
	    value: function channel(name) {
	      return new NullChannel();
	    }
	    /**
	     * Get a private channel instance by name.
	     */

	  }, {
	    key: "privateChannel",
	    value: function privateChannel(name) {
	      return new NullPrivateChannel();
	    }
	    /**
	     * Get a presence channel instance by name.
	     */

	  }, {
	    key: "presenceChannel",
	    value: function presenceChannel(name) {
	      return new NullPresenceChannel();
	    }
	    /**
	     * Leave the given channel, as well as its private and presence variants.
	     */

	  }, {
	    key: "leave",
	    value: function leave(name) {//
	    }
	    /**
	     * Leave the given channel.
	     */

	  }, {
	    key: "leaveChannel",
	    value: function leaveChannel(name) {//
	    }
	    /**
	     * Get the socket ID for the connection.
	     */

	  }, {
	    key: "socketId",
	    value: function socketId() {
	      return 'fake-socket-id';
	    }
	    /**
	     * Disconnect the connection.
	     */

	  }, {
	    key: "disconnect",
	    value: function disconnect() {//
	    }
	  }]);

	  return NullConnector;
	}(Connector);

	/* global Vue,axios */

	/**
	 * This class is the primary API for interacting with broadcasting.
	 */

	var CSocket = /*#__PURE__*/function () {
	  /**
	   * Create a new class instance.
	   */
	  function CSocket(options) {
	    _classCallCheck(this, CSocket);

	    this.connector = null;
	    this.options = options;
	    this.connect();

	    if (!this.options.withoutInterceptors) {
	      this.registerInterceptors();
	    }
	  }
	  /**
	   * Get a channel instance by name.
	   */


	  _createClass(CSocket, [{
	    key: "channel",
	    value: function channel(_channel) {
	      return this.connector.channel(_channel);
	    }
	    /**
	     * Create a new connection.
	     */

	  }, {
	    key: "connect",
	    value: function connect() {
	      if (this.options.broadcaster == 'pusher') {
	        this.connector = new PusherConnector(this.options);
	      } else if (this.options.broadcaster == 'null') {
	        this.connector = new NullConnector(this.options);
	      } else if (typeof this.options.broadcaster == 'function') {
	        this.connector = new this.options.broadcaster(this.options);
	      }
	    }
	    /**
	     * Disconnect from the Echo server.
	     */

	  }, {
	    key: "disconnect",
	    value: function disconnect() {
	      this.connector.disconnect();
	    }
	    /**
	     * Get a presence channel instance by name.
	     */

	  }, {
	    key: "join",
	    value: function join(channel) {
	      return this.connector.presenceChannel(channel);
	    }
	    /**
	     * Leave the given channel, as well as its private and presence variants.
	     */

	  }, {
	    key: "leave",
	    value: function leave(channel) {
	      this.connector.leave(channel);
	    }
	    /**
	     * Leave the given channel.
	     */

	  }, {
	    key: "leaveChannel",
	    value: function leaveChannel(channel) {
	      this.connector.leaveChannel(channel);
	    }
	    /**
	     * Listen for an event on a channel instance.
	     */

	  }, {
	    key: "listen",
	    value: function listen(channel, event, callback) {
	      return this.connector.listen(channel, event, callback);
	    }
	    /**
	     * Get a private channel instance by name.
	     */

	  }, {
	    key: "private",
	    value: function _private(channel) {
	      return this.connector.privateChannel(channel);
	    }
	    /**
	     * Get a private encrypted channel instance by name.
	     */

	  }, {
	    key: "encryptedPrivate",
	    value: function encryptedPrivate(channel) {
	      return this.connector.encryptedPrivateChannel(channel);
	    }
	    /**
	     * Get the Socket ID for the connection.
	     */

	  }, {
	    key: "socketId",
	    value: function socketId() {
	      return this.connector.socketId();
	    }
	    /**
	     * Register 3rd party request interceptiors. These are used to automatically
	     * send a connections socket id to a Laravel app with a X-Socket-Id header.
	     */

	  }, {
	    key: "registerInterceptors",
	    value: function registerInterceptors() {
	      if (typeof Vue === 'function' && Vue.http) {
	        this.registerVueRequestInterceptor();
	      }

	      if (typeof axios === 'function') {
	        this.registerAxiosRequestInterceptor();
	      }

	      if (typeof jQuery === 'function') {
	        this.registerjQueryAjaxSetup();
	      }
	    }
	    /**
	     * Register a Vue HTTP interceptor to add the X-Socket-ID header.
	     */

	  }, {
	    key: "registerVueRequestInterceptor",
	    value: function registerVueRequestInterceptor() {
	      var _this = this;

	      Vue.http.interceptors.push(function (request, next) {
	        if (_this.socketId()) {
	          request.headers.set('X-Socket-ID', _this.socketId());
	        }

	        next();
	      });
	    }
	    /**
	     * Register an Axios HTTP interceptor to add the X-Socket-ID header.
	     */

	  }, {
	    key: "registerAxiosRequestInterceptor",
	    value: function registerAxiosRequestInterceptor() {
	      var _this2 = this;

	      axios.interceptors.request.use(function (config) {
	        if (_this2.socketId()) {
	          config.headers['X-Socket-Id'] = _this2.socketId();
	        }

	        return config;
	      });
	    }
	    /**
	     * Register jQuery AjaxPrefilter to add the X-Socket-ID header.
	     */

	  }, {
	    key: "registerjQueryAjaxSetup",
	    value: function registerjQueryAjaxSetup() {
	      var _this3 = this;

	      if (typeof jQuery.ajax != 'undefined') {
	        jQuery.ajaxPrefilter(function (options, originalOptions, xhr) {
	          if (_this3.socketId()) {
	            xhr.setRequestHeader('X-Socket-Id', _this3.socketId());
	          }
	        });
	      }
	    }
	  }]);

	  return CSocket;
	}();

	var initProgressive = function initProgressive() {
	  // browser supported?
	  var body = document.body;

	  if (!body.getElementsByClassName || !body.querySelector || !body.classList || !body.getBoundingClientRect) {
	    return;
	  }

	  var classReplace = 'replace',
	      classPreview = 'preview',
	      classReveal = 'reveal',
	      pItem = document.getElementsByClassName('cres-progressive ' + classReplace),
	      rAF = window.requestAnimationFrame || function (f) {
	    f();
	  },
	      timer; // bind events


	  ['pageshow', 'scroll', 'resize'].forEach(function (h) {
	    window.addEventListener(h, throttle, {
	      passive: true
	    });
	  }); // DOM mutation observer

	  if (window.MutationObserver) {
	    var observer = new MutationObserver(throttle);
	    observer.observe(body, {
	      subtree: true,
	      childList: true,
	      attributes: true
	    });
	  } // initial check


	  inView(); // throttle events, no more than once every 300ms

	  function throttle() {
	    timer = timer || setTimeout(function () {
	      timer = null;
	      inView();
	    }, 300);
	  } // image in view?


	  function inView() {
	    if (pItem.length) {
	      rAF(function () {
	        var wH = window.innerHeight,
	            cRect,
	            cT,
	            cH,
	            p = 0;

	        while (p < pItem.length) {
	          cRect = pItem[p].getBoundingClientRect();
	          cT = cRect.top;
	          cH = cRect.height;

	          if (cT + cH > 0 && wH > cT) {
	            loadFullImage(pItem[p]);
	          } else {
	            p++;
	          }
	        }
	      });
	    }
	  } // replace with full image


	  function loadFullImage(item, retry) {
	    // cancel monitoring
	    item.classList.remove(classReplace); // fetch href and preview image

	    var href = item.getAttribute('data-href') || item.href,
	        pImg = item.querySelector('img.' + classPreview);

	    if (!href || !pImg) {
	      return;
	    } // load main image


	    var img = new Image(),
	        ds = item.dataset;

	    if (ds) {
	      if (ds.srcset) {
	        img.srcset = ds.srcset;
	      }

	      if (ds.sizes) {
	        img.sizes = ds.sizes;
	      }
	    }

	    img.onload = addImg; // load failure retry

	    retry = 1 + (retry || 0);

	    if (retry < 3) {
	      img.onerror = function () {
	        setTimeout(function () {
	          loadFullImage(item, retry);
	        }, retry * 3000);
	      };
	    }

	    img.src = href; // replace image

	    function addImg() {
	      // disable link
	      if (href === item.href) {
	        item.style.cursor = 'default';
	        item.addEventListener('click', function (e) {
	          e.preventDefault();
	        });
	      } // apply image attributes


	      var imgClass = img.classList;
	      img.className = pImg.className;
	      imgClass.remove(classPreview);
	      imgClass.add(classReveal);
	      img.alt = pImg.alt || '';
	      img.onload = 0;
	      img.onerror = 0;
	      rAF(function () {
	        // add full image
	        item.insertBefore(img, pImg.nextSibling).addEventListener('animationend', function () {
	          // remove preview image
	          item.removeChild(pImg);
	          imgClass.remove(classReveal);
	        });
	      });
	    }
	  }
	};

	/**
	 * Merge the DEFAULT_SETTINGS with the user defined options if specified
	 * @param {Object} options The user defined options
	 */
	function mergeOptions(initialOptions, customOptions) {
	  var merged = customOptions;

	  for (var prop in initialOptions) {
	    if (merged.hasOwnProperty(prop)) {
	      if (initialOptions[prop] !== null && initialOptions[prop].constructor === Object) {
	        merged[prop] = mergeOptions(initialOptions[prop], merged[prop]);
	      }
	    } else {
	      merged[prop] = initialOptions[prop];
	    }
	  }

	  return merged;
	}
	/**
	 * Stylize the Toast.
	 * @param {Element} element The HTML element to stylize
	 * @param {Object}  styles  An object containing the style to apply
	 */


	function stylize(element, styles) {
	  Object.keys(styles).forEach(function (style) {
	    element.style[style] = styles[style];
	  });
	}

	var cresToast = function () {
	  /**
	  * The Toast animation speed; how long the Toast takes to move to and from the screen
	  * @type {number}
	  */
	  var TOAST_ANIMATION_SPEED = 400;
	  var Transitions = {
	    SHOW: {
	      '-webkit-transition': 'opacity ' + TOAST_ANIMATION_SPEED + 'ms, -webkit-transform ' + TOAST_ANIMATION_SPEED + 'ms',
	      transition: 'opacity ' + TOAST_ANIMATION_SPEED + 'ms, transform ' + TOAST_ANIMATION_SPEED + 'ms',
	      opacity: '1',
	      '-webkit-transform': 'translateY(-100%) translateZ(0)',
	      transform: 'translateY(-100%) translateZ(0)'
	    },
	    HIDE: {
	      opacity: '0',
	      '-webkit-transform': 'translateY(150%) translateZ(0)',
	      transform: 'translateY(150%) translateZ(0)'
	    }
	  };
	  /**
	  * The default Toast settings
	  * @type {Object}
	  */

	  var DEFAULT_SETTINGS = {
	    style: {
	      main: {
	        background: 'rgba(0, 0, 0, .85)',
	        'box-shadow': '0 0 10px rgba(0, 0, 0, .8)',
	        'border-radius': '3px',
	        'z-index': '99999',
	        color: 'rgba(255, 255, 255, .9)',
	        'font-family': 'sans-serif',
	        padding: '10px 15px',
	        'max-width': '60%',
	        width: '100%',
	        'word-break': 'keep-all',
	        margin: '0 auto',
	        'text-align': 'center',
	        position: 'fixed',
	        left: '0',
	        right: '0',
	        bottom: '0',
	        '-webkit-transform': 'translateY(150%) translateZ(0)',
	        transform: 'translateY(150%) translateZ(0)',
	        '-webkit-filter': 'blur(0)',
	        opacity: '0'
	      }
	    },
	    settings: {
	      duration: 4000
	    }
	  };
	  /**
	  * The queue of Toasts waiting to be shown
	  * @type {Array}
	  */

	  var toastQueue = [];
	  /**
	  * The toastStage. This is the HTML element in which the toast resides
	  * Getter and setter methods are available privately
	  * @type {HTMLElement}
	  */

	  var toastStage;
	  /**
	  * The Timeout object for animations.
	  * This should be shared among the Toasts, because timeouts may be cancelled e.g. on explicit call of hide()
	  * @type {Object}
	  */

	  var timeout;
	  /**
	  * The main Toast object
	  * @param {string} text The text to put inside the Toast
	  * @param {Object} options Optional; the Toast options. See Toast.prototype.DEFAULT_SETTINGS for more information
	  * @param {Object} transitions Optional; the Transitions object. This should not be used unless you know what you're doing
	  * @param {Object} __internalDefaultSettings For internal use only. Used for Snackbar.
	  */

	  function toast(text, options, transitions) {
	    var toastTransitions = transitions || Transitions;

	    if (getToastStage() !== undefined) {
	      // If there is already a Toast being shown, put this Toast in the queue to show later
	      toastQueue.push({
	        text: text,
	        options: options,
	        transitions: toastTransitions
	      });
	    } else {
	      var toastOptions = options || {};
	      toastOptions = mergeOptions(DEFAULT_SETTINGS, toastOptions);
	      showToast(text, toastOptions, toastTransitions);
	    }

	    return {
	      hide: function hide() {
	        return hideToast(toastTransitions);
	      }
	    };
	  }
	  /**
	  * Show the Toast
	  * @param {string} text The text to show inside the Toast
	  * @param {Object} options The object containing the options for the Toast
	  */


	  function showToast(text, options, transitions) {
	    generateToast(text, options.style.main);
	    var toastStage = getToastStage();
	    document.body.insertBefore(toastStage, document.body.firstChild); // This is a hack to get animations started. Apparently without explicitly redrawing, it'll just attach the class and no animations would be done.

	    toastStage.offsetHeight;
	    stylize(toastStage, transitions.SHOW); // Hide the Toast after the specified time

	    clearTimeout(timeout);

	    if (options.settings.duration !== 0) {
	      timeout = setTimeout(function () {
	        return hideToast(transitions);
	      }, options.settings.duration);
	    }
	  }
	  /**
	  * Hide the Toast that's currently shown.
	  */


	  function hideToast(transitions) {
	    var toastStage = getToastStage();
	    stylize(toastStage, transitions.HIDE); // Destroy the Toast element after animations end.

	    clearTimeout(timeout);
	    toastStage.addEventListener('transitionend', destroyToast, {
	      once: true
	    });
	  }
	  /**
	  * Generate the Toast with the specified text.
	  * @param {string|HTMLElement} text The text to show inside the Toast, can be an HTML element or plain text
	  * @param {Object} style The style to set for the Toast
	  */


	  function generateToast(text, style) {
	    var toastStage = document.createElement('div'); // If the text is a String, create a textNode for appending.

	    if (typeof text === 'string') {
	      text = document.createTextNode(text);
	    }

	    toastStage.appendChild(text);
	    setToastStage(toastStage);
	    stylize(getToastStage(), style);
	  }
	  /**
	  * Clean up after the Toast slides away. Namely, removing the Toast from the DOM.
	  * After the Toast is cleaned up, display the next Toast in the queue if any exists.
	  */


	  function destroyToast() {
	    var toastStage = getToastStage();
	    document.body.removeChild(toastStage);
	    setToastStage(undefined);

	    if (toastQueue.length > 0) {
	      // Show the rest of the Toasts in the queue if they exist.
	      var newToast = toastQueue.shift();
	      toast(newToast.text, newToast.options, newToast.transitions);
	    }
	  }

	  function getToastStage() {
	    return toastStage;
	  }

	  function setToastStage(newToastStage) {
	    toastStage = newToastStage;
	  }

	  return {
	    toast: toast
	  };
	}();

	function getComponentName(element) {
	  return element.getAttribute('x-title') || element.getAttribute('x-id') || element.id || element.getAttribute('name') || findCresID(element.getAttribute('cres:id')) || findLiveViewName(element) || element.getAttribute('aria-label') || extractFunctionName(element.getAttribute('x-data')) || element.getAttribute('role') || element.tagName.toLowerCase();
	}

	function findCresID(cresId) {
	  if (cresId && window.cresenity.ui) {
	    try {
	      var cres = window.cresenity.ui.find(cresId); // eslint-disable-next-line no-underscore-dangle

	      if (window.cresenity.ui.__instance) {
	        // eslint-disable-next-line no-underscore-dangle
	        return 'cres:' + window.cresenity.ui.__instance.fingerprint.name;
	      }
	    } catch (e) {//do nothing
	    }
	  }
	}

	function findLiveViewName(alpineEl) {
	  var phxEl = alpineEl.closest('[data-phx-view]');

	  if (phxEl) {
	    // pretty sure we could do the following instead
	    // return phxEl.dataset.phxView;
	    if (!window.liveSocket.getViewByEl) {
	      return;
	    }

	    var view = window.liveSocket.getViewByEl(phxEl);
	    return view && view.name;
	  }
	}

	function extractFunctionName(functionName) {
	  if (functionName.startsWith('{')) {
	    return;
	  }

	  return functionName.replace(/\(([^\)]+)\)/, '') // Handles myFunction(param)
	  .replace('()', '');
	}
	/**
	 * Semver version check
	 *
	 * @param {string} required
	 * @param {string} actual
	 * @returns {boolean}
	 */


	function isRequiredVersion(required, actual) {
	  if (required === actual) {
	    return true;
	  }

	  var requiredArray = required.split('.').map(function (v) {
	    return parseInt(v, 10);
	  });
	  var currentArray = actual.split('.').map(function (v) {
	    return parseInt(v, 10);
	  });

	  for (var i = 0; i < requiredArray.length; i++) {
	    if (currentArray[i] < requiredArray[i]) {
	      return false;
	    }

	    if (currentArray[i] > requiredArray[i]) {
	      return true;
	    }
	  }

	  return true;
	}

	var CRESALPINE_RENDER_ATTR_NAME = 'data-cresalpine-render';
	var CRESALPINE_RENDER_BINDING_ATTR_NAME = ":".concat(CRESALPINE_RENDER_ATTR_NAME);

	var CresAlpine = /*#__PURE__*/function () {
	  function CresAlpine(Alpine) {
	    _classCallCheck(this, CresAlpine);

	    this.Alpine = Alpine;
	    this.lastComponentCrawl = Date.now();
	    this.components = [];
	    this.uuid = 1;
	    this.errorElements = [];
	    this.observer = null;
	  }

	  _createClass(CresAlpine, [{
	    key: "alpineVersion",
	    get: function get() {
	      return this.Alpine.version || '';
	    }
	  }, {
	    key: "isV3",
	    get: function get() {
	      return isRequiredVersion('3.0.0', this.alpineVersion);
	    }
	  }, {
	    key: "getAlpineDataInstance",
	    value: function getAlpineDataInstance(node) {
	      if (this.isV3) {
	        // eslint-disable-next-line no-underscore-dangle
	        return node._x_dataStack ? node._x_dataStack[0] : null;
	      } // eslint-disable-next-line no-underscore-dangle


	      return node.__x;
	    }
	  }, {
	    key: "getComponent",
	    value: function getComponent(name) {
	      var components = this.getComponents();

	      for (var i = 0; i < components.length; i++) {
	        if (components[i].name == name) {
	          return components[i];
	        }
	      }

	      return null;
	    }
	  }, {
	    key: "getComponents",
	    value: function getComponents() {
	      var _this = this;

	      var alpineRoots = Array.from(document.querySelectorAll('[x-data]'));
	      var allComponentsInitialized = Object.values(alpineRoots).every(function (e) {
	        return e.cresAlpine;
	      });

	      if (allComponentsInitialized) {
	        var lastAlpineRender = alpineRoots.reduce(function (acc, el) {
	          // we add `:data-devtools-render="Date.now()"` when initialising components
	          var renderTimeStr = el.getAttribute(CRESALPINE_RENDER_ATTR_NAME);
	          var renderTime = parseInt(renderTimeStr, 10);

	          if (renderTime && renderTime > acc) {
	            return renderTime;
	          }

	          return acc;
	        }, this.lastComponentCrawl);
	        var someComponentHasUpdated = lastAlpineRender > this.lastComponentCrawl;

	        if (someComponentHasUpdated) {
	          this.lastComponentCrawl = Date.now();
	        } // Exit early if no components have been added, removed and no data has changed


	        if (!someComponentHasUpdated && this.components.length === alpineRoots.length) {
	          return this.components;
	        }
	      }

	      this.components = [];
	      alpineRoots.forEach(function (rootEl, index) {
	        if (!_this.getAlpineDataInstance(rootEl)) {
	          // this component probably crashed during init
	          return;
	        }

	        if (!rootEl.cresAlpine) {
	          if (!_this.isV3) {
	            // only necessary for Alpine v2
	            // add an attr to trigger the mutation observer and run this function
	            // that will send updated state to devtools
	            rootEl.setAttribute(CRESALPINE_RENDER_BINDING_ATTR_NAME, 'Date.now()');
	          }

	          rootEl.cresAlpine = {
	            id: _this.uuid++
	          };
	          window["$x".concat(rootEl.cresAlpine.id - 1)] = _this.getAlpineDataInstance(rootEl);
	        }

	        if (rootEl.cresAlpine.id === _this.selectedComponentId) {
	          _this.sendComponentData(_this.selectedComponentId, rootEl);
	        }

	        if (_this.isV3) {
	          var componentData = _this.getAlpineDataInstance(rootEl);

	          _this.Alpine.effect(function () {
	            Object.keys(componentData).forEach(function (key) {
	              // since effects track which dependencies are accessed,
	              // run a fake component data access so that the effect runs
	              componentData[key];

	              if (rootEl.cresAlpine.id === _this.selectedComponentId) {
	                // this re-computes the whole component data
	                // with effect we could send only the key-value of the field that's changed
	                _this.sendComponentData(_this.selectedComponentId, rootEl);
	              }
	            });
	          });
	        }

	        var componentDepth = index === 0 ? 0 : alpineRoots.reduce(function (depth, el, innerIndex) {
	          if (index === innerIndex) {
	            return depth;
	          }

	          if (el.contains(rootEl)) {
	            return depth + 1;
	          }

	          return depth;
	        }, 0);

	        _this.components.push({
	          name: getComponentName(rootEl),
	          depth: componentDepth,
	          index: index,
	          id: rootEl.cresAlpine.id,
	          getData: function getData() {
	            return _this.getAlpineDataInstance(rootEl);
	          }
	        });
	      });
	      return this.components;
	    }
	  }, {
	    key: "postMessage",
	    value: function postMessage(payload) {
	      window.postMessage({
	        source: 'cres-alpine-backend',
	        payload: payload
	      }, '*');
	    }
	  }]);

	  return CresAlpine;
	}();

	var Cresenity = /*#__PURE__*/function () {
	  function Cresenity() {
	    _classCallCheck(this, Cresenity);

	    this.cf = cf;
	    this.base64 = {
	      encode: encode,
	      decode: decode
	    };
	    this.windowEventList = ['cresenity:confirm', 'cresenity:jquery:loaded', 'cresenity:loaded', 'cresenity:ui:start'];
	    this.modalElements = [];
	    this.cresenityEventList = [];
	    this.url = new Url();
	    this.scrollToTop = new ScrollToTop();
	    this.callback = {};
	    this.filesAdded = [];
	    this.ui = new UI();
	    this.php = php;
	    this.react = cresReact;
	    this.observer = {
	      elementRendered: elementRendered,
	      elementReady: elementReady
	    };
	    this.confirmHandler = defaultConfirmHandler;
	    this.dispatchWindowEvent = dispatch$1;
	    this.websocket = null;
	    this.debounce = debounce$1;
	  }

	  _createClass(Cresenity, [{
	    key: "loadJs",
	    value: function loadJs(filename, callback) {
	      var _this = this;

	      var fileref = document.createElement('script');
	      fileref.setAttribute('type', 'text/javascript');
	      fileref.setAttribute('src', filename); // IE 6 & 7

	      if (typeof callback === 'function') {
	        fileref.onload = callback;

	        fileref.onreadystatechange = function () {
	          if (_this.readyState === 'complete') {
	            callback();
	          }
	        };
	      }

	      document.getElementsByTagName('head')[0].appendChild(fileref);
	    }
	  }, {
	    key: "createWebSocket",
	    value: function createWebSocket(options) {
	      return new CSocket(options);
	    }
	  }, {
	    key: "haveCallback",
	    value: function haveCallback(name) {
	      return typeof this.callback[name] === 'function';
	    }
	  }, {
	    key: "doCallback",
	    value: function doCallback(name) {
	      if (this.haveCallback(name)) {
	        var _this$callback;

	        for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
	          args[_key - 1] = arguments[_key];
	        }

	        (_this$callback = this.callback)[name].apply(_this$callback, args);
	      }
	    }
	  }, {
	    key: "setConfirmHandler",
	    value: function setConfirmHandler(cb) {
	      this.confirmHandler = cb;
	      return this;
	    }
	  }, {
	    key: "setCallback",
	    value: function setCallback(name, cb) {
	      this.callback[name] = cb;
	      return this;
	    }
	  }, {
	    key: "isUsingRequireJs",
	    value: function isUsingRequireJs() {
	      return typeof this.cf.getConfig().requireJs !== 'undefined' ? this.cf.getConfig().requireJs : true;
	    }
	  }, {
	    key: "normalizeRequireJs",
	    value: function normalizeRequireJs() {
	      if (!this.isUsingRequireJs()) {
	        if (typeof define === 'function') {
	          window.define = undefined;
	        }
	      }
	    }
	  }, {
	    key: "isJson",
	    value: function isJson(text) {
	      if (typeof text === 'string') {
	        return /^[\],:{}\s]*$/.test(text.replace(/\\["\\\/bfnrtu]/g, '@').replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').replace(/(?:^|:|,)(?:\s*\[)+/g, ''));
	      }

	      return false;
	    }
	  }, {
	    key: "on",
	    value: function on(eventName, cb) {}
	  }, {
	    key: "handleResponse",
	    value: function handleResponse(data, callback) {
	      if (data.cssRequire && data.cssRequire.length > 0) {
	        for (var i = 0; i < data.cssRequire.length; i++) {
	          this.cf.require(data.cssRequire[i], 'css');
	        }
	      }

	      if (data.css_require && data.css_require.length > 0) {
	        for (var _i = 0; _i < data.css_require.length; _i++) {
	          this.cf.require(data.css_require[_i], 'css');
	        }
	      }

	      callback();
	    }
	  }, {
	    key: "htmlModal",
	    value: function htmlModal(html) {
	      showHtmlModal(html);
	    }
	  }, {
	    key: "urlModal",
	    value: function urlModal(url) {
	      showUrlModal(url);
	    }
	  }, {
	    key: "handleAjaxError",
	    value: function handleAjaxError(xhr, status, error) {
	      if (error !== 'abort') {
	        this.message('error', 'Error, please call administrator... (' + error + ')');

	        if (xhr.status != 200) {
	          if (window.capp && window.capp.environment && window.capp.environment !== 'production') {
	            this.htmlModal(xhr.responseText);
	          }
	        }
	      }
	    }
	  }, {
	    key: "reload",
	    value: function reload(options) {
	      var _this2 = this;

	      var targetOptions = {};

	      if (options && options.selector) {
	        var target = $(options.selector);

	        if (target.attr('data-url')) {
	          targetOptions.url = target.attr('data-url');
	        }

	        if (target.attr('data-method')) {
	          targetOptions.method = target.attr('data-method');
	        }

	        if (target.attr('data-block-html')) {
	          targetOptions.blockHtml = target.attr('data-block-html');
	        }

	        if (target.attr('data-block-type')) {
	          targetOptions.blockType = target.attr('data-block-type');
	        }

	        if (target.attr('data-data-addition')) {
	          targetOptions.dataAddition = JSON.parse(target.attr('data-data-addition'));
	        }
	      }

	      var settings = $.extend({
	        // These are the defaults.
	        method: 'get',
	        dataAddition: {},
	        url: '/',
	        reloadType: 'reload',
	        onComplete: false,
	        onSuccess: false,
	        onBlock: false,
	        blockHtml: false,
	        blockType: 'default',
	        onUnblock: false
	      }, targetOptions, options);
	      var method = settings.method;
	      var selector = settings.selector;
	      ({
	        blockType: settings.blockType
	      });

	      if (settings.blockHtml) ;

	      var xhr = jQuery(selector).data('xhr');

	      if (xhr) {
	        xhr.abort();
	      }

	      var dataAddition = settings.dataAddition;
	      var url = settings.url;

	      if (url) {
	        url = this.url.replaceParam(url);
	      }

	      if (typeof dataAddition === 'undefined') {
	        dataAddition = {};
	      }

	      $(selector).each(function (index, element) {
	        var idTarget = $(element).attr('id');
	        url = _this2.url.addQueryString(url, 'capp_current_container_id', idTarget);

	        if (typeof settings.onBlock === 'function') {
	          settings.onBlock($(element));
	        } else {
	          _this2.blockElement($(element));
	        }

	        $(element).data('xhr', $.ajax({
	          type: method,
	          url: url,
	          dataType: 'json',
	          data: dataAddition,
	          success: function success(data) {
	            var isError = false;

	            if (typeof data.html === 'undefined') {
	              //error
	              _this2.htmlModal(data);

	              isError = true;
	            }

	            if (!isError) {
	              _this2.doCallback('onReloadSuccess', data);

	              _this2.handleResponse(data, function () {
	                switch (settings.reloadType) {
	                  case 'after':
	                    $(element).after(data.html);
	                    break;

	                  case 'before':
	                    $(element).before(data.html);
	                    break;

	                  case 'append':
	                    $(element).append(data.html);
	                    break;

	                  case 'prepend':
	                    $(element).prepend(data.html);
	                    break;

	                  default:
	                    $(element).html(data.html);
	                    break;
	                }

	                if (data.js && data.js.length > 0) {
	                  var script = _this2.base64.decode(data.js);

	                  eval(script);
	                }

	                if ($(element).find('.prettyprint').length > 0) {
	                  if (window.prettyPrint) {
	                    window.prettyPrint();
	                  }
	                }

	                if (typeof settings.onSuccess === 'function') {
	                  settings.onSuccess(data);
	                }
	              });
	            }
	          },
	          error: function error(errorXhr, ajaxOptions, thrownError) {
	            _this2.handleAjaxError(errorXhr, ajaxOptions, thrownError);
	          },
	          complete: function complete() {
	            $(element).data('xhr', false);

	            if (typeof settings.onBlock === 'function') {
	              settings.onUnblock($(element));
	            } else {
	              _this2.unblockElement($(element));
	            }

	            if (typeof settings.onComplete === 'function') {
	              settings.onComplete();
	            }
	          }
	        }));
	      });
	    }
	  }, {
	    key: "append",
	    value: function append(options) {
	      options.reloadType = 'append';
	      this.reload(options);
	    }
	  }, {
	    key: "prepend",
	    value: function prepend(options) {
	      options.reloadType = 'prepend';
	      this.reload(options);
	    }
	  }, {
	    key: "after",
	    value: function after(options) {
	      options.reloadType = 'after';
	      this.reload(options);
	    }
	  }, {
	    key: "before",
	    value: function before(options) {
	      options.reloadType = 'before';
	      this.reload(options);
	    }
	  }, {
	    key: "confirm",
	    value: function confirm(options) {
	      var settings = $.extend({
	        // These are the defaults.
	        method: 'get',
	        dataAddition: {},
	        message: 'Are you sure?',
	        onConfirmed: false,
	        confirmCallback: false,
	        owner: null
	      }, options);
	      var confirmCallback = settings.confirmCallback ? settings.confirmCallback : settings.onConfirmed;

	      if (this.confirmHandler) {
	        return this.confirmHandler(settings.owner, settings, confirmCallback);
	      }

	      if (window.bootbox) {
	        return window.bootbox.confirm(settings.message, confirmCallback);
	      }
	    }
	  }, {
	    key: "modal",
	    value: function modal(options) {
	      var _this3 = this;

	      var settings = $.extend({
	        // These are the defaults.
	        haveHeader: false,
	        haveFooter: false,
	        headerText: '',
	        backdrop: 'static',
	        modalClass: false,
	        onClose: false,
	        appendTo: false,
	        footerAction: {}
	      }, options);

	      if (settings.title) {
	        settings.haveHeader = true;
	        settings.headerText = settings.title;
	      }

	      var modalContainer = jQuery('<div>').addClass('modal');

	      if (settings.modalClass) {
	        modalContainer.addClass(settings.modalClass);
	      }

	      if (settings.isSidebar) {
	        modalContainer.addClass('sidebar');
	        modalContainer.addClass(settings.sidebarMode);
	      }

	      if (settings.isFull) {
	        modalContainer.addClass('sidebar full');
	      }

	      var modalDialog = jQuery('<div>').addClass('modal-dialog modal-xl');
	      var modalContent = jQuery('<div>').addClass('modal-content');
	      var modalHeader = jQuery('<div>').addClass('modal-header');
	      var modalTitle = jQuery('<div>').addClass('modal-title');
	      var modalButtonClose = jQuery('<button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
	      modalButtonClose.click(function () {
	        modalButtonClose.closest('.modal').modal('hide');
	      });
	      var modalBody = jQuery('<div>').addClass('modal-body');
	      var modalFooter = jQuery('<div>').addClass('modal-footer');
	      modalDialog.append(modalContent);
	      modalContainer.append(modalDialog);

	      if (settings.haveHeader) {
	        modalTitle.html(settings.headerText);
	        modalHeader.append(modalTitle).append(modalButtonClose);
	        modalContent.append(modalHeader);
	      }

	      modalDialog.append(modalContent);

	      if (settings.haveFooter) {
	        modalContent.append(modalFooter);
	      }

	      modalContent.append(modalBody);
	      var appendTo = settings.appendTo;

	      if (typeof appendTo === 'undefined' || !appendTo) {
	        appendTo = $('body');
	      }

	      modalContainer.appendTo(appendTo);
	      modalContainer.addClass('capp-modal');
	      modalContainer.on('hidden.bs.modal', function (e) {
	        if (_this3.modalElements.length > 0) {
	          var lastModal = _this3.modalElements[_this3.modalElements.length - 1];

	          if (lastModal && lastModal.get(0) === $(e.target).get(0)) {
	            var Next = function Next() {
	              var _this4 = this;

	              this.isRunning = false;

	              this.callback = function (delay) {
	                var delayMs = delay;

	                if (typeof delayMs === 'undefined') {
	                  delayMs = 0;
	                }

	                if (isNaN(parseInt(delayMs, 10))) {
	                  delayMs = 0;
	                }

	                setTimeout(function () {
	                  $(lastModal).remove();

	                  _this4.modalElements.pop();

	                  var modalExists = $('.modal:visible').length > 0;

	                  if (!modalExists) {
	                    $('body').removeClass('modal-open');
	                  } else if (!$('body').hasClass('modal-open')) {
	                    $('body').addClass('modal-open');
	                  }
	                }, delayMs);
	                _this4.isRunning = true;
	              };
	            };

	            var next = new Next();

	            if (typeof settings.onClose === 'function') {
	              settings.onClose(e, next.callback);
	            }

	            if (!next.isRunning) {
	              next.callback();
	            }
	          }
	        }
	      });
	      modalContainer.on('shown.bs.modal', function () {
	        _this3.modalElements.push(modalContainer);
	      });

	      if (settings.message) {
	        modalBody.append(settings.message);
	      }

	      if (settings.reload) {
	        var reloadOptions = settings.reload;
	        reloadOptions.selector = modalBody;
	        this.reload(reloadOptions);
	      }

	      modalContainer.modal({
	        backdrop: settings.backdrop
	      });
	      return modalContainer;
	    }
	  }, {
	    key: "closeLastModal",
	    value: function closeLastModal() {
	      if (this.modalElements.length > 0) {
	        var lastModal = this.modalElements[this.modalElements.length - 1];
	        lastModal.modal('hide');
	      }
	    }
	  }, {
	    key: "closeDialog",
	    value: function closeDialog() {
	      this.closeLastModal();
	    }
	  }, {
	    key: "ajax",
	    value: function ajax(options) {
	      var _this5 = this;

	      var settings = $.extend({
	        block: true,
	        url: window.location.href,
	        method: 'post'
	      }, options);
	      var dataAddition = settings.dataAddition;
	      var url = settings.url;
	      url = this.url.replaceParam(url);

	      if (typeof dataAddition === 'undefined') {
	        dataAddition = {};
	      }

	      if (settings.block) {
	        this.blockPage();
	      }

	      var validationIsValid = true;
	      var ajaxOptions = {
	        url: url,
	        dataType: 'json',
	        data: dataAddition,
	        type: settings.method,
	        success: function success(response) {
	          var onSuccess = function onSuccess() {};

	          var onError = function onError(errMessage) {
	            _this5.showError(errMessage);
	          };

	          if (typeof settings.onSuccess === 'function' && validationIsValid) {
	            onSuccess = settings.onSuccess;
	          }

	          if (typeof settings.onError === 'function' && validationIsValid) {
	            onError = settings.onError;
	          }

	          {
	            if (settings.handleJsonResponse === true) {
	              _this5.handleJsonResponse(response, onSuccess, onError);
	            } else {
	              onSuccess(response);
	            }
	          }
	        },
	        error: function error(xhr, errorAjaxOptions, thrownError) {
	          if (thrownError !== 'abort') {
	            _this5.showError(thrownError);
	          }
	        },
	        complete: function complete() {
	          if (settings.block) {
	            _this5.unblockPage();
	          }

	          if (typeof settings.onComplete === 'function' && validationIsValid) {
	            settings.onComplete();
	          }
	        }
	      };
	      return $.ajax(ajaxOptions);
	    }
	  }, {
	    key: "ajaxSubmit",
	    value: function ajaxSubmit(options) {
	      var _this6 = this;

	      var settings = $.extend({}, options);
	      var selector = settings.selector;
	      $(selector).each(function (index, element) {
	        //don't do it again if still loading
	        var formAjaxUrl = $(element).attr('action') || '';
	        var formMethod = $(element).attr('method') || 'get';

	        _this6.blockElement($(element));

	        var validationIsValid = true;
	        var ajaxOptions = {
	          url: formAjaxUrl,
	          dataType: 'json',
	          type: formMethod,
	          beforeSubmit: function beforeSubmit() {
	            if (typeof $(element).validate === 'function') {
	              validationIsValid = $(element).validate().form();
	              return validationIsValid;
	            }

	            return true;
	          },
	          success: function success(response) {
	            var onSuccess = function onSuccess() {};

	            var onError = function onError(errMessage) {
	              _this6.showError(errMessage);
	            };

	            var haveOnSuccess = false;

	            if (typeof settings.onSuccess === 'function' && validationIsValid) {
	              onSuccess = settings.onSuccess;
	              haveOnSuccess = true;
	            }

	            if (typeof settings.onError === 'function' && validationIsValid) {
	              onError = settings.onError;
	            }

	            if (validationIsValid) {
	              if (settings.handleJsonResponse === true && haveOnSuccess) {
	                _this6.handleJsonResponse(response, onSuccess, onError);
	              } else {
	                onSuccess(response);
	              }
	            }
	          },
	          complete: function complete() {
	            _this6.unblockElement($(element));

	            if (typeof settings.onComplete === 'function' && validationIsValid) {
	              settings.onComplete();
	            }
	          }
	        };
	        $(element).ajaxSubmit(ajaxOptions);
	      }); //always return false to prevent submit

	      return false;
	    }
	  }, {
	    key: "debug",
	    value: function debug(message) {
	      if (this.cf.getConfig().debug) {
	        window.console.log(message);
	      }
	    }
	  }, {
	    key: "toast",
	    value: function toast(type, message, options) {
	      var settings = $.extend({
	        title: ucfirst(type),
	        position: 'top-right'
	      }, options);

	      if (window.toastr) {
	        return window.toastr[type](message, settings.title, {
	          positionClass: 'toast-' + settings.position,
	          closeButton: true,
	          progressBar: true,
	          preventDuplicates: false,
	          newestOnTop: false
	        });
	      }

	      return cresToast.toast(message);
	    }
	  }, {
	    key: "message",
	    value: function message(type, _message, alertType, callback) {
	      alertType = typeof alertType !== 'undefined' ? alertType : 'notify';
	      var container = $('#container');

	      if (container.length === 0) {
	        container = $('body');
	      }

	      if (alertType === 'bootbox' && window.bootbox) {
	        if (typeof callback === 'undefined') {
	          return window.bootbox.alert(_message);
	        }

	        return window.bootbox.alert(_message, callback);
	      }

	      if (alertType === 'notify') {
	        var obj = $('<div>');
	        container.prepend(obj);
	        obj.addClass('notifications');
	        obj.addClass('top-right');

	        if (typeof obj.notify !== 'undefined') {
	          return obj.notify({
	            message: {
	              text: _message
	            },
	            type: type
	          }).show();
	        }
	      }

	      return this.toast(type, _message);
	    }
	  }, {
	    key: "scrollTo",
	    value: function scrollTo(element, container) {
	      if (typeof container === 'undefined') {
	        container = document.body;
	      }

	      $(container).animate({
	        scrollTop: $(element).offset().top - ($(container).offset().top + $(container).scrollTop())
	      });
	    }
	  }, {
	    key: "replaceAll",
	    value: function replaceAll(string, find, replace) {
	      var escapedFind = find.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, '\\$1');
	      return string.replace(new RegExp(escapedFind, 'g'), replace);
	    }
	  }, {
	    key: "formatCurrency",
	    value: function formatCurrency(rp) {
	      rp = '' + rp;
	      var rupiah = '';
	      var vfloat = '';
	      var ds = window.capp.decimal_separator;
	      var ts = window.capp.thousand_separator;
	      var dd = window.capp.decimal_digit;
	      dd = parseInt(dd, 10);
	      var minusStr = '';

	      if (rp.indexOf('-') >= 0) {
	        minusStr = rp.substring(rp.indexOf('-'), 1);
	        rp = rp.substring(rp.indexOf('-') + 1);
	      }

	      if (rp.indexOf('.') >= 0) {
	        vfloat = rp.substring(rp.indexOf('.'));
	        rp = rp.substring(0, rp.indexOf('.'));
	      }

	      var p = rp.length;

	      while (p > 3) {
	        rupiah = ts + rp.substring(p - 3) + rupiah;
	        var l = rp.length - 3;
	        rp = rp.substring(0, l);
	        p = rp.length;
	      }

	      rupiah = rp + rupiah;
	      vfloat = vfloat.replace('.', ds);

	      if (vfloat.length > dd) {
	        vfloat = vfloat.substring(0, dd + 1);
	      }

	      return minusStr + rupiah + vfloat;
	    }
	  }, {
	    key: "unformatCurrency",
	    value: function unformatCurrency(rp) {
	      if (typeof rp == 'undefined') {
	        rp = '';
	      }

	      var ds = window.capp.decimal_separator;
	      var ts = window.capp.thousand_separator;
	      var last3 = rp.substr(rp.length - 3);
	      var char_last3 = last3.charAt(0);

	      if (char_last3 != ts) {
	        rp = this.replaceAll(rp, ts, '');
	      }

	      rp = rp.replace(ds, '.');
	      return rp;
	    }
	  }, {
	    key: "getStyles",
	    value: function getStyles(selector, only, except) {
	      // the map to return with requested styles and values as KVP
	      var product = {}; // the style object from the DOM element we need to iterate through

	      var style; // recycle the name of the style attribute

	      var name;
	      var element = $(selector); // if it's a limited list, no need to run through the entire style object

	      if (only && only instanceof Array) {
	        for (var i = 0, l = only.length; i < l; i++) {
	          // since we have the name already, just return via built-in .css method
	          name = only[i];
	          product[name] = element.css(name);
	        }
	      } else if (element.length) {
	        // otherwise, we need to get everything
	        var dom = element.get(0); // standards

	        if (window.getComputedStyle) {
	          // convenience methods to turn css case ('background-image') to camel ('backgroundImage')
	          var pattern = /\-([a-z])/g;

	          var uc = function uc(a, b) {
	            return b.toUpperCase();
	          };

	          var camelize = function camelize(string) {
	            return string.replace(pattern, uc);
	          }; // make sure we're getting a good reference


	          if (style = window.getComputedStyle(dom, null)) {
	            var camel;
	            var value; // opera doesn't give back style.length - use truthy since a 0 length may as well be skipped anyways

	            if (style.length) {
	              for (var _i2 = 0, _l = style.length; _i2 < _l; _i2++) {
	                name = style[_i2];
	                camel = camelize(name);
	                value = style.getPropertyValue(name);
	                product[camel] = value;
	              }
	            } else {
	              // opera
	              for (name in style) {
	                camel = camelize(name);
	                value = style.getPropertyValue(name) || style[name];
	                product[camel] = value;
	              }
	            }
	          }
	        } else if (style = dom.currentStyle) {
	          // IE - first try currentStyle, then normal style object - don't bother with runtimeStyle
	          for (name in style) {
	            product[name] = style[name];
	          }
	        } else if (style = dom.style) {
	          for (name in style) {
	            if (typeof style[name] !== 'function') {
	              product[name] = style[name];
	            }
	          }
	        }
	      } // remove any styles specified...
	      // be careful on blacklist - sometimes vendor-specific values aren't obvious but will be visible...  e.g., excepting 'color' will still let '-webkit-text-fill-color' through, which will in fact color the text


	      if (except && except instanceof Array) {
	        for (var _i3 = 0, _l2 = except.length; _i3 < _l2; _i3++) {
	          name = except[_i3];
	          delete product[name];
	        }
	      } // one way out so we can process blacklist in one spot


	      return product;
	    }
	  }, {
	    key: "createPlaceholderElement",
	    value: function createPlaceholderElement(selector, root, depth) {
	      var _this7 = this;

	      depth = parseInt(depth, 10);

	      if (!Number.isInteger(depth)) {
	        depth = 0;
	      }

	      var element = $(selector);

	      if (element.length === 0) {
	        return null;
	      }

	      root = root || element;
	      var newElement = element.clone().empty();
	      newElement.removeAttr('id');
	      newElement.removeAttr('data-block-html');
	      newElement.removeClass();

	      if (!element.is(':visible')) {
	        return null;
	      }

	      var styles = this.getStyles(element);

	      if (depth > 0) {
	        //newElement.addClass('remove-after');
	        //newElement.addClass('remove-before');
	        if (element.children(':visible:not(:empty)').length === 0) {
	          var relativeY = element.offset().top - root.offset().top;
	          var relativeX = element.offset().left - root.offset().left;
	          styles.width = '' + element.outerWidth() + 'px';
	          styles.height = '' + (element.outerHeight() - 8) + 'px';
	          styles.position = 'absolute';
	          styles.top = '' + (relativeY + 4) + 'px';
	          styles.left = '' + relativeX + 'px';
	          styles.backgroundColor = '#ced4da';
	        }
	      }

	      styles.border = '0';
	      styles.borderRadius = '0';
	      styles.overflow = 'hidden';

	      switch (element.prop('tagName').toLowerCase()) {
	        case 'ul':
	          styles.padding = '0px';
	          break;

	        case 'li':
	          styles.listStyle = 'none';
	          break;
	      }

	      if (depth === 0) {
	        styles.position = 'relative';
	      }

	      newElement.css(styles);

	      if (depth === 0) {
	        newElement.addClass('capp-ph-item');
	        newElement.attr('style', function (i, s) {
	          return (s || '') + 'margin: 0 !important;';
	        });
	      }

	      element.children().each(function (idx, item) {
	        var newChild = _this7.createPlaceholderElement(item, root, depth + 1);

	        if (newChild) {
	          newElement.append(newChild);
	        }
	      });
	      return newElement;
	    }
	  }, {
	    key: "blockPage",
	    value: function blockPage(options) {
	      var settings = $.extend({
	        innerMessage: '<div class="sk-folding-cube sk-primary"><div class="sk-cube1 sk-cube"></div><div class="sk-cube2 sk-cube"></div><div class="sk-cube4 sk-cube"></div><div class="sk-cube3 sk-cube"></div></div><h5 style="color: #444">LOADING...</h5>'
	      }, options);
	      $.blockUI({
	        message: settings.innerMessage,
	        css: {
	          backgroundColor: 'transparent',
	          border: '0',
	          zIndex: 9999999
	        },
	        overlayCSS: {
	          backgroundColor: '#fff',
	          opacity: 0.8,
	          zIndex: 9999990
	        }
	      });
	    }
	  }, {
	    key: "unblockPage",
	    value: function unblockPage() {
	      $.unblockUI();
	    }
	  }, {
	    key: "blockElement",
	    value: function blockElement(selector, options) {
	      var settings = $.extend({
	        innerMessage: '<div class="sk-wave sk-primary"><div class="sk-rect sk-rect1"></div> <div class="sk-rect sk-rect2"></div> <div class="sk-rect sk-rect3"></div> <div class="sk-rect sk-rect4"></div> <div class="sk-rect sk-rect5"></div></div>'
	      }, options);
	      $(selector).block({
	        message: settings.innerMessage,
	        css: {
	          backgroundColor: 'transparent',
	          border: '0'
	        },
	        overlayCSS: {
	          backgroundColor: '#fff',
	          opacity: 0.8
	        }
	      });
	    }
	  }, {
	    key: "unblockElement",
	    value: function unblockElement(selector) {
	      $(selector).unblock();
	    }
	  }, {
	    key: "value",
	    value: function value(elm) {
	      elm = $(elm);

	      if (elm.length === 0) {
	        return null;
	      }

	      if (elm.attr('type') === 'checkbox') {
	        if (!elm.is(':checked')) {
	          return null;
	        }
	      }

	      if (elm.attr('type') === 'radio') {
	        if (!elm.is(':checked')) {
	          return null;
	        }
	      }

	      if (typeof elm.val() !== 'undefined') {
	        return elm.val();
	      }

	      if (typeof elm.attr('value') !== 'undefined') {
	        return elm.attr('value');
	      }

	      return elm.html();
	    }
	  }, {
	    key: "initConfirm",
	    value: function initConfirm() {
	      var _this8 = this;

	      elementRendered('a.confirm, button.confirm, input[type=submit].confirm', function (el) {
	        $(el).click(function (e) {
	          e.preventDefault();
	          e.stopPropagation();
	          confirmFromElement(el, _this8.confirmHandler);
	          return false;
	        });
	      });
	      jQuery(document).ready(function () {
	        jQuery('#toggle-subnavbar').click(function () {
	          var cmd = jQuery('#toggle-subnavbar span').html();

	          if (cmd === 'Hide') {
	            jQuery('#subnavbar').slideUp('slow');
	            jQuery('#toggle-subnavbar span').html('Show');
	          } else {
	            jQuery('#subnavbar').slideDown('slow');
	            jQuery('#toggle-subnavbar span').html('Hide');
	          }
	        });
	        jQuery('#toggle-fullscreen').click(function () {
	          toggleFullscreen(document.documentElement);
	        });
	      });
	    }
	  }, {
	    key: "initReload",
	    value: function initReload() {
	      var _this9 = this;

	      var reloadInitialized = $('body').attr('data-reload-initialized');

	      if (!reloadInitialized) {
	        $('.capp-reload').each(function (idx, item) {
	          if (!$(item).hasClass('capp-reloaded')) {
	            var reloadOptions = {};
	            reloadOptions.selector = $(item);

	            _this9.reload(reloadOptions);

	            $(item).addClass('capp-reloaded');
	          }
	        });
	        $('body').attr('data-reload-initialized', '1');
	      }
	    }
	  }, {
	    key: "initValidation",
	    value: function initValidation$1() {
	      if ($ && $.validator) {
	        initValidation();
	      }
	    }
	  }, {
	    key: "initAlpineAndUi",
	    value: function initAlpineAndUi() {
	      window.Alpine = module_default;
	      this.ui.start();
	      window.Alpine.start();
	      this.alpine = new CresAlpine(window.Alpine);
	    }
	  }, {
	    key: "initLiveReload",
	    value: function initLiveReload() {
	      if (!this.cf.isProduction() && this.cf.config.vscode.liveReload.enable) {
	        try {
	          var rsocket = new WebSocket(this.cf.config.vscode.liveReload.protocol + '://' + this.cf.config.vscode.liveReload.host + ':' + this.cf.config.vscode.liveReload.port + '/', 'reload-protocol');

	          rsocket.onmessage = function (msg) {
	            if (msg.data == 'RELOAD') {
	              location.reload();
	            }
	          };
	        } catch (e) {//do nothing
	        }
	      }
	    }
	  }, {
	    key: "init",
	    value: function init() {
	      var _this10 = this;

	      this.cf.onBeforeInit(function () {
	        _this10.normalizeRequireJs();
	      });
	      this.cf.onAfterInit(function () {
	        if (_this10.cf.getConfig().haveScrollToTop) {
	          if (!document.getElementById('cres-topcontrol')) {
	            _this10.scrollToTop.init();
	          }
	        }

	        _this10.initConfirm();

	        _this10.initReload();

	        _this10.initValidation();

	        _this10.initAlpineAndUi();

	        _this10.initLiveReload();

	        initProgressive();
	        var root = document.getElementsByTagName('html')[0]; // '0' to assign the first (and only `HTML` tag)

	        root.classList.add('cresenity-loaded');
	        root.classList.remove('no-js');
	        dispatch$1('cresenity:loaded');
	      });
	      this.cf.init();
	    }
	  }, {
	    key: "downloadProgress",
	    value: function downloadProgress(options) {
	      var _this11 = this;

	      var settings = $.extend({
	        // These are the defaults.
	        method: 'get',
	        dataAddition: {},
	        url: '/',
	        onComplete: false,
	        onSuccess: false,
	        onBlock: false,
	        onUnblock: false
	      }, options);
	      var method = settings.method;
	      var xhr = jQuery(window).data('cappXhrProgress');

	      if (xhr) {
	        xhr.abort();
	      }

	      var dataAddition = settings.dataAddition;
	      var url = settings.url;
	      url = this.url.replaceParam(url);

	      if (typeof dataAddition === 'undefined') {
	        dataAddition = {};
	      }

	      if (typeof settings.onBlock === 'function') {
	        settings.onBlock();
	      } else {
	        this.blockPage();
	      }

	      $.ajax({
	        type: method,
	        url: url,
	        dataType: 'json',
	        data: dataAddition,
	        success: function success(response) {
	          _this11.handleJsonResponse(response, function (data) {
	            var progressUrl = data.progressUrl;
	            var progressContainer = $('<div>').addClass('progress-container');
	            var interval = setInterval(function () {
	              $.ajax({
	                type: method,
	                url: progressUrl,
	                dataType: 'json',
	                success: function success(responseProgress) {
	                  _this11.handleJsonResponse(responseProgress, function (dataProgress) {
	                    if (data.state === 'DONE') {
	                      progressContainer.find('.progress-container-status').empty();

	                      var _innerStatus = $('<div>');

	                      var _innerStatusLabel = $('<label>', {
	                        class: 'mb-3 d-block'
	                      }).append('Your file is ready');

	                      var linkDownload = $('<a>', {
	                        target: '_blank',
	                        href: dataProgress.fileUrl,
	                        class: 'btn btn-primary'
	                      }).append('Download');
	                      var linkClose = $('<a>', {
	                        href: 'javascript:;',
	                        class: 'btn btn-primary ml-3'
	                      }).append('Close');

	                      _innerStatus.append(_innerStatusLabel);

	                      _innerStatus.append(linkDownload);

	                      _innerStatus.append(linkClose);

	                      progressContainer.find('.progress-container-status').append(_innerStatus);
	                      linkClose.click(function () {
	                        _this11.closeLastModal();
	                      });
	                      clearInterval(interval);
	                    }
	                  });
	                }
	              });
	            }, 3000);
	            var innerStatus = $('<div>');
	            var innerStatusLabel = $('<label>', {
	              class: 'mb-4'
	            }).append('Please Wait...');
	            var innerStatusAnimation = $('<div>').append('<div class="sk-fading-circle sk-primary"><div class="sk-circle1 sk-circle"></div><div class="sk-circle2 sk-circle"></div><div class="sk-circle3 sk-circle"></div><div class="sk-circle4 sk-circle"></div><div class="sk-circle5 sk-circle"></div><div class="sk-circle6 sk-circle"></div><div class="sk-circle7 sk-circle"></div><div class="sk-circle8 sk-circle"></div><div class="sk-circle9 sk-circle"></div><div class="sk-circle10 sk-circle"></div><div class="sk-circle11 sk-circle"></div><div class="sk-circle12 sk-circle"></div></div>');
	            var innerStatusAction = $('<div>', {
	              class: 'text-center my-3'
	            });
	            var innerStatusCancelButton = $('<button>', {
	              class: 'btn btn-primary'
	            }).append('Cancel');
	            innerStatusAction.append(innerStatusCancelButton);
	            innerStatus.append(innerStatusLabel);
	            innerStatus.append(innerStatusAnimation);
	            innerStatus.append(innerStatusAction);
	            progressContainer.append($('<div>').addClass('progress-container-status').append(innerStatus));
	            innerStatusCancelButton.click(function () {
	              clearInterval(interval);

	              _this11.closeLastModal();
	            });

	            _this11.modal({
	              message: progressContainer,
	              modalClass: 'modal-download-progress'
	            });
	          });
	        },
	        error: function error(xhrError, ajaxOptions, thrownError) {
	          if (thrownError !== 'abort') {
	            _this11.message('error', 'Error, please call administrator... (' + thrownError + ')');
	          }
	        },
	        complete: function complete() {
	          if (typeof settings.onBlock === 'function') {
	            settings.onUnblock();
	          } else {
	            _this11.unblockPage();
	          }

	          if (typeof settings.onComplete === 'function') {
	            settings.onComplete();
	          }
	        }
	      });
	    }
	  }, {
	    key: "reactive",
	    value: function reactive(data, cb) {
	      var reactiveData = module_default.reactive(data);

	      if (typeof cb == 'function') {
	        module_default.effect(function () {
	          cb(reactiveData);
	        });
	      }

	      return reactiveData;
	    }
	  }, {
	    key: "handleJsonResponse",
	    value: function handleJsonResponse(response, onSuccess, onError) {
	      var errMessage = 'Unexpected error happen, please relogin ro refresh this page';

	      if (typeof onError == 'string') {
	        errMessage = onError;
	      }

	      if (response.errCode == 0) {
	        if (typeof onSuccess == 'function') {
	          onSuccess(response.data);
	        }
	      } else {
	        if (typeof response.errMessage != 'undefined') {
	          errMessage = response.errMessage;
	        }

	        if (typeof onError == 'function') {
	          onError(errMessage);
	        } else {
	          this.showError(errMessage);
	        }
	      }
	    }
	  }, {
	    key: "showError",
	    value: function showError(errMessage) {
	      this.toast('error', errMessage);
	    }
	  }]);

	  return Cresenity;
	}();

	String.prototype.contains = function (a) {
	  return !!~this.indexOf(a);
	}; // eslint-disable-next-line no-extend-native


	String.prototype.toNumber = function () {
	  var n = parseFloat(this);

	  if (!isNaN(n)) {
	    return n;
	  }

	  return 0;
	};
	window.Cresenity = Cresenity;

	if (!window.cresenity) {
	  window.cresenity = new Cresenity();
	}

	window.document.addEventListener('DOMContentLoaded', function () {
	  window.cresenity.init();
	});

}));
//# sourceMappingURL=cres.js.map

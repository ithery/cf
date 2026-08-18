<?php

use PhpParser\Node;
use PHPStan\Rules\Rule;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Reflection\ParametersAcceptorSelector;

/**
 * Nama relasi yang tidak ada pada `whereHas()`, `with()`, dan kerabatnya.
 *
 * Salah ketik di sini tidak ketahuan sampai baris itu benar-benar dijalankan,
 * dan pemanggilannya banyak: 2.245 tempat di seluruh aplikasi saat diukur
 * 2026-08-18. Karena itu ia yang diporting lebih dulu di antara rule Larastan
 * yang tersisa.
 *
 * Yang diperiksa hanya nama yang nilainya dapat dipastikan PHPStan - termasuk
 * lewat variabel yang isinya jelas beberapa baris di atasnya. Yang datang dari
 * masukan pengguna atau perhitungan dibiarkan: menebaknya melahirkan salah
 * lapor, dan rule yang salah lapor lebih cepat dimatikan orang daripada
 * diperbaiki.
 *
 * @implements Rule<Node\Expr\CallLike>
 */
final class CQC_Phpstan_Service_Rule_RelationExistenceRule implements Rule {
    /**
     * Method yang argumen pertamanya nama relasi.
     *
     * Seluruhnya benar-benar ada di CModel_Trait_QueriesRelationships dan
     * CModel_Query - diperiksa satu per satu, bukan disalin dari Larastan.
     *
     * @var string[]
     */
    const METHOD = [
        'has',
        'orHas',
        'doesntHave',
        'orDoesntHave',
        'whereHas',
        'orWhereHas',
        'whereDoesntHave',
        'orWhereDoesntHave',
        'withWhereHas',
        'whereRelation',
        'orWhereRelation',
        'with',
        'withCount',
        'withSum',
        'withAggregate',
    ];

    /**
     * @var CQC_Phpstan_Service_Rule_ModelRuleHelper
     */
    private $modelRuleHelper;

    /**
     * @var CQC_Phpstan_Service_RelationParserHelper
     */
    private $relationParserHelper;

    public function __construct(
        CQC_Phpstan_Service_Rule_ModelRuleHelper $modelRuleHelper,
        CQC_Phpstan_Service_RelationParserHelper $relationParserHelper
    ) {
        $this->modelRuleHelper = $modelRuleHelper;
        $this->relationParserHelper = $relationParserHelper;
    }

    public function getNodeType(): string {
        return Node\Expr\CallLike::class;
    }

    public function processNode(Node $node, Scope $scope): array {
        if (!$node instanceof MethodCall && !$node instanceof StaticCall) {
            return [];
        }

        if (!$node->name instanceof Node\Identifier) {
            return [];
        }

        if (!in_array($node->name->name, static::METHOD, true)) {
            return [];
        }

        $args = $node->getArgs();
        if (count($args) < 1) {
            return [];
        }

        $modelReflection = $this->modelRuleHelper->findModelReflectionFromType(
            $this->calledOnType($node, $scope)
        );

        if ($modelReflection === null) {
            return [];
        }

        //Kelas induk aplikasi dilewati. Penamaan CF membedakannya dengan jelas:
        //model konkret selalu `{Prefix}Model_{Nama}`, sedangkan yang tanpa garis
        //bawah adalah induknya (`CModel`, `TBModel`, `OHModel`). Tipe itu muncul
        //dari idiom `TBModel::make('Member')` yang me-resolve model dari teks
        //saat runtime - secara statis relasinya tidak mungkin diketahui, dan
        //memeriksanya melaporkan 19 relasi yang ada sebagai hilang (tribelio,
        //2026-08-18).
        if (strpos($modelReflection->getName(), '_') === false) {
            return [];
        }

        $errors = [];
        foreach ($this->relationNameList($scope->getType($args[0]->value)) as $relationName) {
            $error = $this->checkRelation($modelReflection, $relationName, $node, $scope);
            if ($error !== null) {
                $errors[] = $error;
            }
        }

        return $errors;
    }

    /**
     * @param MethodCall|StaticCall $node
     *
     * @return \PHPStan\Type\Type
     */
    private function calledOnType($node, Scope $scope) {
        $calledOn = $node instanceof MethodCall ? $node->var : $node->class;

        if ($calledOn instanceof Node\Name) {
            return new ObjectType($scope->resolveName($calledOn));
        }

        return $scope->getType($calledOn);
    }

    /**
     * Nama relasi yang tertulis sebagai teks tetap pada argumen pertama.
     *
     * Bentuknya bermacam-macam: satu teks, daftar teks, atau array berkunci
     * relasi dengan closure sebagai nilainya - yang terakhir dipakai `with()`.
     *
     * @param \PHPStan\Type\Type $type
     *
     * @return string[]
     */
    private function relationNameList($type) {
        $names = [];

        $arrays = $type->getConstantArrays();

        if (count($arrays) > 0) {
            foreach ($arrays as $array) {
                foreach ($array->getKeyTypes() as $keyType) {
                    foreach ($keyType->getConstantStrings() as $constant) {
                        $names[] = $constant->getValue();
                    }
                }
                foreach ($array->getValueTypes() as $valueType) {
                    foreach ($valueType->getConstantStrings() as $constant) {
                        $names[] = $constant->getValue();
                    }
                }
            }
        } else {
            foreach ($type->getConstantStrings() as $constant) {
                $names[] = $constant->getValue();
            }
        }

        $result = [];
        foreach ($names as $name) {
            //`with('user:id,name')` memilih kolom, dan `withCount('post as total')`
            //memberi alias - keduanya bukan bagian nama relasinya
            $name = trim(explode(':', $name)[0]);
            $name = trim(preg_split('/\s+as\s+/i', $name)[0]);

            if (strlen($name) > 0) {
                $result[] = $name;
            }
        }

        return $result;
    }

    /**
     * @param \PHPStan\Reflection\ClassReflection $modelReflection
     * @param string                              $relationName
     * @param MethodCall|StaticCall               $node
     *
     * @return null|\PHPStan\Rules\RuleError
     */
    private function checkRelation($modelReflection, $relationName, $node, Scope $scope) {
        $currentModel = $modelReflection;

        //relasi bersarang ditulis dengan titik: `with('post.author')`
        foreach (explode('.', $relationName) as $segment) {
            if (strlen($segment) == 0) {
                return null;
            }

            $relationMethod = $this->relationMethod($currentModel, $segment, $scope);

            if ($relationMethod === null) {
                return RuleErrorBuilder::message(sprintf(
                    'Relation %s is not found in %s model.',
                    $relationName,
                    $currentModel->getName()
                ))->identifier('cf.relationNotFound')->line($node->getLine())->build();
            }

            $relatedModel = $this->relationParserHelper->findRelatedModelInRelationMethod($relationMethod);

            //model terkaitnya tidak terbaca dari badan methodnya - segmen
            //berikutnya tidak dapat diperiksa tanpa menebak, jadi berhenti
            if ($relatedModel === null || !$this->reflectionExists($currentModel, $relatedModel)) {
                return null;
            }

            $currentModel = $this->relatedReflection($currentModel, $relatedModel);
            if ($currentModel === null) {
                return null;
            }
        }

        return null;
    }

    /**
     * Method relasi bernama itu, atau null bila bukan relasi.
     *
     * @param \PHPStan\Reflection\ClassReflection $modelReflection
     * @param string                              $name
     *
     * @return null|\PHPStan\Reflection\MethodReflection
     */
    private function relationMethod($modelReflection, $name, Scope $scope) {
        if (!$modelReflection->hasMethod($name)) {
            return null;
        }

        //Sengaja TIDAK menuntut tipe kembaliannya CModel_Relation, berbeda dari
        //Larastan. Di CF relasi lazim dirantai - `belongsTo(...)->withTrashed()`,
        //`belongsToMany(...)->wherePivot(...)` - sehingga tipe kembaliannya
        //bukan Relation lagi, dan `@return` jarang ditulis. Menuntutnya membuat
        //19 relasi yang benar-benar ada dilaporkan hilang saat diukur pada
        //ohayomart 2026-08-18. Yang dijaga rule ini nama yang tidak ada sama
        //sekali; membuktikan sebuah method "bukan relasi" tidak mungkin di sini
        //tanpa menebak.
        return $modelReflection->getMethod($name, $scope);
    }

    /**
     * @param \PHPStan\Reflection\ClassReflection $context
     * @param string                              $className
     *
     * @return bool
     */
    private function reflectionExists($context, $className) {
        return class_exists($className);
    }

    /**
     * @param \PHPStan\Reflection\ClassReflection $context
     * @param string                              $className
     *
     * @return null|\PHPStan\Reflection\ClassReflection
     */
    private function relatedReflection($context, $className) {
        $type = new ObjectType($className);

        return $type->getClassReflection();
    }
}

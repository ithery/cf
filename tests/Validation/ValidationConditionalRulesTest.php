<?php

use PHPUnit\Framework\TestCase;

/**
 * Aturan bersyarat: yang menentukan sebuah medan wajib, terlarang, atau
 * dibuang berdasarkan isi medan lain.
 *
 * Kegagalan di sini yang paling berbahaya, karena arahnya diam-diam melonggar:
 * aturan yang tidak jalan meloloskan data yang seharusnya ditolak, dan tidak
 * ada pesan galat yang bisa dilihat siapa pun.
 */
class ValidationConditionalRulesTest extends TestCase {
    /**
     * @param array $data
     * @param array $rules
     *
     * @return CValidation_Validator
     */
    protected function validator(array $data, array $rules) {
        return CValidation_Factory::instance()->make($data, $rules);
    }

    /**
     * @param array $data
     * @param array $rules
     *
     * @return void
     */
    protected function assertPasses(array $data, array $rules) {
        $v = $this->validator($data, $rules);
        $this->assertTrue($v->passes(), 'seharusnya lolos, tetapi gagal: ' . json_encode($v->errors()->all()));
    }

    /**
     * @param array $data
     * @param array $rules
     *
     * @return void
     */
    protected function assertFails(array $data, array $rules) {
        $this->assertTrue($this->validator($data, $rules)->fails(), 'seharusnya gagal, tetapi lolos');
    }

    public function testRequiredIfDemandsTheFieldOnlyWhenTheOtherMatches() {
        $rules = ['alasan' => 'required_if:status,ditolak'];

        $this->assertFails(['status' => 'ditolak'], $rules);
        $this->assertPasses(['status' => 'ditolak', 'alasan' => 'berkas kurang'], $rules);
        $this->assertPasses(['status' => 'diterima'], $rules);
    }

    public function testRequiredIfAcceptsSeveralMatchingValues() {
        $rules = ['alasan' => 'required_if:status,ditolak,ditunda'];

        $this->assertFails(['status' => 'ditunda'], $rules);
        $this->assertPasses(['status' => 'diterima'], $rules);
    }

    public function testRequiredUnlessIsTheMirrorImage() {
        $rules = ['alasan' => 'required_unless:status,diterima'];

        $this->assertPasses(['status' => 'diterima'], $rules);
        $this->assertFails(['status' => 'ditolak'], $rules);
    }

    public function testRequiredWithDemandsTheFieldWhenAnyOtherIsPresent() {
        $rules = ['kota' => 'required_with:alamat,pos'];

        $this->assertFails(['alamat' => 'Jl. Merdeka'], $rules);
        $this->assertPasses(['alamat' => 'Jl. Merdeka', 'kota' => 'Jakarta'], $rules);
        $this->assertPasses([], $rules);
    }

    public function testRequiredWithAllDemandsTheFieldOnlyWhenEveryOtherIsPresent() {
        $rules = ['kota' => 'required_with_all:alamat,pos'];

        $this->assertPasses(['alamat' => 'Jl. Merdeka'], $rules);
        $this->assertFails(['alamat' => 'Jl. Merdeka', 'pos' => '10110'], $rules);
    }

    public function testRequiredWithoutDemandsTheFieldWhenAnotherIsMissing() {
        $rules = ['telepon' => 'required_without:email'];

        $this->assertFails([], $rules);
        $this->assertPasses(['email' => 'a@contoh.test'], $rules);
        $this->assertPasses(['telepon' => '0812'], $rules);
    }

    /**
     * Pasangan required_without_all dipakai untuk "isi salah satu": keduanya
     * kosong ditolak, salah satu terisi sudah cukup.
     */
    public function testRequiredWithoutAllIsTheOneOfThesePattern() {
        $rules = [
            'telepon' => 'required_without_all:email,whatsapp',
            'email' => 'required_without_all:telepon,whatsapp',
        ];

        $this->assertFails([], $rules);
        $this->assertPasses(['telepon' => '0812'], $rules);
        $this->assertPasses(['email' => 'a@contoh.test'], $rules);
    }

    public function testAnEmptyStringDoesNotCountAsPresent() {
        $this->assertFails(['status' => 'ditolak', 'alasan' => ''], ['alasan' => 'required_if:status,ditolak']);
    }

    public function testRequiredArrayKeysDemandsEachNamedKey() {
        $rules = ['pengaturan' => 'array|required_array_keys:tema,bahasa'];

        $this->assertPasses(['pengaturan' => ['tema' => 'gelap', 'bahasa' => 'id']], $rules);
        $this->assertFails(['pengaturan' => ['tema' => 'gelap']], $rules);
    }

    /**
     * Kebalikan required: medan ini justru tidak boleh terisi ketika syaratnya
     * terpenuhi -- misalnya nomor resi pada pesanan yang belum dikirim.
     */
    public function testProhibitedIfForbidsTheFieldWhenTheOtherMatches() {
        $rules = ['resi' => 'prohibited_if:status,belum_kirim'];

        $this->assertFails(['status' => 'belum_kirim', 'resi' => 'JNE123'], $rules);
        $this->assertPasses(['status' => 'belum_kirim'], $rules);
        $this->assertPasses(['status' => 'dikirim', 'resi' => 'JNE123'], $rules);
    }

    public function testProhibitedUnlessIsTheMirrorImage() {
        $rules = ['resi' => 'prohibited_unless:status,dikirim'];

        $this->assertPasses(['status' => 'dikirim', 'resi' => 'JNE123'], $rules);
        $this->assertFails(['status' => 'belum_kirim', 'resi' => 'JNE123'], $rules);
    }

    public function testProhibitsForbidsTheOtherFieldWhenThisOneIsFilled() {
        $rules = ['tunai' => 'prohibits:kartu'];

        $this->assertFails(['tunai' => 100, 'kartu' => '4111'], $rules);
        $this->assertPasses(['tunai' => 100], $rules);
        $this->assertPasses(['kartu' => '4111'], $rules);
    }

    public function testProhibitedAlwaysForbidsTheField() {
        $this->assertFails(['medan' => 'apa pun'], ['medan' => 'prohibited']);
        $this->assertPasses([], ['medan' => 'prohibited']);
    }

    /**
     * missing berbeda dari prohibited: yang dipersoalkan keberadaan kuncinya,
     * bukan isinya. Kunci yang ada tetapi kosong tetap dianggap ada.
     */
    public function testMissingDemandsTheKeyItselfBeAbsent() {
        $this->assertPasses([], ['medan' => 'missing']);
        $this->assertFails(['medan' => ''], ['medan' => 'missing']);
    }

    public function testMissingIfOnlyAppliesWhenTheOtherMatches() {
        $rules = ['resi' => 'missing_if:status,belum_kirim'];

        $this->assertFails(['status' => 'belum_kirim', 'resi' => ''], $rules);
        $this->assertPasses(['status' => 'dikirim', 'resi' => ''], $rules);
    }

    public function testMissingUnlessIsTheMirrorImage() {
        $rules = ['resi' => 'missing_unless:status,dikirim'];

        $this->assertPasses(['status' => 'dikirim', 'resi' => ''], $rules);
        $this->assertFails(['status' => 'belum_kirim', 'resi' => ''], $rules);
    }

    public function testMissingWithAppliesWhenAnotherFieldIsPresent() {
        $rules = ['resi' => 'missing_with:dibatalkan'];

        $this->assertFails(['dibatalkan' => 1, 'resi' => 'JNE123'], $rules);
        $this->assertPasses(['resi' => 'JNE123'], $rules);
    }

    public function testAcceptedIfAndDeclinedIfFollowTheSameShape() {
        $this->assertFails(['jenis' => 'baru', 'setuju' => 'no'], ['setuju' => 'accepted_if:jenis,baru']);
        $this->assertPasses(['jenis' => 'baru', 'setuju' => 'yes'], ['setuju' => 'accepted_if:jenis,baru']);
        $this->assertFails(['jenis' => 'baru', 'setuju' => 'yes'], ['setuju' => 'declined_if:jenis,baru']);
        $this->assertPasses(['jenis' => 'baru', 'setuju' => 'no'], ['setuju' => 'declined_if:jenis,baru']);
    }

    /**
     * exclude_if membuang medannya dari hasil alih-alih menolaknya, sehingga
     * data yang tidak relevan tidak ikut tersimpan.
     */
    public function testExcludeIfDropsTheFieldFromTheValidatedResult() {
        $v = $this->validator(
            ['jenis' => 'perorangan', 'npwp' => '123'],
            ['jenis' => 'required', 'npwp' => 'exclude_if:jenis,perorangan|required']
        );

        $this->assertTrue($v->passes());
        $this->assertArrayNotHasKey('npwp', $v->validated());
    }

    public function testExcludeUnlessKeepsTheFieldWhenTheConditionMatches() {
        $v = $this->validator(
            ['jenis' => 'perusahaan', 'npwp' => '123'],
            ['jenis' => 'required', 'npwp' => 'exclude_unless:jenis,perusahaan|required']
        );

        $this->assertTrue($v->passes());
        $this->assertSame('123', $v->validated()['npwp']);
    }

    public function testExcludeAlwaysDropsTheField() {
        $v = $this->validator(
            ['nama' => 'Hery', 'internal' => 'rahasia'],
            ['nama' => 'required', 'internal' => 'exclude']
        );

        $this->assertTrue($v->passes());
        $this->assertSame(['nama' => 'Hery'], $v->validated());
    }

    /**
     * Objek RequiredIf menerima closure, sehingga syaratnya boleh berupa apa
     * pun yang tidak dapat dituliskan sebagai string aturan.
     */
    public function testTheRequiredIfObjectAcceptsAClosure() {
        $wajib = new CValidation_Rule_RequiredIf(function () {
            return true;
        });
        $tidak = new CValidation_Rule_RequiredIf(function () {
            return false;
        });

        $this->assertFails([], ['medan' => [$wajib]]);
        $this->assertPasses([], ['medan' => [$tidak]]);
    }

    public function testTheProhibitedIfObjectAcceptsAPlainBoolean() {
        $this->assertFails(['medan' => 'isi'], ['medan' => [new CValidation_Rule_ProhibitedIf(true)]]);
        $this->assertPasses(['medan' => 'isi'], ['medan' => [new CValidation_Rule_ProhibitedIf(false)]]);
    }

    /**
     * Objeknya menjadi string aturan biasa saat dipakai: 'required' bila
     * syaratnya terpenuhi, dan string kosong -- yang berarti tanpa aturan --
     * bila tidak.
     */
    public function testTheRequiredIfObjectRendersItselfAsARuleString() {
        $this->assertSame('required', (string) new CValidation_Rule_RequiredIf(true));
        $this->assertSame('', (string) new CValidation_Rule_RequiredIf(false));
        $this->assertSame('required', (string) new CValidation_Rule_RequiredIf(function () {
            return true;
        }));
    }

    /**
     * Nama fungsi berupa string ditolak mentah-mentah. Kalau diterima, sebuah
     * nilai dari luar yang kebetulan bernama sama dengan fungsi PHP akan ikut
     * terpanggil saat aturan dirender.
     */
    public function testTheRequiredIfObjectRefusesAStringCondition() {
        $this->expectException(InvalidArgumentException::class);

        new CValidation_Rule_RequiredIf('phpinfo');
    }

    public function testTheProhibitedIfObjectRendersItselfAsARuleString() {
        $this->assertSame('prohibited', (string) new CValidation_Rule_ProhibitedIf(true));
        $this->assertSame('', (string) new CValidation_Rule_ProhibitedIf(false));
    }

    public function testTheProhibitedIfObjectAlsoRefusesAStringCondition() {
        $this->expectException(InvalidArgumentException::class);

        new CValidation_Rule_ProhibitedIf('phpinfo');
    }

    /**
     * Aturan berupa objek boleh dioper sendirian, bukan hanya di dalam larik.
     */
    public function testTheRuleObjectCanBeGivenOnItsOwn() {
        $this->assertFails([], ['medan' => new CValidation_Rule_RequiredIf(true)]);
        $this->assertPasses([], ['medan' => new CValidation_Rule_RequiredIf(false)]);
    }
}

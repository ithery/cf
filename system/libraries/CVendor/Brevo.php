<?php

class CVendor_Brevo {
    /**
     * @param array $options
     *
     * @return CVendor_Brevo_TransactionalEmail
     */
    public static function transactionalEmail($options = []) {
        //Sengaja memeriksa isinya, bukan keberadaan kuncinya. Pemanggil yang
        //meneruskan apiKey bernilai null - misalnya dari konfigurasi yang
        //belum diisi - akan mematikan fallback ini kalau dipakai carr::get
        //dengan nilai bawaan.
        $apiKey = carr::get($options, 'apiKey');

        if (strlen((string) $apiKey) === 0) {
            $apiKey = CF::config('vendor.brevo.api_key');
        }

        return new CVendor_Brevo_TransactionalEmail($apiKey);
    }
}

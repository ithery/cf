<div id="user">
    <div class="page-header">
        <h1>user</h1>
    </div>

    <p>fungsi sebagai footer table dan informasi pada table tersebut. tambahkan fungsi <span class="badge">add_footer_field()</span></p>

    <h2>Example</h2>
    <h4>Fungsi user</h4>
    <pre class="prettyprint linenums">
public function user() {
    if ($this->_user == null) {
        $session = Session::instance();
        $user = $session->get("user");
        if (!$user)
            $user = null;
        $this->_user = $user;
    }
    return $this->_user;
}</pre>
    <h4>Penggunaan</h4>
    <pre class="prettyprint linenums">$user = $app->user();</pre>
</div>
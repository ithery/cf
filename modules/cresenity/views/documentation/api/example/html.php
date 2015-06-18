	<div class="row-fluid">
		<div class="span3 bs-docs-sidebar">
			
			<ul id="ul-sidenav" class="nav nav-list bs-docs-sidenav">
				<li><a href="#ping"><i class="icon-chevron-right"></i> ping</a></li>
				<li><a href="#check_api_key"><i class="icon-chevron-right"></i> check_api_key</a></li>
				<li><a href="#check_code_version"><i class="icon-chevron-right"></i> check_code_version</a></li>
				<li><a href="#check_db_version"><i class="icon-chevron-right"></i> check_db_version</a></li>

			</ul>
			&nbsp;
		</div>
		<div id="docs-container" class="span9">
			<div id="ping">
				<div class="page-header">
				<h1>ping</h1>
				</div>

				<h2>Example</h2>
				<p>Ping to API Server, Check Up/Down server</p>
				
				<h4>Request</h4>
				<pre class="prettyprint linenums">
{
	"api_key": "14a41ddfd1adbeee7ad213aa832638d7", 
	"data": "" 
}
				</pre>
				<h4>Response</h4>
				<pre class="prettyprint linenums">
{
	"result": 1,
	"data": ""	
}
				</pre>
			 
			</div>
			<div id="check_api_key">
				<div class="page-header">
				<h1>check_api_key</h1>
				</div>

				<h2>Example</h2>
				<p>Validation API Key</p>
				
				<h4>Request</h4>
				<pre class="prettyprint linenums">
{
	"api_key": "14a41ddfd1adbeee7ad213aa832638d7", 
	"data": "" 
}
				</pre>
				<h4>Response</h4>
				<pre class="prettyprint linenums">
{
	"result": 1,
	"data": ""		
}
				</pre>
			</div>
			<div id="check_code_version">
				<div class="page-header">
				<h1>check_code_version</h1>
				</div>

				<h2>Example</h2>
				<p>Get latest code version from server </p>
				
				<h4>Request</h4>
				<pre class="prettyprint linenums">
{
	"api_key": "14a41ddfd1adbeee7ad213aa832638d7", 
	"data": "" 
}
				</pre>
				<h4>Response</h4>
				<pre class="prettyprint linenums">
{
	"result": 1,
	"data": {
		"code_version": "1.0.0",
		"download_link": "\/download\/client\/1.0.0.zip"
	}
}
				</pre>
			</div>
			<div id="check_db_version">
				<div class="page-header">
				<h1>check_db_version</h1>
				</div>

				<h2>Example</h2>
				<p>Get latest DB version from server </p>
				
				<h4>Request</h4>
				<pre class="prettyprint linenums">
{
	"api_key": "14a41ddfd1adbeee7ad213aa832638d7", 
	"data": "" 
}
				</pre>
				<h4>Response</h4>
				<pre class="prettyprint linenums">
{
	"result": 1,
	"data": {
		"db_version": "1.1.0"
	}
}
				</pre>
			</div>
		</div>
	</div>
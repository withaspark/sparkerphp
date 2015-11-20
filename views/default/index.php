<h1>SparkerPHP Framework</h1>

<p>SparkerPHP is a tiny PHP framework for quickly prototyping MVC like
applications.</p>

<p>For more information, visit
<a href='http://withaspark.com/sparkerphp/'>http://withaspark.com/sparkerphp/</a>.
</p>

<h2>Test</h2>

<p>Input a valid hexadecimal ID and URL, or don't.
</p>
<form method="POST" action="{$approot}default/edit" enctype="multipart/form-data">
	<input type="text" name="links--id" placeholder="Link ID" value="{$links--id}">
	<input type="text" name="links--link" placeholder="Link URL" value="{$links--link}">
	<input type="file" name="links--file[]" placeholder="File path" value="">
	<input type="file" name="links--file[]" placeholder="File path" value="">
	<input type="submit" value="Submit!">
</form>

<hr>

<h2>Examples</h2>

<h3>User Input Sanitization</h3>

<p>SparkerPHP attempts to ensure only sanitized inputs are ever used by the
application. To do so, the $_GET, $_POST, $_SESSION, $_COOKIE, and $_FILES
globals are unset automatically and must be retrieved through the router.
</p>

<h4>Defining a Schema</h4>
<p>The application data schema is defined using the JSON schema definition file
located at
</p>
<code>/config/schema.json</code>

<h4>Fetching Inputs</h4>
<p>In the router class,
the globals
</p>
<code>$_POST['somepost'];
$_GET['someget'];
$_SESSION['somesession'];
$_COOKIE['somecookie'];
$_FILES['somefile'];
</code>
<p>can be read through:
</p>
<code>$this-&gt;inputs-&gt;post('somepost');
$this-&gt;inputs-&gt;get('someget');
$this-&gt;inputs-&gt;session('somesession');
$this-&gt;inputs-&gt;cookie('somecookie');
$this-&gt;inputs-&gt;file('somefile');
</code>

<h4>Input Validity Test</h4>
<p>To test that the data input was valid, the validity can be recovered and
used to determine flow of logic.
</p>
<code>$this-&gt;inputs-&gt;isClean('post', 'somepost');
</code>

<h4>Input Error Feedback</h4>
<p>In the event the value is invalid, the error message can be recovered and
used to give the user feedback regarding the failure.
</p>
<code>$this-&gt;inputs-&gt;getError('post', 'somepost');
</code>



<h3>Using Templates</h3>

<p>SparkerPHP uses a very simple templating system where tags can be added to views
and defined in controls. You may either use template tags or raw PHP variables in
your views.</p>

<p>First, define the variable/tag in your router method, preload, postload, or
constructor.</p>
<code>public function index() {
	$this-&gt;addData('world', 'hello world');
}
</code>

<h4>Tags</h4>

<p>The tag can then be used in the view.
</p>

<code>hello &#123;$world&#125;
</code>

<p>
And you would get the result
</p>

<code>hello {$world}
</code>

<h4>Variables</h4>

<p>However, this only works for simple types (strings, integers, floats, etc.).
Alternatively, you could simply use the PHP variable in the view. (This is the
way to work with complex types like arrays and objects.) 
</p>

<p>For instance, if we needed to iterate over an array.
</p>

<code>public function index() {
	$this-&gt;addData('myarr', array(1, 2, 3, 4, 5);
}
</code>

<p>The variable can then be used in the view.
</p>

<code>&lt;?php foreach ($myarr as $el) echo $el; ?&gt;
</code>

<p>
And you would get the result
</p>

<code><?php foreach ($myarr as $el) echo $el; ?>
</code>

<hr>

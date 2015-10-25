<h1>SparkerPHP Framework</h1>

<p>SparkerPHP is a tiny PHP framework for quickly prototyping MVC like
applications.</p>

<p>For more information, visit
<a href='http://withaspark.com/sparkerphp/'>http://withaspark.com/sparkerphp/</a>.
</p>

<h2>Examples</h2>

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

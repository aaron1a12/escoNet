<?php
function code_print($code){
    $code = substr(str_replace('<', '&lt;', $code), 1);
    echo $code;
}


class customPage extends page {
    public $title = 'Javascript';
    function content() {
?>
<h1>Cascading Style Sheets (CSS)</h1>
<p><b>C</b>ascading <b>S</b>tyle <b>S</b>heets is the name of the language used to define the way HTML pages look. <b>CSS</b> can be loaded on an HTML page with 3 methods:</p>
<ul>
    <li>Inline</li>
    <li>External .css File</li>
    <li>Via Scripting</li>
</ul>

<h2>Inline</h2>
<p>Inline CSS can be applied directly to any visible HTML tag with the "style" attribute. Within "style" we apply raw CSS properties separated by semicolons. Look at the following code:</p>


<pre class="brush: xml;"><?php
code_print('
<span style="color: blue;">Hello World!</span>
'); ?></pre>




<?php
    }
}

new customPage();
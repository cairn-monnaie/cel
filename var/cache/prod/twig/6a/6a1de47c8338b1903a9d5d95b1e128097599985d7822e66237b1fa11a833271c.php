<?php

/* TwigBundle:Exception:traces_text.html.twig */
class __TwigTemplate_1430157d9135a97a277182ded140b14c088308293e91be3cc5a826efdebb5021 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<table class=\"trace trace-as-text\">
    <thead class=\"trace-head\">
        <tr>
            <th class=\"sf-toggle\" data-toggle-selector=\"#trace-text-";
        // line 4
        echo twig_escape_filter($this->env, ($context["index"] ?? null), "html", null, true);
        echo "\" data-toggle-initial=\"";
        echo (((1 == ($context["index"] ?? null))) ? ("display") : (""));
        echo "\">
                <h3 class=\"trace-class\">
                    ";
        // line 6
        if ((($context["num_exceptions"] ?? null) > 1)) {
            // line 7
            echo "                        <span class=\"text-muted\">[";
            echo twig_escape_filter($this->env, ((($context["num_exceptions"] ?? null) - ($context["index"] ?? null)) + 1), "html", null, true);
            echo "/";
            echo twig_escape_filter($this->env, ($context["num_exceptions"] ?? null), "html", null, true);
            echo "]</span>
                    ";
        }
        // line 9
        echo "                    ";
        echo twig_escape_filter($this->env, twig_last($this->env, twig_split_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["exception"] ?? null), "class", array()), "\\")), "html", null, true);
        echo "
                    <span class=\"icon icon-close\">";
        // line 10
        echo twig_include($this->env, $context, "@Twig/images/icon-minus-square-o.svg");
        echo "</span>
                    <span class=\"icon icon-open\">";
        // line 11
        echo twig_include($this->env, $context, "@Twig/images/icon-plus-square-o.svg");
        echo "</span>
                </h3>
            </th>
        </tr>
    </thead>

    <tbody id=\"trace-text-";
        // line 17
        echo twig_escape_filter($this->env, ($context["index"] ?? null), "html", null, true);
        echo "\">
        <tr>
            <td>
                ";
        // line 20
        echo twig_include($this->env, $context, "@Twig/Exception/traces.txt.twig", array("exception" => ($context["exception"] ?? null)), false);
        echo "
            </td>
        </tr>
    </tbody>
</table>
";
    }

    public function getTemplateName()
    {
        return "TwigBundle:Exception:traces_text.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  69 => 20,  63 => 17,  54 => 11,  50 => 10,  45 => 9,  37 => 7,  35 => 6,  28 => 4,  23 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "TwigBundle:Exception:traces_text.html.twig", "/var/www/Symfony/CairnB2B/vendor/symfony/symfony/src/Symfony/Bundle/TwigBundle/Resources/views/Exception/traces_text.html.twig");
    }
}

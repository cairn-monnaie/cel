<?php

/* TwigBundle:Exception:trace.html.twig */
class __TwigTemplate_7d06e1f3cc590c7c5816bd8226fcce79b4d1c72fee2024d9055ad8a8472a1bc2 extends Twig_Template
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
        echo "<div class=\"trace-line-header break-long-words ";
        echo ((((twig_get_attribute($this->env, $this->source, ($context["trace"] ?? null), "file", array(), "any", true, true)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, ($context["trace"] ?? null), "file", array()), false)) : (false))) ? ("sf-toggle") : (""));
        echo "\" data-toggle-selector=\"#trace-html-";
        echo twig_escape_filter($this->env, ($context["prefix"] ?? null), "html", null, true);
        echo "-";
        echo twig_escape_filter($this->env, ($context["i"] ?? null), "html", null, true);
        echo "\" data-toggle-initial=\"";
        echo ((($context["_display_code_snippet"] ?? null)) ? ("display") : (""));
        echo "\">
    ";
        // line 2
        if (((twig_get_attribute($this->env, $this->source, ($context["trace"] ?? null), "file", array(), "any", true, true)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, ($context["trace"] ?? null), "file", array()), false)) : (false))) {
            // line 3
            echo "        <span class=\"icon icon-close\">";
            echo twig_include($this->env, $context, "@Twig/images/icon-minus-square.svg");
            echo "</span>
        <span class=\"icon icon-open\">";
            // line 4
            echo twig_include($this->env, $context, "@Twig/images/icon-plus-square.svg");
            echo "</span>
    ";
        }
        // line 6
        echo "
    ";
        // line 7
        if (twig_get_attribute($this->env, $this->source, ($context["trace"] ?? null), "function", array())) {
            // line 8
            echo "        <span class=\"trace-class\">";
            echo $this->extensions['Symfony\Bridge\Twig\Extension\CodeExtension']->abbrClass(twig_get_attribute($this->env, $this->source, ($context["trace"] ?? null), "class", array()));
            echo "</span>";
            if ( !twig_test_empty(twig_get_attribute($this->env, $this->source, ($context["trace"] ?? null), "type", array()))) {
                echo "<span class=\"trace-type\">";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["trace"] ?? null), "type", array()), "html", null, true);
                echo "</span>";
            }
            echo "<span class=\"trace-method\">";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["trace"] ?? null), "function", array()), "html", null, true);
            echo "</span><span class=\"trace-arguments\">(";
            echo $this->extensions['Symfony\Bridge\Twig\Extension\CodeExtension']->formatArgs(twig_get_attribute($this->env, $this->source, ($context["trace"] ?? null), "args", array()));
            echo ")</span>
    ";
        }
        // line 10
        echo "
    ";
        // line 11
        if (((twig_get_attribute($this->env, $this->source, ($context["trace"] ?? null), "file", array(), "any", true, true)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, ($context["trace"] ?? null), "file", array()), false)) : (false))) {
            // line 12
            echo "        ";
            $context["line_number"] = ((twig_get_attribute($this->env, $this->source, ($context["trace"] ?? null), "line", array(), "any", true, true)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, ($context["trace"] ?? null), "line", array()), 1)) : (1));
            // line 13
            echo "        ";
            $context["file_link"] = $this->extensions['Symfony\Bridge\Twig\Extension\CodeExtension']->getFileLink(twig_get_attribute($this->env, $this->source, ($context["trace"] ?? null), "file", array()), ($context["line_number"] ?? null));
            // line 14
            echo "        ";
            $context["file_path"] = twig_replace_filter(strip_tags($this->extensions['Symfony\Bridge\Twig\Extension\CodeExtension']->formatFile(twig_get_attribute($this->env, $this->source, ($context["trace"] ?? null), "file", array()), ($context["line_number"] ?? null))), array((" at line " . ($context["line_number"] ?? null)) => ""));
            // line 15
            echo "        ";
            $context["file_path_parts"] = twig_split_filter($this->env, ($context["file_path"] ?? null), twig_constant("DIRECTORY_SEPARATOR"));
            // line 16
            echo "
        <span class=\"block trace-file-path\">
            in
            <a href=\"";
            // line 19
            echo twig_escape_filter($this->env, ($context["file_link"] ?? null), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, twig_join_filter(twig_slice($this->env, ($context["file_path_parts"] ?? null), 0,  -1), twig_constant("DIRECTORY_SEPARATOR")), "html", null, true);
            echo twig_escape_filter($this->env, twig_constant("DIRECTORY_SEPARATOR"), "html", null, true);
            echo "<strong>";
            echo twig_escape_filter($this->env, twig_last($this->env, ($context["file_path_parts"] ?? null)), "html", null, true);
            echo "</strong></a>
            (line ";
            // line 20
            echo twig_escape_filter($this->env, ($context["line_number"] ?? null), "html", null, true);
            echo ")
        </span>
    ";
        }
        // line 23
        echo "</div>
";
        // line 24
        if (((twig_get_attribute($this->env, $this->source, ($context["trace"] ?? null), "file", array(), "any", true, true)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, ($context["trace"] ?? null), "file", array()), false)) : (false))) {
            // line 25
            echo "    <div id=\"trace-html-";
            echo twig_escape_filter($this->env, ((($context["prefix"] ?? null) . "-") . ($context["i"] ?? null)), "html", null, true);
            echo "\" class=\"trace-code sf-toggle-content\">
        ";
            // line 26
            echo twig_replace_filter($this->extensions['Symfony\Bridge\Twig\Extension\CodeExtension']->fileExcerpt(twig_get_attribute($this->env, $this->source, ($context["trace"] ?? null), "file", array()), twig_get_attribute($this->env, $this->source, ($context["trace"] ?? null), "line", array()), 5), array("#DD0000" => "#183691", "#007700" => "#a71d5d", "#0000BB" => "#222222", "#FF8000" => "#969896"));
            // line 31
            echo "
    </div>
";
        }
    }

    public function getTemplateName()
    {
        return "TwigBundle:Exception:trace.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  116 => 31,  114 => 26,  109 => 25,  107 => 24,  104 => 23,  98 => 20,  89 => 19,  84 => 16,  81 => 15,  78 => 14,  75 => 13,  72 => 12,  70 => 11,  67 => 10,  51 => 8,  49 => 7,  46 => 6,  41 => 4,  36 => 3,  34 => 2,  23 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "TwigBundle:Exception:trace.html.twig", "/var/www/Symfony/CairnB2B/vendor/symfony/symfony/src/Symfony/Bundle/TwigBundle/Resources/views/Exception/trace.html.twig");
    }
}

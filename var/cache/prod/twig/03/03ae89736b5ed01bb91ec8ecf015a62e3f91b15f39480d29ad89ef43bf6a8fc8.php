<?php

/* TwigBundle:Exception:logs.html.twig */
class __TwigTemplate_1f4b98035e65335649f8edad6fbf34300b31bcece1a5feace982dea5c7ae7ceb extends Twig_Template
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
        $context["channel_is_defined"] = twig_get_attribute($this->env, $this->source, twig_first($this->env, ($context["logs"] ?? null)), "channel", array(), "any", true, true);
        // line 2
        echo "
<table class=\"logs\">
    <thead>
        <tr>
            <th>Level</th>
            ";
        // line 7
        if (($context["channel_is_defined"] ?? null)) {
            echo "<th>Channel</th>";
        }
        // line 8
        echo "            <th class=\"full-width\">Message</th>
        </tr>
    </thead>

    <tbody>
    ";
        // line 13
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["logs"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["log"]) {
            // line 14
            echo "        ";
            if ((twig_get_attribute($this->env, $this->source, $context["log"], "priority", array()) >= 400)) {
                // line 15
                echo "            ";
                $context["status"] = "error";
                // line 16
                echo "        ";
            } elseif ((twig_get_attribute($this->env, $this->source, $context["log"], "priority", array()) >= 300)) {
                // line 17
                echo "            ";
                $context["status"] = "warning";
                // line 18
                echo "        ";
            } else {
                // line 19
                echo "            ";
                $context["severity"] = ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["log"], "context", array(), "any", false, true), "exception", array(), "any", false, true), "severity", array(), "any", true, true)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["log"], "context", array(), "any", false, true), "exception", array(), "any", false, true), "severity", array()), false)) : (false));
                // line 20
                echo "            ";
                $context["status"] = ((((($context["severity"] ?? null) === constant("E_DEPRECATED")) || (($context["severity"] ?? null) === constant("E_USER_DEPRECATED")))) ? ("warning") : ("normal"));
                // line 21
                echo "        ";
            }
            // line 22
            echo "        <tr class=\"status-";
            echo twig_escape_filter($this->env, ($context["status"] ?? null), "html", null, true);
            echo "\">
            <td class=\"text-small\" nowrap>
                <span class=\"colored text-bold\">";
            // line 24
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["log"], "priorityName", array()), "html", null, true);
            echo "</span>
                <span class=\"text-muted newline\">";
            // line 25
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, $context["log"], "timestamp", array()), "H:i:s"), "html", null, true);
            echo "</span>
            </td>
            ";
            // line 27
            if (($context["channel_is_defined"] ?? null)) {
                // line 28
                echo "                <td class=\"text-small text-bold nowrap\">
                    ";
                // line 29
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["log"], "channel", array()), "html", null, true);
                echo "
                </td>
            ";
            }
            // line 32
            echo "            <td>";
            echo $this->extensions['Symfony\Bridge\Twig\Extension\CodeExtension']->formatLogMessage(twig_get_attribute($this->env, $this->source, $context["log"], "message", array()), twig_get_attribute($this->env, $this->source, $context["log"], "context", array()));
            echo "</td>
        </tr>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['log'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 35
        echo "    </tbody>
</table>
";
    }

    public function getTemplateName()
    {
        return "TwigBundle:Exception:logs.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  107 => 35,  97 => 32,  91 => 29,  88 => 28,  86 => 27,  81 => 25,  77 => 24,  71 => 22,  68 => 21,  65 => 20,  62 => 19,  59 => 18,  56 => 17,  53 => 16,  50 => 15,  47 => 14,  43 => 13,  36 => 8,  32 => 7,  25 => 2,  23 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "TwigBundle:Exception:logs.html.twig", "/var/www/Symfony/CairnB2B/vendor/symfony/symfony/src/Symfony/Bundle/TwigBundle/Resources/views/Exception/logs.html.twig");
    }
}

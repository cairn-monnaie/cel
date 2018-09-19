<?php

/* TwigBundle:Exception:exception.txt.twig */
class __TwigTemplate_159716da4bce0a81e69755256a4aee1138a0fe697e6ef37193565942379c19fd extends Twig_Template
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
        echo "[exception] ";
        echo ((((($context["status_code"] ?? null) . " | ") . ($context["status_text"] ?? null)) . " | ") . twig_get_attribute($this->env, $this->source, ($context["exception"] ?? null), "class", array()));
        echo "
[message] ";
        // line 2
        echo twig_get_attribute($this->env, $this->source, ($context["exception"] ?? null), "message", array());
        echo "
";
        // line 3
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["exception"] ?? null), "toarray", array()));
        foreach ($context['_seq'] as $context["i"] => $context["e"]) {
            // line 4
            echo "[";
            echo ($context["i"] + 1);
            echo "] ";
            echo twig_get_attribute($this->env, $this->source, $context["e"], "class", array());
            echo ": ";
            echo twig_get_attribute($this->env, $this->source, $context["e"], "message", array());
            echo "
";
            // line 5
            echo twig_include($this->env, $context, "@Twig/Exception/traces.txt.twig", array("exception" => $context["e"]), false);
            echo "

";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['i'], $context['e'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    public function getTemplateName()
    {
        return "TwigBundle:Exception:exception.txt.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  45 => 5,  36 => 4,  32 => 3,  28 => 2,  23 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "TwigBundle:Exception:exception.txt.twig", "/var/www/Symfony/CairnB2B/vendor/symfony/symfony/src/Symfony/Bundle/TwigBundle/Resources/views/Exception/exception.txt.twig");
    }
}

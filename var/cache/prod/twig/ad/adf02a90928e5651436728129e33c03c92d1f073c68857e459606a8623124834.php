<?php

/* CairnUserBundle:Banking:operation_confirm.html.twig */
class __TwigTemplate_fc6f31e0253521c29ab8bd41ed5c154cab9e5a3007d368f30edc17edacac0edb extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Banking:operation_confirm.html.twig", 3);
        $this->blocks = array(
            'stylesheets' => array($this, 'block_stylesheets'),
            'body' => array($this, 'block_body'),
            'javascripts' => array($this, 'block_javascripts'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "CairnUserBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_stylesheets($context, array $blocks = array())
    {
        echo " ";
        $this->displayParentBlock("stylesheets", $context, $blocks);
        echo " ";
    }

    // line 6
    public function block_body($context, array $blocks = array())
    {
        // line 7
        echo "        ";
        $this->displayParentBlock("body", $context, $blocks);
        echo "
    <h1>Récapitulatif du paiement en cours </h1>
    
    <li>
        ";
        // line 11
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["operationReview"] ?? null), "from", array(), "any", false, true), "display", array(), "any", true, true)) {
            // line 12
            echo "            ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["operationReview"] ?? null), "from", array()), "display", array()), "html", null, true);
            echo "
        ";
        } else {
            // line 14
            echo "            Association Le Cairn
        ";
        }
        // line 16
        echo "    </li>
    <li>
        ";
        // line 18
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["operationReview"] ?? null), "to", array(), "any", false, true), "display", array(), "any", true, true)) {
            // line 19
            echo "            ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["operationReview"] ?? null), "to", array()), "display", array()), "html", null, true);
            echo "
        ";
        } else {
            // line 21
            echo "            Association Le Cairn
        ";
        }
        // line 23
        echo "    </li>
    <li>";
        // line 24
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["operationReview"] ?? null), "amount", array()), "html", null, true);
        echo "</li>
    
    <div class=\"well\">
      ";
        // line 27
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form');
        echo "
    </div>
";
    }

    // line 31
    public function block_javascripts($context, array $blocks = array())
    {
        // line 32
        echo "
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:operation_confirm.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  100 => 32,  97 => 31,  90 => 27,  84 => 24,  81 => 23,  77 => 21,  71 => 19,  69 => 18,  65 => 16,  61 => 14,  55 => 12,  53 => 11,  45 => 7,  42 => 6,  34 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Banking:operation_confirm.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/operation_confirm.html.twig");
    }
}

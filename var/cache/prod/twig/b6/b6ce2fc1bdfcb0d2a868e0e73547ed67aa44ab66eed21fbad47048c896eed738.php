<?php

/* CairnUserBundle:Banking:view_conversions.html.twig */
class __TwigTemplate_185e053b52b9b42bab81591e331f5e22b3f8f9e4a8d34adf93c735b4248a4fcb extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Banking:view_conversions.html.twig", 3);
        $this->blocks = array(
            'body' => array($this, 'block_body'),
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
    public function block_body($context, array $blocks = array())
    {
        // line 6
        echo "    ";
        $this->displayParentBlock("body", $context, $blocks);
        echo "

<div>
   ";
        // line 50
        echo "
    <table>
    <caption> <span> Vos conversions passées </span> </caption>
    <thead>
        <tr>
            <th> Date d'éxecution </th>
            <th> Compte crédité</th>
            <th> Motif </th> 
            <th> Montant </th>
 
        </tr>
    </thead>

    <tbody>
    ";
        // line 64
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["processedTransactions"] ?? null));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["transaction"]) {
            // line 65
            echo "                <tr>
                    <td> ";
            // line 66
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "date", array()), "html", null, true);
            echo " </td>
                    <td>
                        ";
            // line 68
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["transaction"], "relatedOwner", array(), "any", false, true), "display", array(), "any", true, true)) {
                // line 69
                echo "                            ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["transaction"], "relatedOwner", array()), "display", array()), "html", null, true);
                echo "
                        ";
            } else {
                // line 71
                echo "                            Association Le Cairn
                        ";
            }
            // line 73
            echo "                    </td>
                    <td> ";
            // line 74
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "description", array()), "html", null, true);
            echo " </td>  
                    <td> ";
            // line 75
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "amount", array()), "html", null, true);
            echo " </td>
                </tr>
    ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 78
            echo "        Aucune conversion effectuée 
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['transaction'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 80
        echo "    </tbody>
    </table>

</div>
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:view_conversions.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  105 => 80,  98 => 78,  90 => 75,  86 => 74,  83 => 73,  79 => 71,  73 => 69,  71 => 68,  66 => 66,  63 => 65,  58 => 64,  42 => 50,  35 => 6,  32 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Banking:view_conversions.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/view_conversions.html.twig");
    }
}

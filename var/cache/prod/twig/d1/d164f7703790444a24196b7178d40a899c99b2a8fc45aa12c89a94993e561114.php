<?php

/* CairnUserBundle:Banking:view_reconversions.html.twig */
class __TwigTemplate_3f807c05d62dab580ee2be8e1c3bd4edddca37737c88208f9cacfe9411074fd5 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Banking:view_reconversions.html.twig", 3);
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
    <table>
    <caption> <span> Vos reconversions passées </span> </caption>
    <thead>
        <tr>
            <th> Date d'éxecution </th>
            <th> Compte débité</th>
            <th> Motif </th> 
            <th> Montant </th>
 
        </tr>
    </thead>

    <tbody>
    ";
        // line 22
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["processedTransactions"] ?? null));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["transaction"]) {
            // line 23
            echo "                <tr>
                    <td> ";
            // line 24
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "date", array()), "html", null, true);
            echo " </td>
                    <td>
                        ";
            // line 26
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["transaction"], "relatedOwner", array(), "any", false, true), "display", array(), "any", true, true)) {
                // line 27
                echo "                            ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["transaction"], "relatedOwner", array()), "display", array()), "html", null, true);
                echo "
                        ";
            } else {
                // line 29
                echo "                            Association Le Cairn
                        ";
            }
            // line 31
            echo "                    </td>
                    <td> ";
            // line 32
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "description", array()), "html", null, true);
            echo " </td>  
                    <td> ";
            // line 33
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "amount", array()), "html", null, true);
            echo " </td>
                </tr>
    ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 36
            echo "        Aucune reconversion effectuée 
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['transaction'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 38
        echo "    </tbody>
    </table>

</div>
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:view_reconversions.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  102 => 38,  95 => 36,  87 => 33,  83 => 32,  80 => 31,  76 => 29,  70 => 27,  68 => 26,  63 => 24,  60 => 23,  55 => 22,  35 => 6,  32 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Banking:view_reconversions.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/view_reconversions.html.twig");
    }
}

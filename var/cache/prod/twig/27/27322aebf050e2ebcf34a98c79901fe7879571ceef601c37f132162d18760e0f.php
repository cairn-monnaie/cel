<?php

/* CairnUserBundle:Banking:view_detailed_recurring_transactions.html.twig */
class __TwigTemplate_57f88ed71239f00314e10164e8e199f55fea807a30185f235d3d7c4fc10fc8ea extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Banking:view_detailed_recurring_transactions.html.twig", 3);
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
        <caption> <span> Détail du virement permanent</span> </caption>
        <thead>
            <tr>
                <th> N° </th>
                <th> Montant </th>
                <th> Date d'éxecution</th>
                <th> Etat </th>
                <th> Action </th> 
            </tr>
        </thead>
    
        <tbody>
        ";
        // line 21
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["data"] ?? null), "occurrences", array()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["occurrence"]) {
            // line 22
            echo "                    <tr>
                        <td> ";
            // line 23
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["occurrence"], "number", array()), "html", null, true);
            echo " </td>
                        <td>";
            // line 24
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["occurrence"], "currencyAmount", array()), "amount", array()), "html", null, true);
            echo " </td>
                        <td> ";
            // line 25
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["occurrence"], "date", array()), "html", null, true);
            echo " </td>  
                        <td> ";
            // line 26
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["occurrence"], "status", array()), "html", null, true);
            echo " </td>
                        <td>
                             ";
            // line 28
            if ((twig_get_attribute($this->env, $this->source, $context["occurrence"], "status", array()) == "FAILED")) {
                // line 29
                echo "                                <a href=\"";
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transaction_occurrence_execute", array("id" => twig_get_attribute($this->env, $this->source, $context["occurrence"], "id", array()))), "html", null, true);
                echo "\" >Executer </a>
                             ";
            } else {
                // line 31
                echo "                                <a href=\"";
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transfer_view", array("type" => "recurring", "id" => twig_get_attribute($this->env, $this->source, $context["occurrence"], "id", array()))), "html", null, true);
                echo "\"> Voir le détail </a>
                             ";
            }
            // line 33
            echo "                        </td>
                    </tr>
        ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 36
            echo "            Aucune occurrence executée
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['occurrence'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 38
        echo "        </tbody>
        </table>
    </div>    
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:view_detailed_recurring_transactions.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  107 => 38,  100 => 36,  93 => 33,  87 => 31,  81 => 29,  79 => 28,  74 => 26,  70 => 25,  66 => 24,  62 => 23,  59 => 22,  54 => 21,  35 => 6,  32 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Banking:view_detailed_recurring_transactions.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/view_detailed_recurring_transactions.html.twig");
    }
}

<?php

/* CairnUserBundle:Banking:view_single_transactions.html.twig */
class __TwigTemplate_0da2a16747f6991ed6dcc719c8bc1d78ad91ef84726de9ca58034bfbddfd92bf extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Banking:view_single_transactions.html.twig", 3);
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
";
        // line 12
        echo "<div>
    <table>
    <caption> <span> Vos virements simples à venir </span> </caption>
    <thead>
        <tr>
            <th> Date de demande </th>
            <th> Date d'éxecution </th>
            <th> Bénéficiaire</th>
            <th> Motif </th> 
            <th> Montant </th>
            <th> Etat </th>
            <th> Action </th>
 
        </tr>
    </thead>

    <tbody>
    ";
        // line 29
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["futureInstallments"] ?? null));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["installment"]) {
            // line 30
            echo "                <tr>
                    <td> ";
            // line 31
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["installment"], "scheduledPayment", array()), "date", array()), "html", null, true);
            echo " </td>
                    <td> ";
            // line 32
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["installment"], "dueDate", array()), "html", null, true);
            echo " </td>
                    <td>
                        ";
            // line 34
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["installment"], "scheduledPayment", array(), "any", false, true), "relatedOwner", array(), "any", false, true), "display", array(), "any", true, true)) {
                // line 35
                echo "                            ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["installment"], "scheduledPayment", array()), "relatedOwner", array()), "display", array()), "html", null, true);
                echo "
                        ";
            } else {
                // line 37
                echo "                            Association Le Cairn
                        ";
            }
            // line 39
            echo "                    </td>
                    <td> ";
            // line 40
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["installment"], "scheduledPayment", array()), "description", array()), "html", null, true);
            echo " </td>  
                    <td> ";
            // line 41
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["installment"], "amount", array()), "html", null, true);
            echo " </td>
                    <td> 
                        ";
            // line 43
            if ((twig_get_attribute($this->env, $this->source, $context["installment"], "status", array()) == "SCHEDULED")) {
                echo " 
                            En cours
                        ";
            } elseif ((twig_get_attribute($this->env, $this->source,             // line 45
$context["installment"], "status", array()) == "BLOCKED")) {
                // line 46
                echo "                            Bloqué
                        ";
            }
            // line 48
            echo "                    </td>
                    <td>
                        <a href=\"";
            // line 50
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transfer_view", array("type" => "scheduled.futur", "id" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["installment"], "scheduledPayment", array()), "id", array()))), "html", null, true);
            echo "\"> Voir le détail </a>
                     |
                         <a href=\"";
            // line 52
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transaction_scheduled_changestatus", array("id" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["installment"], "scheduledPayment", array()), "id", array()), "status" => "cancel")), "html", null, true);
            echo "\"> Annuler </a>
                     | 
                    ";
            // line 54
            if ((twig_get_attribute($this->env, $this->source, $context["installment"], "status", array()) == "BLOCKED")) {
                // line 55
                echo "                        <a href=\"";
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transaction_scheduled_changestatus", array("id" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["installment"], "scheduledPayment", array()), "id", array()), "status" => "open")), "html", null, true);
                echo "\"> Débloquer </a>  </td>
                    ";
            } elseif ((twig_get_attribute($this->env, $this->source,             // line 56
$context["installment"], "status", array()) == "SCHEDULED")) {
                // line 57
                echo "                        <a href=\"";
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transaction_scheduled_changestatus", array("id" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["installment"], "scheduledPayment", array()), "id", array()), "status" => "block")), "html", null, true);
                echo "\"> Bloquer </a>  </td>
                    ";
            }
            // line 59
            echo "                </tr>
    ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 61
            echo "        Aucun virement en attente 
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['installment'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 63
        echo "    </tbody>
    </table>

    <table>
    <caption> <span> Vos virements passés </span> </caption>
    <thead>
        <tr>
            <th> Date d'éxecution </th>
            <th> Bénéficiaire</th>
            <th> Motif </th> 
            <th> Montant </th>
            <th> Action </th>

        </tr>
    </thead>

    <tbody>
    ";
        // line 80
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["processedTransactions"] ?? null));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["transaction"]) {
            // line 81
            echo "                <tr>
                    <td> ";
            // line 82
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "date", array()), "html", null, true);
            echo " </td>
                    <td>
                        ";
            // line 84
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["transaction"], "relatedOwner", array(), "any", false, true), "display", array(), "any", true, true)) {
                // line 85
                echo "                            ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["transaction"], "relatedOwner", array()), "display", array()), "html", null, true);
                echo "
                        ";
            } else {
                // line 87
                echo "                            Association Le Cairn
                        ";
            }
            // line 89
            echo "                    </td>
                    <td> ";
            // line 90
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "description", array()), "html", null, true);
            echo " </td>  
                    <td> ";
            // line 91
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "amount", array()), "html", null, true);
            echo " </td>
                    ";
            // line 92
            if (twig_get_attribute($this->env, $this->source, $context["transaction"], "processedInstallments", array(), "any", true, true)) {
                // line 93
                echo "                        <td> <a href=\"";
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transfer_view", array("type" => "scheduled.past", "id" => twig_get_attribute($this->env, $this->source, $context["transaction"], "id", array()))), "html", null, true);
                echo "\"> Voir le détail </a></td>
                    ";
            } else {
                // line 95
                echo "                        <td> <a href=\"";
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transfer_view", array("type" => "simple", "id" => twig_get_attribute($this->env, $this->source, $context["transaction"], "transactionNumber", array()))), "html", null, true);
                echo "\"> Voir le détail </a></td>
                    ";
            }
            // line 97
            echo "                </tr>
    ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 99
            echo "        Aucun virement effectué 
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['transaction'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 101
        echo "    </tbody>
    </table>

</div>
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:view_single_transactions.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  235 => 101,  228 => 99,  222 => 97,  216 => 95,  210 => 93,  208 => 92,  204 => 91,  200 => 90,  197 => 89,  193 => 87,  187 => 85,  185 => 84,  180 => 82,  177 => 81,  172 => 80,  153 => 63,  146 => 61,  140 => 59,  134 => 57,  132 => 56,  127 => 55,  125 => 54,  120 => 52,  115 => 50,  111 => 48,  107 => 46,  105 => 45,  100 => 43,  95 => 41,  91 => 40,  88 => 39,  84 => 37,  78 => 35,  76 => 34,  71 => 32,  67 => 31,  64 => 30,  59 => 29,  40 => 12,  35 => 6,  32 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Banking:view_single_transactions.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/view_single_transactions.html.twig");
    }
}
